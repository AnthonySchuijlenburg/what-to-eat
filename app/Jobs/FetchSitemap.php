<?php

namespace App\Jobs;

use App\Services\BrowserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchSitemap implements ShouldQueue
{
    use Queueable;

    private BrowserService $browserService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->browserService = new BrowserService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = config('app.recipes_source_base_url').'/sitemap.xml';
        $result = $this->browserService->makeRequest('GET', $url);

        $pattern = '/<url>(.*?)<\/url>/s';

        if (! preg_match_all($pattern, $result, $matches)) {
            return;
        }

        foreach ($matches[1] as $loc) {
            preg_match('/<loc>(.*?)<\/loc>/', $loc, $link);
            preg_match('/<lastmod>(.*?)<\/lastmod>/', $loc, $lastModification);

            if (! $link || ! $lastModification || ! str_contains($link[1], '/recepten/gezond-recept/')) {
                continue;
            }

            ScrapeRecipe::dispatch(trim($link[1]), trim($lastModification[1]));
        }
    }
}
