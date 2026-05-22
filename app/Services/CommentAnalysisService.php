<?php
namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommentAnalysisService
{
    private array $competitionSignals;
    private array $opEngagementSignals;

    public function __construct()
    {
        $this->competitionSignals    = config('replyradar.competition_signals', []);
        $this->opEngagementSignals   = config('replyradar.op_engagement_signals', []);
    }

    public function analyze(Post $post): array
    {
        $redditConfig = config('replyradar.reddit');
        $commentsLimit = $redditConfig['comments_per_post'] ?? 30;

        try {
            $postId = $post->external_id;
            $url = "https://www.reddit.com/comments/{$postId}.json";

            $response = Http::withHeaders([
                'User-Agent' => $redditConfig['user_agent'],
            ])->timeout(15)->get($url, [
                'limit' => $commentsLimit,
                'sort'  => 'top',
            ]);

            if (!$response->ok()) {
                return ['competition_score' => 0, 'op_engaged' => false];
            }

            $data = $response->json();
            if (empty($data) || !isset($data[1]['data']['children'])) {
                return ['competition_score' => 0, 'op_engaged' => false];
            }

            $comments = $data[1]['data']['children'];
            $postAuthor = strtolower($post->author ?? '');

            return $this->analyzeComments($comments, $postAuthor);

        } catch (\Exception $e) {
            Log::warning("Comment analysis failed for post {$post->id}: {$e->getMessage()}");
            return ['competition_score' => 0, 'op_engaged' => false];
        }
    }

    private function analyzeComments(array $comments, string $postAuthor): array
    {
        $competitionScore = 0;
        $opEngaged = false;
        $totalComments = 0;
        $solutionComments = 0;

        foreach ($comments as $comment) {
            $data = $comment['data'] ?? [];
            if (empty($data) || isset($data['removed'])) continue;

            $totalComments++;
            $body = strtolower($data['body'] ?? '');
            $author = strtolower($data['author'] ?? '');

            if ($author === $postAuthor && $author !== '') {
                $opEngaged = true;
                $opEngagementSignals = $this->opEngagementSignals;
                foreach ($opEngagementSignals as $phrase => $weight) {
                    if (str_contains($body, $phrase)) {
                        $opEngaged = true;
                        break;
                    }
                }
            }

            $isSolution = false;
            foreach ($this->competitionSignals as $phrase => $weight) {
                if (str_contains($body, $phrase)) {
                    $competitionScore += $weight;
                    $isSolution = true;
                }
            }

            if (preg_match('/https?:\/\/[^\s]+/', $body)) {
                $competitionScore += 5;
            }

            if ($isSolution) {
                $solutionComments++;
            }

            $commentScore = $data['score'] ?? 0;
            if ($commentScore > 5 && $isSolution) {
                $competitionScore += 3;
            }
        }

        $competitionScore = min(100, $competitionScore);

        if ($totalComments > 0) {
            $solutionRatio = $solutionComments / $totalComments;
            if ($solutionRatio > 0.5) {
                $competitionScore = min(100, $competitionScore * 1.3);
            }
        }

        return [
            'competition_score' => round($competitionScore, 1),
            'op_engaged'        => $opEngaged,
        ];
    }
}
