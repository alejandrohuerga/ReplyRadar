<?php
namespace App\Services;

use App\Models\Keyword;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwitterFetcherService
{
    private IntentScoringService      $intentScorer;
    private KeywordMatchingService    $keywordMatcher;
    private FinalScoreService         $finalScorer;
    private ContentTranslationService $translator;

    private array $seenHashes = [];

    public function __construct(
        IntentScoringService      $intentScorer,
        KeywordMatchingService    $keywordMatcher,
        FinalScoreService         $finalScorer,
        ContentTranslationService $translator,
    ) {
        $this->intentScorer    = $intentScorer;
        $this->keywordMatcher  = $keywordMatcher;
        $this->finalScorer     = $finalScorer;
        $this->translator      = $translator;
    }

    public function fetchForKeyword(Keyword $keyword): int
    {
        $config = config('replyradar.twitter');
        if (!($config['enabled'] ?? false)) return 0;

        $this->seenHashes = Post::whereHas('keyword', fn($q) =>
            $q->where('project_id', $keyword->project_id)
        )->pluck('content_hash')->toArray();

        $perKeyword = $config['per_keyword'] ?? 25;
        $tweets = $this->searchTweets($keyword->term, $perKeyword);
        if (empty($tweets)) return 0;

        $saved = 0;
        foreach ($tweets as $tweet) {
            if ($this->saveTweet($keyword, $tweet)) $saved++;
        }

        if ($saved > 0) {
            $keyword->update(['last_fetched_at' => now()]);
        }

        return $saved;
    }

    private function searchTweets(string $query, int $maxResults): array
    {
        $bearerToken = config('replyradar.twitter.bearer_token');
        if (!$bearerToken) {
            Log::warning('Twitter Bearer Token not configured');
            return [];
        }

        try {
            $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken,
                ])
                ->timeout(30)
                ->get('https://api.twitter.com/2/tweets/search/recent', [
                    'query'       => $query . ' -is:retweet lang:en',
                    'max_results' => min($maxResults, 100),
                    'tweet.fields' => 'created_at,public_metrics,author_id',
                    'user.fields'  => 'username,name,public_metrics',
                    'expansions'   => 'author_id',
                ]);

            if ($response->failed()) {
                Log::warning("Twitter search failed for '{$query}': {$response->body()}");
                return [];
            }

            $body = $response->json();
            $tweets = $body['data'] ?? [];
            $users = [];
            foreach ($body['includes']['users'] ?? [] as $u) {
                $users[$u['id']] = $u;
            }

            $results = [];
            foreach ($tweets as $t) {
                $author = $users[$t['author_id']] ?? null;
                $metrics = $t['public_metrics'] ?? [];
                $results[] = [
                    'id'              => $t['id'],
                    'text'            => $t['text'],
                    'created_at'      => $t['created_at'] ?? null,
                    'author_id'       => $t['author_id'] ?? '',
                    'author_username' => $author['username'] ?? '',
                    'author_name'     => $author['name'] ?? '',
                    'author_followers' => $author['public_metrics']['followers_count'] ?? 0,
                    'like_count'      => $metrics['like_count'] ?? 0,
                    'retweet_count'   => $metrics['retweet_count'] ?? 0,
                    'reply_count'     => $metrics['reply_count'] ?? 0,
                    'quote_count'     => $metrics['quote_count'] ?? 0,
                ];
            }

            return $results;
        } catch (\Exception $e) {
            Log::warning("Twitter search exception for '{$query}': {$e->getMessage()}");
            return [];
        }
    }

    private function saveTweet(Keyword $keyword, array $data): bool
    {
        $text    = $data['text'] ?? '';
        $tweetId = $data['id'] ?? '';

        if (!$text || !$tweetId) return false;

        $hash = md5($text . 'twitter');

        if (in_array($hash, $this->seenHashes)) return false;

        $matchScore     = $this->keywordMatcher->score($keyword->term, $text, '');
        $intentScore    = $this->intentScorer->score($text, '', $matchScore);
        $urgencyScore   = $this->intentScorer->scoreUrgency($text, '');
        $depthScore     = $this->intentScorer->scoreDepth($text, '');

        $likeCount    = (int) ($data['like_count'] ?? 0);
        $retweetCount = (int) ($data['retweet_count'] ?? 0);
        $replyCount   = (int) ($data['reply_count'] ?? 0);
        $totalEngagement = $likeCount + $retweetCount + $replyCount;

        $engagementScore = round(min(100, log10(max(1, $totalEngagement)) * 25), 1);

        $postedAt = isset($data['created_at'])
            ? Carbon::parse($data['created_at'])
            : null;

        $freshnessScore = round($this->calculateFreshness($postedAt), 1);

        $finalScore = $this->finalScorer->calculate(
            intentScore: $intentScore,
            matchScore: $matchScore,
            redditScore: 0,
            numComments: $replyCount,
            urgencyScore: $urgencyScore,
            depthScore: $depthScore,
            postedAt: $postedAt,
            competitionScore: 0,
            opEngaged: false,
        );

        $locale = app()->getLocale();
        $titleEn = $locale !== 'en' ? $this->translator->translateToEnglish($text) : null;
        $titleEs = $locale !== 'es' ? $this->translator->translateToSpanish($text) : null;
        $tweetUrl = "https://x.com/{$data['author_username']}/status/{$tweetId}";

        try {
            Post::create([
                'keyword_id'        => $keyword->id,
                'external_id'       => 'tw_' . $tweetId,
                'title'             => $text,
                'content'           => null,
                'title_en'          => $titleEn,
                'content_en'        => null,
                'title_es'          => $titleEs,
                'content_es'        => null,
                'subreddit'         => 'twitter',
                'url'               => $tweetUrl,
                'author'            => $data['author_name'] ?? '',
                'author_karma'      => 0,
                'reddit_score'      => 0,
                'num_comments'      => $replyCount,
                'intent_score'      => $intentScore,
                'match_score'       => $matchScore,
                'engagement_score'  => $engagementScore,
                'urgency_score'     => $urgencyScore,
                'depth_score'       => $depthScore,
                'freshness_score'   => $freshnessScore,
                'competition_score' => 0,
                'op_engaged'        => false,
                'final_score'       => $finalScore,
                'content_hash'      => $hash,
                'posted_at'         => $postedAt,
                'source'            => 'twitter',
                'like_count'        => $likeCount,
                'retweet_count'     => $retweetCount,
                'reply_count'       => $replyCount,
                'author_handle'     => '@' . $data['author_username'],
                'author_followers'  => $data['author_followers'] ?? 0,
            ]);

            $this->seenHashes[] = $hash;
            return true;

        } catch (\Exception $e) {
            Log::warning("Failed to save tweet: {$e->getMessage()}");
            return false;
        }
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
