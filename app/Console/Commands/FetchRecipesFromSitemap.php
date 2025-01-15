<?php

namespace App\Console\Commands;

use App\Models\ScrapedRecipe;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class FetchRecipesFromSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipe:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $url = 'https://www.voedingscentrum.nl/sitemap.xml';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', $this->url);

        $pattern = '/<loc>(.*?)<\/loc>/';
        $count = 0;

        if (preg_match_all($pattern, $crawler->outerHtml(), $matches)) {
            foreach ($matches[1] as $link) {
                $link = trim($link);
                if (str_contains($link, '/recepten/gezond-recept/') && ! ScrapedRecipe::query()->where('source', '=', $link)->exists()) {
                    (new ScrapedRecipe(['source' => $link]))->save();
                    $count++;
                }
            }
        }

        $this->info(count($matches[1]).' locations found and '.$count.' scheduled.');
    }
}
