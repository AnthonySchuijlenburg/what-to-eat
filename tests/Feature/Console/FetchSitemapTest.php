<?php

namespace Feature\Console;

use App\Jobs\FetchSitemapJob;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FetchSitemapTest extends TestCase
{
    public function test_it_should_schedule_a_sitemap_link(): void
    {
        // Arrange
        Queue::fake();

        // Act
        $this->artisan('fetch-sitemap');

        // Assert
        Queue::assertPushed(FetchSitemapJob::class);
    }
}
