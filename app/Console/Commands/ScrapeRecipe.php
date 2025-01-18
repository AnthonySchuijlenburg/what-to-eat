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
    protected $signature = 'recipe:scrape {amount=5}';

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

            $sourceItem->save();
        }
    }
}
