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

        $pattern = '/<url>(.*?)<\/url>/s';

        if (! preg_match_all($pattern, $crawler->outerHtml(), $matches)) {
            $this->error('No locations where found');

            return;
        }

        $count = ['found' => count($matches[1]), 'updated' => 0, 'created' => 0];

        foreach ($matches[1] as $loc) {
            preg_match('/<loc>(.*?)<\/loc>/', $loc, $link);
            preg_match('/<lastmod>(.*?)<\/lastmod>/', $loc, $lastModification);

            if (! $link || ! $lastModification || ! str_contains($link[1], '/recepten/gezond-recept/')) {
                continue;
            }

            $result = ScrapedRecipe::updateOrCreate(['source' => trim($link[1])], ['last_modified_at' => $lastModification[1]]);
            $result->wasRecentlyCreated ? $count['created']++ : $count['updated']++;
        }

        $this->info(sprintf(
            '%s found, %s created and %s updated',
            $count['found'],
            $count['created'],
            $count['updated'],
        ));
    }
}
