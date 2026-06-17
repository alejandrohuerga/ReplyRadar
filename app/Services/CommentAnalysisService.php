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
            $subreddit = $post->subreddit;
            $slug = $this->slugify($post->title);

            $url = "https://www.reddit.com/r/{$subreddit}/comments/{$postId}/{$slug}/.rss";

            $response = Http::withHeaders([
                'User-Agent' => $redditConfig['user_agent'],
            ])->timeout(15)->get($url, [
                'limit' => $commentsLimit,
                'sort'  => 'top',
            ]);

            if (!$response->ok()) {
                return ['competition_score' => 0, 'op_engaged' => false];
            }

            $comments = $this->parseCommentRss($response->body(), $post->author);

            return $comments;

        } catch (\Exception $e) {
            Log::warning("Comment analysis failed for post {$post->id}: {$e->getMessage()}");
            return ['competition_score' => 0, 'op_engaged' => false];
        }
    }

    private function slugify(string $title): string
    {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
        $slug = trim($slug, '_');
        $slug = substr($slug, 0, 80);
        return $slug;
    }

    private function parseCommentRss(string $xml, string $postAuthor): array
    {
        $competitionScore = 0;
        $opEngaged = false;
        $totalComments = 0;
        $solutionComments = 0;

        try {
            $feed = simplexml_load_string($xml);
            if (!$feed) return ['competition_score' => 0, 'op_engaged' => false];

            foreach ($feed->entry as $i => $entry) {
                if ($i === 0) continue;

                $totalComments++;
                $authorFull = (string) $entry->author->name;
                $author = ltrim($authorFull, '/u/');
                $bodyHtml = (string) $entry->content;
                $body = strip_tags($bodyHtml);
                $body = html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $bodyLower = mb_strtolower($body);

                if ($author === mb_strtolower($postAuthor) && $author !== '') {
                    foreach ($this->opEngagementSignals as $phrase => $weight) {
                        if (str_contains($bodyLower, $phrase)) {
                            $opEngaged = true;
                            break;
                        }
                    }
                }

                $isSolution = false;
                foreach ($this->competitionSignals as $phrase => $weight) {
                    if (str_contains($bodyLower, $phrase)) {
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
            }
        } catch (\Exception $e) {
            Log::warning("Comment RSS parse error: {$e->getMessage()}");
            return ['competition_score' => 0, 'op_engaged' => false];
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
