<?php

namespace App\Console\Commands;

use App\Models\ScrapedRecipe;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeRecipes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:scrape {amount=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(): void
    {
        $source = ScrapedRecipe::query()
            ->oldest()
            ->where('scraped_at', '=', null)
            ->orWhereColumn('scraped_at', '<', 'last_modified_at')
            ->limit($this->argument('amount'))
            ->get();

        if ($source->isEmpty()) {
            $this->info('No sources where available to scrape.');

            return;
        }

        foreach ($source as $sourceItem) {
            $this->info('Scraping: '.$sourceItem->source);

            $browser = new HttpBrowser(HttpClient::create());
            $crawler = $browser->request('GET', $sourceItem->source);

            $sourceItem->update([
                'content' => $crawler->outerHtml(),
                'scraped_at' => now(),
            ]);

            if ($crawler->getUri() === 'https://www.voedingscentrum.nl/nl/404.aspx') {
                $sourceItem->update([
                    'content' => null,
                    'scraped_at' => new Carbon('01-01-2030'),
                    'processed_at' => new Carbon('01-01-2030'),
                ]);
            }

            $sourceItem->save();
        }
    }
}
