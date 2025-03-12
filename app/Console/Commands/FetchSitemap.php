<?php

namespace App\Console\Commands;

use App\Jobs\FetchSitemap as FetchSitemapJob;
use Illuminate\Console\Command;

class FetchSitemap extends Command
{
    protected $signature = 'fetch-sitemap';

    protected $description = 'Schedule a job for fetching and handling the sitemap';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        FetchSitemapJob::dispatch();
    }
}
