<?php

namespace Tests\Feature\Jobs;

use App\Jobs\FetchSitemapJob;
use App\Jobs\ScrapeRecipeJob;
use App\Services\BrowserService;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\BrowserKit\Response;
use Tests\TestCase;

class FetchSitemapJobTest extends TestCase
{
    public function test_it_should_not_schedule_any_jobs_for_empty_sitemap(): void
    {
        // Arrange
        Queue::fake([
            ScrapeRecipeJob::class,
        ]);

        $content = '<?xml version="1.0" encoding="UTF-8" ?>
                        <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
                        </urlset>
                    ';
        $statusCode = 200;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $job = new FetchSitemapJob(new SitemapService($browserService));

        // Act
        $job->handle();

        // Assert
        Queue::assertNotPushed(ScrapeRecipeJob::class);
    }

    public function test_it_should_not_schedule_any_jobs_for_a_sitemap_without_recipes(): void
    {
        // Arrange
        Queue::fake([
            ScrapeRecipeJob::class,
        ]);

        $content = '<?xml version="1.0" encoding="UTF-8" ?>
                        <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
                            <url>
                                <loc>https://www.voedingscentrum.nl/encyclopedie.aspx</loc>
                                <lastmod>2021-03-04</lastmod>
                                <priority>1</priority>
                            </url>
                        </urlset>
                    ';
        $statusCode = 200;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $job = new FetchSitemapJob(new SitemapService($browserService));

        // Act
        $job->handle();

        // Assert
        Queue::assertNotPushed(ScrapeRecipeJob::class);
    }

    public function test_it_should_schedule_a_job_for_a_sitemap_with_a_job(): void
    {
        // Arrange
        Queue::fake([
            ScrapeRecipeJob::class,
        ]);

        $content = '<?xml version="1.0" encoding="UTF-8" ?>
                        <urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
                            <url>
                                <loc>https://www.voedingscentrum.nl/recepten/gezond-recept/aardappel-bloemkoolcurry-met-bief.aspx</loc>
                                <lastmod>2023-04-13</lastmod>
                                <priority>1</priority>
                            </url>
                        </urlset>
                    ';
        $statusCode = 200;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $job = new FetchSitemapJob(new SitemapService($browserService));

        // Act
        $job->handle();

        // Assert
        Queue::assertPushed(ScrapeRecipeJob::class, 1);
    }
}
