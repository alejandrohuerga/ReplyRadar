<?php
namespace App\Services;

use App\Models\Keyword;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedditFetcherService
{
    private IntentScoringService  $intentScorer;
    private KeywordMatchingService $keywordMatcher;
    private FinalScoreService      $finalScorer;

    public function __construct(
        IntentScoringService   $intentScorer,
        KeywordMatchingService $keywordMatcher,
        FinalScoreService      $finalScorer
    ) {
        $this->intentScorer   = $intentScorer;
        $this->keywordMatcher  = $keywordMatcher;
        $this->finalScorer     = $finalScorer;
    }

    public function fetchForKeyword(Keyword $keyword): int
    {
        $config = config('replyradar.reddit');
        $url    = "{$config['base_url']}/search.json";

        try {
            $response = Http::withHeaders([
                'User-Agent' => $config['user_agent'],
            ])->get($url, [
                'q'    => $keyword->term,
                'sort' => 'new',
                'limit' => $config['per_keyword'],
                'type' => 'link',
            ]);

            if (!$response->ok()) {
                Log::warning("Reddit fetch failed for keyword {$keyword->term}: {$response->status()}");
                return 0;
            }

            $posts   = $response->json('data.children', []);
            $saved   = 0;

            foreach ($posts as $item) {
                $data = $item['data'] ?? [];
                if (empty($data)) continue;

                $saved += $this->processPost($keyword, $data);
            }

            $keyword->update(['last_fetched_at' => now()]);

            return $saved;

        } catch (\Exception $e) {
            Log::error("Reddit fetch exception: {$e->getMessage()}");
            return 0;
        }
    }

    private function processPost(Keyword $keyword, array $data): int
    {
        $externalId  = $data['id'] ?? null;
        $title       = $data['title'] ?? '';
        $content     = $data['selftext'] ?? '';

        if (!$externalId || !$title) return 0;

        // Deduplicación por hash
        $hash = md5($title . $content);
        if (Post::where('content_hash', $hash)->exists()) return 0;

        // Scoring
        $intentScore = $this->intentScorer->score($title, $content);
        $matchScore  = $this->keywordMatcher->score($keyword->term, $title, $content);
        $redditScore = (int) ($data['score'] ?? 0);
        $numComments = (int) ($data['num_comments'] ?? 0);
        $finalScore  = $this->finalScorer->calculate($intentScore, $matchScore, $redditScore, $numComments);

        Post::create([
            'keyword_id'       => $keyword->id,
            'external_id'      => $externalId,
            'title'            => $title,
            'content'          => substr($content, 0, 2000),
            'subreddit'        => $data['subreddit'] ?? '',
            'url'              => 'https://reddit.com' . ($data['permalink'] ?? ''),
            'author'           => $data['author'] ?? null,
            'reddit_score'     => $redditScore,
            'num_comments'     => $numComments,
            'intent_score'     => $intentScore,
            'match_score'      => $matchScore,
            'engagement_score' => 0,
            'final_score'      => $finalScore,
            'content_hash'     => $hash,
            'posted_at'        => isset($data['created_utc'])
                                    ? \Carbon\Carbon::createFromTimestamp($data['created_utc'])
                                    : null,
        ]);

        return 1;
    }
}
