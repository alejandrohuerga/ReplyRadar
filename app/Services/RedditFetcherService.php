<?php
namespace App\Services;

use App\Models\Keyword;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedditFetcherService
{
    private IntentScoringService   $intentScorer;
    private KeywordMatchingService $keywordMatcher;
    private FinalScoreService      $finalScorer;

    private array $seenHashes = [];

    public function __construct(
        IntentScoringService   $intentScorer,
        KeywordMatchingService $keywordMatcher,
        FinalScoreService      $finalScorer,
    ) {
        $this->intentScorer    = $intentScorer;
        $this->keywordMatcher  = $keywordMatcher;
        $this->finalScorer     = $finalScorer;
    }

    public function fetchForKeyword(Keyword $keyword): int
    {
        $config = config('replyradar.reddit');
        $baseUrl = $config['base_url'];
        $sortModes = $config['sort_modes'] ?? ['relevance'];
        $perMode   = $config['per_mode'] ?? 25;
        $timeFilter = $config['time_filter'] ?? 'week';

        $this->seenHashes = Post::whereHas('keyword', fn($q) =>
            $q->where('project_id', $keyword->project_id)
        )->pluck('content_hash')->toArray();

        $allPostData = [];

        foreach ($sortModes as $sort) {
            try {
                $url = "{$baseUrl}/search.json";

                $response = Http::withHeaders([
                    'User-Agent' => $config['user_agent'],
                ])->timeout(15)->get($url, [
                    'q'      => $keyword->term,
                    'sort'   => $sort,
                    'limit'  => $perMode,
                    'type'   => 'link',
                    't'      => $timeFilter,
                ]);

                if (!$response->ok()) {
                    Log::warning("Reddit fetch failed [{$sort}] for '{$keyword->term}': {$response->status()}");
                    continue;
                }

                $posts = $response->json('data.children', []);
                $allPostData = array_merge($allPostData, $posts);

                usleep(500000);

            } catch (\Exception $e) {
                Log::error("Reddit fetch exception [{$sort}]: {$e->getMessage()}");
                continue;
            }
        }

        $saved = 0;
        $processed = $this->deduplicateAndProcess($keyword, $allPostData);

        foreach ($processed as $data) {
            if ($this->savePost($keyword, $data)) {
                $saved++;
            }
        }

        if ($saved > 0) {
            $keyword->update(['last_fetched_at' => now()]);
        }

        return $saved;
    }

    private function deduplicateAndProcess(Keyword $keyword, array $posts): array
    {
        $seen = [];
        $unique = [];

        foreach ($posts as $item) {
            $data = $item['data'] ?? [];
            if (empty($data)) continue;

            $externalId = $data['id'] ?? null;
            $title      = $data['title'] ?? '';

            if (!$externalId || !$title) continue;

            if (isset($seen[$externalId])) continue;
            $seen[$externalId] = true;

            $unique[] = $data;
        }

        return $unique;
    }

    private function savePost(Keyword $keyword, array $data): bool
    {
        $externalId = $data['id'] ?? '';
        $title      = $data['title'] ?? '';
        $content    = $data['selftext'] ?? '';

        $hash = md5($title . $content);

        if (in_array($hash, $this->seenHashes)) return false;

        $authorKarma = (int) ($data['author_flair_text'] ?? 0);
        $author      = $data['author'] ?? '';

        $intentScore    = $this->intentScorer->score($title, $content);
        $matchScore     = $this->keywordMatcher->score($keyword->term, $title, $content);
        $urgencyScore   = $this->intentScorer->scoreUrgency($title, $content);
        $depthScore     = $this->intentScorer->scoreDepth($title, $content);

        $redditScore    = (int) ($data['score'] ?? 0);
        $numComments    = (int) ($data['num_comments'] ?? 0);
        $postedAt       = isset($data['created_utc'])
                            ? Carbon::createFromTimestamp($data['created_utc'])
                            : null;

        $engagementScore = round($this->calculateEngagement($redditScore, $numComments), 1);
        $freshnessScore  = round($this->calculateFreshness($postedAt), 1);

        $competitionScore = 0;
        $opEngaged        = false;

        $finalScore = $this->finalScorer->calculate(
            intentScore: $intentScore,
            matchScore: $matchScore,
            redditScore: $redditScore,
            numComments: $numComments,
            urgencyScore: $urgencyScore,
            depthScore: $depthScore,
            postedAt: $postedAt,
            competitionScore: $competitionScore,
            opEngaged: $opEngaged,
        );

        try {
            Post::create([
                'keyword_id'       => $keyword->id,
                'external_id'      => $externalId,
                'title'            => $title,
                'content'          => substr($content, 0, 5000),
                'subreddit'        => $data['subreddit'] ?? '',
                'url'              => 'https://reddit.com' . ($data['permalink'] ?? ''),
                'author'           => $author,
                'author_karma'     => $authorKarma,
                'reddit_score'     => $redditScore,
                'num_comments'     => $numComments,
                'intent_score'     => $intentScore,
                'match_score'      => $matchScore,
                'engagement_score' => $engagementScore,
                'urgency_score'    => $urgencyScore,
                'depth_score'      => $depthScore,
                'freshness_score'  => $freshnessScore,
                'competition_score'=> 0,
                'op_engaged'       => false,
                'final_score'      => $finalScore,
                'content_hash'     => $hash,
                'posted_at'        => $postedAt,
            ]);

            $this->seenHashes[] = $hash;
            return true;

        } catch (\Exception $e) {
            Log::warning("Failed to save post '{$title}': {$e->getMessage()}");
            return false;
        }
    }

    private function calculateEngagement(int $score, int $comments): float
    {
        if ($score <= 0 && $comments <= 0) return 0;

        $logScore    = $score > 0    ? log10($score)    * 18 : 0;
        $logComments = $comments > 0 ? log10($comments) * 12 : 0;

        return min(100, round($logScore + $logComments, 1));
    }

    private function calculateFreshness(?Carbon $postedAt): float
    {
        if ($postedAt === null) return 50;

        $hoursAgo = $postedAt->diffInRealHours(now());
        $decayCfg = config('replyradar.time_decay');

        $decayHours = $decayCfg['decay_hours'] ?? 48;
        $maxDecay   = $decayCfg['max_decay'] ?? 0.20;
        $boostHours = $decayCfg['boost_hours'] ?? 6;

        if ($hoursAgo <= $boostHours) {
            $boost = 1 - ($hoursAgo / $boostHours) * 0.3;
            return round(min(100, 100 * $boost), 1);
        }

        $decay = max($maxDecay, 1 - ($hoursAgo / $decayHours));
        return round(min(100, 100 * $decay), 1);
    }
}
