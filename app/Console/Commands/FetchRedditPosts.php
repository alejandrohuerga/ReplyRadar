<?php
namespace App\Console\Commands;

use App\Models\Keyword;
use App\Services\OpportunityEnricherService;
use App\Services\RedditFetcherService;
use Illuminate\Console\Command;

class FetchRedditPosts extends Command
{
    protected $signature   = 'fetch:reddit-posts {--keyword=} {--enrich : Also enrich top posts with comment analysis}';
    protected $description = 'Fetch Reddit posts for all active keywords with advanced scoring';

    public function handle(
        RedditFetcherService       $fetcher,
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

        $this->info("Processing {$keywords->count()} keywords with multi-sort Reddit search...");
        $bar   = $this->output->createProgressBar($keywords->count());
        $total = 0;

        foreach ($keywords as $keyword) {
            $saved  = $fetcher->fetchForKeyword($keyword);
            $total += $saved;

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
