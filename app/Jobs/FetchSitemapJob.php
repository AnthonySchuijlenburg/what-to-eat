<?php

namespace App\Jobs;

use App\Exceptions\NotFoundException;
use App\Services\SitemapService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchSitemapJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly SitemapService $sitemapService
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $sitemap = $this->sitemapService->fetchSitemap();
        } catch (NotFoundException $exception) {
            // Retry the job at a later time
            return;
        }

        $links = $this->sitemapService->unpackSitemapAndReturnLocations($sitemap);

        foreach ($links as $link => $lastChange) {
            $this->sitemapService->handleSitemapLocation($link, $lastChange);
        }
    }
}
