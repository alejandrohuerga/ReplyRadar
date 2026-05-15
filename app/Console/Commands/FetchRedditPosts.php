<?php
namespace App\Console\Commands;

use App\Models\Keyword;
use App\Services\RedditFetcherService;
use Illuminate\Console\Command;

class FetchRedditPosts extends Command
{
    protected $signature   = 'fetch:reddit-posts {--keyword=}';
    protected $description = 'Fetch Reddit posts for all active keywords';

    public function handle(RedditFetcherService $fetcher): int
    {
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
            $saved  = $fetcher->fetchForKeyword($keyword);
            $total += $saved;
            $bar->advance();
            sleep(1); // Respeta rate limit de Reddit
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. {$total} new posts saved.");

        return self::SUCCESS;
    }
}