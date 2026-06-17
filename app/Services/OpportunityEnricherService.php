<?php
namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Log;

class OpportunityEnricherService
{
    private CommentAnalysisService $commentAnalyzer;
    private FinalScoreService      $finalScorer;

    public function __construct(
        CommentAnalysisService $commentAnalyzer,
        FinalScoreService      $finalScorer,
    ) {
        $this->commentAnalyzer = $commentAnalyzer;
        $this->finalScorer     = $finalScorer;
    }

    public function enrichKeyword(int $keywordId, int $limit = 5): int
    {
        $config = config('replyradar.enrichment');
        if (!($config['enabled'] ?? true)) return 0;

        $minScore = $config['min_total_score'] ?? 50;

        $posts = Post::where('keyword_id', $keywordId)
            ->where('final_score', '>=', $minScore)
            ->where('competition_score', 0)
            ->where('op_engaged', false)
            ->orderByDesc('final_score')
            ->limit($limit)
            ->get();

        if ($posts->isEmpty()) return 0;

        $enriched = 0;

        foreach ($posts as $post) {
            try {
                $result = $this->commentAnalyzer->analyze($post);

                $competitionScore = $result['competition_score'];
                $opEngaged        = $result['op_engaged'];

                $newFinal = $this->finalScorer->calculate(
                    intentScore: $post->intent_score,
                    matchScore: $post->match_score,
                    redditScore: $post->reddit_score,
                    numComments: $post->num_comments,
                    urgencyScore: $post->urgency_score,
                    depthScore: $post->depth_score,
                    postedAt: $post->posted_at,
                    competitionScore: $competitionScore,
                    opEngaged: $opEngaged,
                );

                $post->update([
                    'competition_score' => $competitionScore,
                    'op_engaged'        => $opEngaged,
                    'final_score'       => $newFinal,
                ]);

                $enriched++;

                usleep(500000);

            } catch (\Exception $e) {
                Log::warning("Enrichment failed for post {$post->id}: {$e->getMessage()}");
                continue;
            }
        }

        return $enriched;
    }

    public function enrichTopPosts(int $userId, int $limit = 20): int
    {
        $config = config('replyradar.enrichment');
        if (!($config['enabled'] ?? true)) return 0;

        $minScore = $config['min_total_score'] ?? 50;
        $batchSize = $config['batch_size'] ?? 10;

        $posts = Post::whereHas('keyword.project', fn($q) => $q->where('user_id', $userId))
            ->where('final_score', '>=', $minScore)
            ->where('competition_score', 0)
            ->orderByDesc('final_score')
            ->limit($limit)
            ->get();

        if ($posts->isEmpty()) return 0;

        $enriched = 0;
        $batch = 0;

        foreach ($posts as $post) {
            try {
                $result = $this->commentAnalyzer->analyze($post);

                $newFinal = $this->finalScorer->calculate(
                    intentScore: $post->intent_score,
                    matchScore: $post->match_score,
                    redditScore: $post->reddit_score,
                    numComments: $post->num_comments,
                    urgencyScore: $post->urgency_score,
                    depthScore: $post->depth_score,
                    postedAt: $post->posted_at,
                    competitionScore: $result['competition_score'],
                    opEngaged: $result['op_engaged'],
                );

                $post->update([
                    'competition_score' => $result['competition_score'],
                    'op_engaged'        => $result['op_engaged'],
                    'final_score'       => $newFinal,
                ]);

                $enriched++;
                $batch++;

                if ($batch >= $batchSize) {
                    sleep(2);
                    $batch = 0;
                }

            } catch (\Exception $e) {
                Log::warning("Enrichment failed for post {$post->id}: {$e->getMessage()}");
                continue;
            }
        }

        return $enriched;
    }
}
