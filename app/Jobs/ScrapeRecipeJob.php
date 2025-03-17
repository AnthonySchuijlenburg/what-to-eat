<?php

namespace App\Jobs;

use App\Models\Recipe;
use App\Models\RecipeResult;
use App\Services\BrowserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeRecipeJob implements ShouldQueue
{
    use Queueable;

    private BrowserService $browserService;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $sourceUrl,
        private readonly string $lastModification,
    ) {}

    public function handle(
        BrowserService $browserService
    ): void {
        $this->browserService = $browserService;

        $recipeResult = RecipeResult::query()
            ->where('url', $this->sourceUrl)
            ->latest()
            ->first();

        if (
            $recipeResult &&
            new Carbon($recipeResult->updated_at) > new Carbon($this->lastModification)
        ) {
            // Latest scrape is already newer than last modification
            return;
        }

        $source = $this->scrape();

        if ($source->status_code === 404) {
            return;
        }

        $this->process($source);
    }

    private function scrape(): RecipeResult
    {
        $result = $this->browserService->makeRequest('GET', $this->sourceUrl);

        return RecipeResult::query()->create([
            'url' => $this->sourceUrl,
            'status_code' => $result->getStatusCode(),
            'result' => $result->getContent(),
        ]);
    }

    private function process(RecipeResult $recipeResult): Recipe
    {
        $crawler = new Crawler($recipeResult->result);

        $recipe = new Recipe([
            'name' => $crawler->filter('h1')->text(),
            'description' => $this->tryToGetAttribute($crawler, '[itemprop^="description"]'),
            'serves' => $this->tryToGetAttribute($crawler, '[itemprop^="recipeYield"]'),
            'preparation_time' => $this->tryToGetAttribute($crawler, '[itemprop^="totalTime"]'),
            'course' => $this->tryToGetAttribute($crawler, '[itemprop^="recipeCategory"]'),
            'nutritional_value' => $this->tryToGetAttribute($crawler, '[itemprop^="recipeCalories"]'),
            'image_url' => '',
            'source_url' => $this->sourceUrl,
        ]);

        $previousRecipe = Recipe::query()
            ->where('source_url', $this->sourceUrl)
            ->first();

        if ($previousRecipe !== null) {
            $recipe->id = $previousRecipe->id;
            $recipe->exists = true;
            $recipe->wasRecentlyCreated = true;
        }

        $steps = [];

        foreach ($crawler->filter('[itemprop^="recipeInstructions"] li') as $step) {
            $steps[] = trim(str_replace('\n', '', $step->textContent));
        }

        $recipe->steps = $steps;

        // Extract the ingredients before writing the recipe to the database
        // So that the database won't have incomplete recipes when something goes wrong
        $ingredients = $this->extractIngredients(
            $crawler->filter('[itemprop^="recipeIngredient"]')
        );

        $recipe->save();

        $recipeResult
            ->recipe()
            ->associate($recipe)
            ->save();

        $recipe->ingredients()->createMany(
            $ingredients
        );

        try {
            $image_url = $crawler->filter('[itemprop^="image"]')->attr('src', '');

            if ($image_url === null || $image_url === '') {
                throw new Exception('Image URL is empty');
            }

            $imageContent = file_get_contents($image_url);
            Storage::disk('public')
                ->put(sprintf('%s.jpg', $recipe->id), $imageContent);
            $recipe->image_url = sprintf('%s.jpg', $recipe->id);
            $recipe->save();
        } catch (Exception $e) {
            // Don't save an image
        }

        return $recipe;
    }

    private function tryToGetAttribute(Crawler $crawler, string $query): string
    {
        try {
            return $crawler->filter($query)->text();
        } catch (Exception $exception) {
            return 'Onbekend';
        }
    }

    private function extractIngredients(Crawler $crawler): array
    {
        return array_map(function ($ingredient) {
            return [
                'source' => trim($ingredient->textContent),
            ];
        }, iterator_to_array($crawler));
    }
}
