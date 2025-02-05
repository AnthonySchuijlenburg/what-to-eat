<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeRecipe;
use Illuminate\Console\Command;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

class FetchRecipes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $url = config('app.recipes_source_base_url').'/sitemap.xml';

        $this->info($url);

        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', $url);

        $pattern = '/<url>(.*?)<\/url>/s';

        if (! preg_match_all($pattern, $crawler->outerHtml(), $matches)) {
            $this->error('No locations where found');

            return;
        }

        $count = 0;

        foreach ($matches[1] as $loc) {
            preg_match('/<loc>(.*?)<\/loc>/', $loc, $link);
            preg_match('/<lastmod>(.*?)<\/lastmod>/', $loc, $lastModification);

            if (! $link || ! $lastModification || ! str_contains($link[1], '/recepten/gezond-recept/')) {
                continue;
            }

            ScrapeRecipe::dispatch(trim($link[1]), trim($lastModification[1]));
            $count++;
        }

        $this->info(sprintf(
            '%s found, %s scheduled',
            count($matches[1]),
            $count,
        ));
    }
}
