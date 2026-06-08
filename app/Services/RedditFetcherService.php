<?php
namespace App\Services;

use App\Models\Keyword;
use App\Models\Post;
use App\Services\ContentTranslationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedditFetcherService
{
    private IntentScoringService      $intentScorer;
    private KeywordMatchingService    $keywordMatcher;
    private FinalScoreService         $finalScorer;
    private ContentTranslationService $translator;

    private array   $seenHashes = [];
    private ?string $accessToken = null;
    private ?Carbon $tokenExpiresAt = null;

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
        $config = config('replyradar.reddit');
        $sortModes  = $config['sort_modes'] ?? ['relevance'];
        $perMode    = $config['per_mode'] ?? 25;
        $timeFilter = $config['time_filter'] ?? 'week';
        $useOAuth   = ($config['oauth']['enabled'] ?? false) === true;

        $this->seenHashes = Post::whereHas('keyword', fn($q) =>
            $q->where('project_id', $keyword->project_id)
        )->pluck('content_hash')->toArray();

        $allPostData = [];

        foreach ($sortModes as $sort) {
            try {
                if ($useOAuth) {
                    $parsed = $this->fetchViaOAuth($keyword->term, $sort, $perMode, $timeFilter, $config);
                } else {
                    $parsed = $this->fetchViaRss($keyword->term, $sort, $perMode, $timeFilter, $config);
                }

                $allPostData = array_merge($allPostData, $parsed);
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

    private function fetchViaOAuth(string $term, string $sort, int $limit, string $time, array $config): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            Log::warning('OAuth token falló, usando RSS como fallback');
            return $this->fetchViaRss($term, $sort, $limit, $time, $config);
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'User-Agent'    => $config['user_agent'],
        ])->timeout(15)->get('https://oauth.reddit.com/search', [
            'q'        => $term,
            'sort'     => $sort,
            'limit'    => $limit,
            't'        => $time,
            'raw_json' => 1,
        ]);

        if (!$response->ok()) {
            Log::warning("Reddit OAuth API failed [{$sort}]: {$response->status()}, usando RSS");
            return $this->fetchViaRss($term, $sort, $limit, $time, $config);
        }

        return $this->parseJson($response->json());
    }

    private function fetchViaRss(string $term, string $sort, int $limit, string $time, array $config): array
    {
        $response = Http::withHeaders([
            'User-Agent' => $config['user_agent'],
        ])->timeout(15)->get('https://www.reddit.com/search.rss', [
            'q'     => $term,
            'sort'  => $sort,
            'limit' => $limit,
            't'     => $time,
        ]);

        if (!$response->ok()) {
            Log::warning("Reddit RSS failed [{$sort}] for '{$term}': {$response->status()}");
            return [];
        }

        return $this->parseRss($response->body());
    }

    private function getAccessToken(): ?string
    {
        if ($this->accessToken && $this->tokenExpiresAt && now()->lt($this->tokenExpiresAt)) {
            return $this->accessToken;
        }

        $oauthConfig = config('replyradar.reddit.oauth');
        $clientId     = $oauthConfig['client_id'] ?? '';
        $clientSecret = $oauthConfig['client_secret'] ?? '';
        $username     = $oauthConfig['username'] ?? '';
        $password     = $oauthConfig['password'] ?? '';
        $tokenUrl     = $oauthConfig['token_url'] ?? 'https://www.reddit.com/api/v1/access_token';
        $userAgent    = config('replyradar.reddit.user_agent', 'ReplyRadar/2.0');

        if (!$clientId || !$clientSecret || !$username || !$password) {
            Log::error('Reddit OAuth: credenciales incompletas');
            return null;
        }

        try {
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->withHeaders(['User-Agent' => $userAgent])
                ->asForm()
                ->timeout(15)
                ->post($tokenUrl, [
                    'grant_type' => 'password',
                    'username'   => $username,
                    'password'   => $password,
                ]);

            if (!$response->ok()) {
                Log::error("Reddit OAuth token error: {$response->status()}");
                return null;
            }

            $data = $response->json();
            $this->accessToken = $data['access_token'] ?? null;
            $expiresIn = $data['expires_in'] ?? 3600;
            $this->tokenExpiresAt = now()->addSeconds(max($expiresIn - 60, 0));

            return $this->accessToken;

        } catch (\Exception $e) {
            Log::error("Reddit OAuth exception: {$e->getMessage()}");
            return null;
        }
    }

    private function parseJson(?array $json): array
    {
        $posts = [];

        if (!$json || !isset($json['data']['children'])) {
            return $posts;
        }

        foreach ($json['data']['children'] as $child) {
            $data = $child['data'] ?? [];

            if (($child['kind'] ?? '') !== 't3') continue;

            $title      = $data['title'] ?? '';
            $externalId = $data['id'] ?? '';

            if (!$externalId || !$title) continue;

            $posts[] = [
                'id'           => $externalId,
                'title'        => $title,
                'selftext'     => $data['selftext'] ?? '',
                'author'       => $data['author'] ?? '',
                'subreddit'    => $data['subreddit'] ?? '',
                'permalink'    => $data['permalink'] ?? '',
                'url'          => $data['url'] ?? '',
                'created_utc'  => $data['created_utc'] ?? null,
                'score'        => (int) ($data['score'] ?? 0),
                'num_comments' => (int) ($data['num_comments'] ?? 0),
                'author_karma' => (int) ($data['link_karma'] ?? 0) + (int) ($data['comment_karma'] ?? 0),
            ];
        }

        return $posts;
    }

    private function parseRss(string $xml): array
    {
        $posts = [];

        try {
            $feed = simplexml_load_string($xml);
            if (!$feed) return [];

            foreach ($feed->entry as $entry) {
                $id = (string) $entry->id;
                $title = (string) $entry->title;

                if (!$id || !$title) continue;

                $externalId = str_replace('t3_', '', $id);

                $contentHtml = (string) $entry->content;
                $content = strip_tags($contentHtml);
                $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $authorFull = (string) $entry->author->name;
                $author = ltrim($authorFull, '/u/');

                $link = (string) $entry->link->attributes()->href;
                preg_match('#/r/([^/]+)/#', $link, $subMatch);
                $subreddit = $subMatch[1] ?? '';

                $published = (string) $entry->published;
                $postedAt = $published ? Carbon::parse($published) : null;

                $posts[] = [
                    'id'           => $externalId,
                    'title'        => $title,
                    'selftext'     => $content,
                    'author'       => $author,
                    'subreddit'    => $subreddit,
                    'permalink'    => parse_url($link, PHP_URL_PATH) ?? '',
                    'url'          => $link,
                    'created_utc'  => $postedAt?->timestamp,
                    'score'        => 0,
                    'num_comments' => 0,
                    'author_karma' => 0,
                ];
            }
        } catch (\Exception $e) {
            Log::warning("RSS parse error: {$e->getMessage()}");
        }

        return $posts;
    }

    private function deduplicateAndProcess(Keyword $keyword, array $posts): array
    {
        $seen = [];
        $unique = [];

        foreach ($posts as $data) {
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

        $author      = $data['author'] ?? '';
        $authorKarma = (int) ($data['author_karma'] ?? 0);

        $matchScore     = $this->keywordMatcher->score($keyword->term, $title, $content);
        $intentScore    = $this->intentScorer->score($title, $content, $matchScore);
        $urgencyScore   = $this->intentScorer->scoreUrgency($title, $content);
        $depthScore     = $this->intentScorer->scoreDepth($title, $content);

        $redditScore    = (int) ($data['score'] ?? 0);
        $numComments    = (int) ($data['num_comments'] ?? 0);
        $postedAt       = isset($data['created_utc'])
                            ? Carbon::createFromTimestamp($data['created_utc'])
                            : null;

        $engagementScore = round($this->calculateEngagement($redditScore, $numComments), 1);
        $freshnessScore  = round($this->calculateFreshness($postedAt), 1);

        $finalScore = $this->finalScorer->calculate(
            intentScore: $intentScore,
            matchScore: $matchScore,
            redditScore: $redditScore,
            numComments: $numComments,
            urgencyScore: $urgencyScore,
            depthScore: $depthScore,
            postedAt: $postedAt,
            competitionScore: 0,
            opEngaged: false,
        );

        $titleEn = $this->translator->translateToEnglish($title);
        $contentEn = $this->translator->translateToEnglish(substr($content, 0, 5000));
        $titleEs = $this->translator->translateToSpanish($title);
        $contentEs = $this->translator->translateToSpanish(substr($content, 0, 5000));

        try {
            Post::create([
                'keyword_id'       => $keyword->id,
                'external_id'      => $externalId,
                'title'            => $title,
                'content'          => substr($content, 0, 5000),
                'title_en'         => ($titleEn !== $title) ? $titleEn : null,
                'content_en'       => ($contentEn !== $content) ? $contentEn : null,
                'title_es'         => ($titleEs !== $title) ? $titleEs : null,
                'content_es'       => ($contentEs !== $content) ? $contentEs : null,
                'subreddit'        => $data['subreddit'] ?? '',
                'url'              => $data['url'] ?? ('https://reddit.com' . ($data['permalink'] ?? '')),
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
