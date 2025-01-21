<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Models\ScrapedRecipe;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\DomCrawler\Crawler;

class ProcessRecipes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recipes:process {limit=50}';

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
        $scrapedRecipes = ScrapedRecipe::query()
            ->where(function (Builder $query) {
                $query
                    ->where('scraped_at', '!=', null)
                    ->where('content', '!=', null)
                    ->where('processed_at', '=', null);
            })
            ->orWhere(function (Builder $query) {
                $query
                    ->where('scraped_at', '!=', null)
                    ->where('content', '!=', null)
                    ->whereColumn('processed_at', '<=', 'scraped_at');
            })
            ->limit($this->argument('limit'))
            ->get();

        $this->info(sprintf('Found %s recipes to process', $scrapedRecipes->count()));

        if ($scrapedRecipes->isEmpty()) {
            return;
        }

        foreach ($scrapedRecipes as $scrapedRecipe) {
            $crawler = new Crawler($scrapedRecipe->content);
            $recipe = new Recipe([
                'name' => $crawler->filter('h1')->text(),
                'description' => $crawler->filter('[itemprop^="description"]')->text(),
                'variable_size' => false,
                'serves' => preg_replace('/[^0-9]/', '', $crawler->filter('[itemprop^="recipeYield"]')->text()),
                'image_url' => $crawler->filter('[itemprop^="image"]')->attr('src', ''),
            ]);
            $recipe->scrapedRecipe()->associate($scrapedRecipe);

            $steps = [];
            foreach ($crawler->filter('[itemprop^="recipeInstructions"] li') as $step) {
                $steps[] = trim(str_replace('\n', '', $step->textContent));
            }

            $recipe->steps = $steps;

            $recipe->save();

            foreach ($crawler->filter('[itemprop^="recipeIngredient"]') as $ingredient) {
                $recipe->ingredients()->create([
                    'name' => trim($ingredient->textContent),
                    'amount' => trim($ingredient->textContent),
                    'amount_in_grams' => 0,
                ]);
            }

            $scrapedRecipe->processed_at = now();
            $scrapedRecipe->save();
        }

        $this->info(sprintf('Processed %s recipes', $scrapedRecipes->count()));
    }
}
