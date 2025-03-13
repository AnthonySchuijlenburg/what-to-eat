<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Jobs\ScrapeRecipe;

class SitemapService
{
    public function __construct(
        private readonly BrowserService $browserService
    )
    {}


    /**
     * @throws NotFoundException when the sitemap returns an unsuccessful response
     * @return string The content of the sitemap
     */
    public function fetchSitemap(): string
    {
        $url = config('app.recipes_source_base_url').'/sitemap.xml';
        $result = $this->browserService->makeRequest('GET', $url);

        if (200 !== $result->getStatusCode()) {
            throw new NotFoundException();
        }

        return $result->getContent();
    }

    /**
     * @param string $sitemap
     * @return array<string, string>
     */
    public function unpackSitemapAndScheduleJobs(string $sitemap): array
    {
        $pattern = '/<url>(.*?)<\/url>/s';

        if (! preg_match_all($pattern, $sitemap, $matches)) {
            return [];
        }

        $links = [];

        foreach ($matches[1] as $loc) {
            preg_match('/<loc>(.*?)<\/loc>/', $loc, $link);
            preg_match('/<lastmod>(.*?)<\/lastmod>/', $loc, $lastModification);

            if (! $link || ! $lastModification || ! str_contains($link[1], '/recepten/gezond-recept/')) {
                continue;
            }

            $links[trim($link[1])] = trim($lastModification[1]);
        }

        return $links;
    }

    public function handleSitemapLocation(string $link, string $lastChange): void
    {
        dd($link, $lastChange);
        ScrapeRecipe::dispatch($link, $lastChange);
    }
}
