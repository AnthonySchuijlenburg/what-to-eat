<?php

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Jobs\ScrapeRecipeJob;

readonly class SitemapService
{
    public function __construct(
        private BrowserService $browserService
    ) {}

    /**
     * @return string The content of the sitemap
     *
     * @throws NotFoundException when the sitemap returns an unsuccessful response
     */
    public function fetchSitemap(): string
    {
        $url = config('app.recipes_source_base_url').'/sitemap.xml';
        $result = $this->browserService->makeRequest('GET', $url);

        if ($result->getStatusCode() !== 200) {
            throw new NotFoundException;
        }

        return $result->getContent();
    }

    /**
     * @return array<string, string>
     */
    public function unpackSitemapAndReturnLocations(string $sitemap): array
    {
        $pattern = '/<url>.*?<loc>(?<url>.*?\/recepten\/gezond-recept\/.*?)<\/loc>.*?<lastmod>(?<lastmod>.*?)<\/lastmod>.*?<\/url>/s';
        $matchCount = preg_match_all($pattern, $sitemap, $matches);

        $links = [];

        for ($i = 0; $i < $matchCount; $i++) {
            $links[$matches['url'][$i]] = $matches['lastmod'][$i];
        }

        return $links;
    }

    public function handleSitemapLocation(string $link, string $lastChange): void
    {
        ScrapeRecipeJob::dispatch($link, $lastChange);
    }
}
