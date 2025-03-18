<?php

namespace App\Jobs;

use App\Exceptions\NotFoundException;
use App\Services\SitemapService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchSitemapJob implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(
        SitemapService $sitemapService
    ): void {
        try {
            $sitemap = $sitemapService->fetchSitemap();
        } catch (NotFoundException $exception) {
            // Retry the job at a later time
            return;
        }

        $links = $sitemapService->unpackSitemapAndReturnLocations($sitemap);

        foreach ($links as $link => $lastChange) {
            $sitemapService->handleSitemapLocation($link, $lastChange);
        }
    }
}
