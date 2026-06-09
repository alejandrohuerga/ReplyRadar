<?php
namespace App\Jobs;

use App\Models\Keyword;
use App\Services\MastodonFetcherService;
use App\Services\RedditFetcherService;
use App\Services\TwitterFetcherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchKeywordPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;

    public function __construct(
        public Keyword $keyword,
    ) {}

    public function handle(
        RedditFetcherService   $redditFetcher,
        MastodonFetcherService $mastodonFetcher,
        TwitterFetcherService  $twitterFetcher,
    ): void {
        $user = $this->keyword->project->user;

        $this->keyword->update(['last_fetched_at' => now()]);

        $mastodonFetcher->fetchForKeyword($this->keyword);

        $redditFetcher->fetchForKeyword($this->keyword);

        if ($user && in_array($user->plan, ['pro', 'business'])) {
            $twitterFetcher->fetchForKeyword($this->keyword);
        }
    }
}
