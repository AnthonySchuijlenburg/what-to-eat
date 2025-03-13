<?php

namespace App\Console\Commands;

use App\Jobs\FetchSitemapJob;
use App\Services\SitemapService;
use Illuminate\Console\Command;

class FetchSitemap extends Command
{
    protected $signature = 'fetch-sitemap';

    protected $description = 'Schedule a job for fetching and handling the sitemap';

    public function __construct(
        private readonly SitemapService $sitemapService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        FetchSitemapJob::dispatch($this->sitemapService);
    }
}
