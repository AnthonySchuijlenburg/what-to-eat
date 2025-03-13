<?php

namespace Tests\Unit\Services;

use App\Exceptions\NotFoundException;
use App\Jobs\ScrapeRecipeJob;
use App\Services\BrowserService;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\BrowserKit\Response;
use Tests\TestCase;

class SitemapServiceTest extends TestCase
{
    public function test_it_should_throw_an_exception_on_not_found(): void
    {
        // Arrange
        $browser = $this->createMock(BrowserService::class);
        $sitemapService = new SitemapService($browser);

        $content = '';
        $statusCode = 404;

        $browser
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $this->expectException(NotFoundException::class);

        // Act
        $result = $sitemapService->fetchSitemap();

        // Assert
        $this->assertNull($result);
    }

    public function test_it_should_not_throw_an_exception_on_found(): void
    {
        // Arrange
        $browser = $this->createMock(BrowserService::class);
        $sitemapService = new SitemapService($browser);

        $sitemap = '<?xml version="1.0" encoding="UTF-8" ?>
                        <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
                        </urlset>
                    ';
        $statusCode = 200;

        $browser
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($sitemap, $statusCode));

        // Act
        $result = $sitemapService->fetchSitemap();

        // Assert
        $this->assertEquals($sitemap, $result);
    }

    public function test_it_should_return_no_locations_on_an_empty_sitemap()
    {
        // Arrange
        $browserService = $this->createMock(BrowserService::class);
        $sitemapService = new SitemapService($browserService);

        $sitemap = '<?xml version="1.0" encoding="UTF-8" ?>
                        <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
                        </urlset>
                    ';

        // Act
        $result = $sitemapService->unpackSitemapAndReturnLocations($sitemap);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_it_should_return_no_locations_on_a_sitemap_without_recipes()
    {
        // Arrange
        $browserService = $this->createMock(BrowserService::class);
        $sitemapService = new SitemapService($browserService);

        $sitemap = '<?xml version="1.0" encoding="UTF-8" ?>
                        <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
                            <url>
                                <loc>https://www.voedingscentrum.nl/encyclopedie.aspx</loc>
                                <lastmod>2021-03-04</lastmod>
                                <priority>1</priority>
                            </url>
                        </urlset>
                    ';

        // Act
        $result = $sitemapService->unpackSitemapAndReturnLocations($sitemap);

        // Assert
        $this->assertEmpty($result);
    }

    public function test_it_should_return_recipe_locations()
    {
        // Arrange
        $browserService = $this->createMock(BrowserService::class);
        $sitemapService = new SitemapService($browserService);

        $url = 'https://www.voedingscentrum.nl/recepten/gezond-recept/aardappel-bloemkoolcurry-met-bief.aspx';
        $lastmod = '2023-04-13';

        $sitemap = '<?xml version="1.0" encoding="UTF-8" ?>
                        <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
                            <url>
                                <loc>'.$url.'</loc>
                                <lastmod>'.$lastmod.'</lastmod>
                                <priority>1</priority>
                            </url>
                        </urlset>
                    ';

        // Act
        $result = $sitemapService->unpackSitemapAndReturnLocations($sitemap);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals(
            [$url => $lastmod],
            $result
        );
    }

    public function test_it_should_schedule_a_job(): void
    {
        // Arrange
        $browserService = $this->createMock(BrowserService::class);
        $sitemapService = new SitemapService($browserService);

        $url = 'https://www.voedingscentrum.nl/recepten/gezond-recept/aardappel-bloemkoolcurry-met-bief.aspx';
        $lastmod = '2023-04-13';

        Queue::fake();

        // Act
        $sitemapService->handleSitemapLocation($url, $lastmod);

        Queue::assertPushed(ScrapeRecipeJob::class);
    }
}
