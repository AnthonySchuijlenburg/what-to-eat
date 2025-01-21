<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use App\Models\ScrapedRecipe;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
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
                'serves' => $crawler->filter('[itemprop^="recipeYield"]')->text(),
                'preparation_time' => $crawler->filter('[itemprop^="totalTime"]')->text(),
                'course' => $crawler->filter('[itemprop^="recipeCategory"]')->text(),
                'nutritional_value' => $crawler->filter('[itemprop^="recipeCalories"]')->text(),
                'image_url' => '',
            ]);
            $recipe->scrapedRecipe()->associate($scrapedRecipe);

            $steps = [];
            foreach ($crawler->filter('[itemprop^="recipeInstructions"] li') as $step) {
                $steps[] = trim(str_replace('\n', '', $step->textContent));
            }

            $recipe->steps = $steps;

            $recipe->save();

            try {
                $image_url = $crawler->filter('[itemprop^="image"]')->attr('src', '');
                $imageContent = file_get_contents($image_url);
                Storage::disk('public')
                    ->put(sprintf('%s.jpg', $recipe->id), $imageContent);

                $recipe->image_url = sprintf('%s.jpg', $recipe->id);
                $recipe->save();
            } catch (\Exception $e) {
                $this->error($e);
            }

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
