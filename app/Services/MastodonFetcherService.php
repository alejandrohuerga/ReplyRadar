<?php
namespace App\Services;

use App\Models\Keyword;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MastodonFetcherService
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
        if (!config('replyradar.mastodon.enabled')) return 0;

        $this->seenHashes = Post::whereHas('keyword', fn($q) =>
            $q->where('project_id', $keyword->project_id)
        )->pluck('content_hash')->toArray();

        $hashtag = $this->keywordToHashtag($keyword->term);
        $limit = config('replyradar.mastodon.per_keyword', 25);
        $toots = $this->fetchHashtagTimeline($hashtag, $limit);

        if (empty($toots)) {
            Log::info("Mastodon: no toots found for #{$hashtag}");
            return 0;
        }

        $saved = 0;
        foreach ($toots as $toot) {
            if ($this->saveToot($keyword, $toot, $hashtag)) $saved++;
        }

        if ($saved > 0) {
            $keyword->update(['last_fetched_at' => now()]);
        }

        return $saved;
    }

    private function keywordToHashtag(string $keyword): string
    {
        $cleaned = preg_replace('/[^a-zA-Z0-9\s]/', '', $keyword);
        $words = array_filter(explode(' ', $cleaned));
        $camel = implode('', array_map(fn($w) => ucfirst(strtolower(trim($w))), $words));
        return $camel ?: 'Trending';
    }

    private function fetchHashtagTimeline(string $hashtag, int $limit): array
    {
        $instance = config('replyradar.mastodon.instance', 'mastodon.social');
        $perPage = min($limit, 40);

        try {
            $response = Http::timeout(15)
                ->get("https://{$instance}/api/v1/timelines/tag/{$hashtag}", [
                    'limit' => $perPage,
                ]);

            if ($response->failed()) {
                Log::warning("Mastodon timeline failed for #{$hashtag}: {$response->body()}");
                return [];
            }

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::warning("Mastodon timeline exception for #{$hashtag}: {$e->getMessage()}");
            return [];
        }
    }

    private function saveToot(Keyword $keyword, array $data, string $hashtag): bool
    {
        $content = strip_tags($data['content'] ?? '');
        $tootId  = $data['id'] ?? '';

        if (!$content || !$tootId) return false;

        $hash = md5($content . 'mastodon');
        if (in_array($hash, $this->seenHashes)) return false;

        $matchScore     = $this->keywordMatcher->score($keyword->term, $content, '');
        $intentScore    = $this->intentScorer->score($content, '', $matchScore);
        $urgencyScore   = $this->intentScorer->scoreUrgency($content, '');
        $depthScore     = $this->intentScorer->scoreDepth($content, '');

        $favCount    = (int) ($data['favourites_count'] ?? 0);
        $boostCount  = (int) ($data['reblogs_count'] ?? 0);
        $replyCount  = (int) ($data['replies_count'] ?? 0);
        $totalEngagement = $favCount + $boostCount + $replyCount;

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

        $instance = config('replyradar.mastodon.instance', 'mastodon.social');
        $locale = app()->getLocale();
        $titleEn = $locale !== 'en' ? $this->translator->translateToEnglish($content) : null;
        $titleEs = $locale !== 'es' ? $this->translator->translateToSpanish($content) : null;
        $tootUrl = $data['url'] ?? "https://{$instance}/@{$data['account']['username']}/{$tootId}";

        try {
            Post::create([
                'keyword_id'        => $keyword->id,
                'external_id'       => 'ma_' . $tootId,
                'title'             => $content,
                'content'           => null,
                'title_en'          => $titleEn,
                'content_en'        => null,
                'title_es'          => $titleEs,
                'content_es'        => null,
                'subreddit'         => 'mastodon',
                'url'               => $tootUrl,
                'author'            => $data['account']['display_name'] ?? $data['account']['username'] ?? '',
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
                'source'            => 'mastodon',
                'like_count'        => $favCount,
                'retweet_count'     => $boostCount,
                'reply_count'       => $replyCount,
                'author_handle'     => '@' . $data['account']['username'],
                'author_followers'  => $data['account']['followers_count'] ?? 0,
            ]);

            $this->seenHashes[] = $hash;
            return true;

        } catch (\Exception $e) {
            Log::warning("Failed to save toot: {$e->getMessage()}");
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
