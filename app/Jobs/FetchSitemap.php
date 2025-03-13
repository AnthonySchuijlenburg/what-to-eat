<?php

namespace App\Jobs;

use App\Exceptions\NotFoundException;
use App\Models\RecipeResult;
use App\Services\BrowserService;
use App\Services\SitemapService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class FetchSitemap implements ShouldQueue
{
    use Queueable;

    private SitemapService $sitemapService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->sitemapService = new SitemapService(
            new BrowserService(),
        );
    }

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

        $links = $this->sitemapService->unpackSitemapAndScheduleJobs($sitemap);

        foreach ($links as $link => $lastChange) {
            $this->sitemapService->handleSitemapLocation($link, $lastChange);
        }
    }
}
