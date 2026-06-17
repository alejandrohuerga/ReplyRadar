<?php
namespace App\Console\Commands;

use App\Models\Keyword;
use App\Models\Project;
use App\Services\OpportunityEnricherService;
use App\Services\MastodonFetcherService;
use App\Services\RedditFetcherService;
use App\Services\TwitterFetcherService;
use Illuminate\Console\Command;

class FetchRedditPosts extends Command
{
    protected $signature   = 'fetch:reddit-posts {--keyword=} {--enrich : Also enrich top posts with comment analysis}';
    protected $description = 'Fetch Reddit posts for all active keywords with advanced scoring';

    public function handle(
        RedditFetcherService       $fetcher,
        TwitterFetcherService      $twitterFetcher,
        MastodonFetcherService     $mastodonFetcher,
        OpportunityEnricherService $enricher,
    ): int {
        $query = Keyword::where('is_active', true)
            ->whereHas('project', fn($q) => $q->where('is_active', true));

        if ($keywordId = $this->option('keyword')) {
            $query->where('id', $keywordId);
        }

        $keywords = $query->get();

        if ($keywords->isEmpty()) {
            $this->warn('No active keywords found.');
            return self::SUCCESS;
        }

        $this->info("Processing {$keywords->count()} keywords...");
        $bar   = $this->output->createProgressBar($keywords->count());
        $total = 0;

        foreach ($keywords as $keyword) {
            $project = $keyword->project;
            $user    = $project->user;

            $saved  = $fetcher->fetchForKeyword($keyword);
            $total += $saved;

            if ($user && in_array($user->plan, ['pro', 'business'])) {
                $twitterSaved = $twitterFetcher->fetchForKeyword($keyword);
                $total += $twitterSaved;
            }

            $mastodonSaved = $mastodonFetcher->fetchForKeyword($keyword);
            $total += $mastodonSaved;

            if ($saved > 0 && $this->option('enrich')) {
                $enriched = $enricher->enrichKeyword($keyword->id);
                if ($enriched > 0) {
                    $this->line(" Enriched {$enriched} posts for '{$keyword->term}'");
                }
            }

            $bar->advance();
            sleep(1);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. {$total} new posts saved with intelligent scoring.");

        return self::SUCCESS;
    }
}
