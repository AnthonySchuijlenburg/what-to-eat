<?php

namespace App\Console\Commands;

use App\Models\ScrapedRecipe;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeRecipe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipe:scrape';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {
        $source = ScrapedRecipe::query()->oldest()->where('scraped_at', '=', null)->first();

        if ($source === null) {
            $this->info('No sources where available to scrape.');

            return;
        }

        $this->info('Scraping: '.$source->source);

        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', $source->source);

        $source->update([
            'content' => $crawler->outerHtml(),
            'scraped_at' => now(),
        ]);

        $source->save();
    }
}
