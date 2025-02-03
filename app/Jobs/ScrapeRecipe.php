<?php

namespace App\Jobs;

use App\Models\ScrapedRecipe;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeRecipe implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private ScrapedRecipe $scrapedRecipe
    ) {}

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new RateLimited('scrape-recipe')];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', $this->scrapedRecipe->source);

        $this->scrapedRecipe->update([
            'content' => $crawler->outerHtml(),
            'scraped_at' => now(),
        ]);

        if ($crawler->getUri() === 'https://www.voedingscentrum.nl/nl/404.aspx') {
            $this->scrapedRecipe->update([
                'content' => null,
                'scraped_at' => new Carbon('01-01-2030'),
                'processed_at' => new Carbon('01-01-2030'),
            ]);
        }

        $this->scrapedRecipe->save();
    }
}
