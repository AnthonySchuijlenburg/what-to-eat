<?php

namespace App\Jobs;

use App\Exceptions\NotFoundException;
use App\Models\Recipe;
use App\Models\RecipeResult;
use Carbon\Carbon;
use Cloudstudio\Ollama\Facades\Ollama;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeRecipeJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 240;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $sourceUrl,
        public readonly string $lastModification,
    ) {}

    public function handle(): void
    {
        //        $recipeResult = RecipeResult::query()
        //            ->where('url', $this->sourceUrl)
        //            ->latest()
        //            ->first();
        //
        //        if (
        //            $recipeResult &&
        //            new Carbon($recipeResult->updated_at) > new Carbon($this->lastModification)
        //        ) {
        //            return;
        //        }
        //
        //        try {
        //            $content = $this->scrape();
        //            $this->process($content);
        //        } catch (NotFoundException $exception) {
        //            RecipeResult::query()
        //                ->updateOrCreate(
        //                    [
        //                        'url' => $this->sourceUrl,
        //                    ],
        //                    [
        //                        'status_code' => 404,
        //                    ]
        //                );
        //        }
    }

    //    /**
    //     * @throws NotFoundException
    //     */
    //    private function scrape(): RecipeResult
    //    {
    //        $browser = new HttpBrowser(HttpClient::create());
    //        $crawler = $browser->request('GET', $this->sourceUrl);
    //
    //        if ($crawler->getUri() === config('app.recipes_source_base_url').'/nl/404.aspx') {
    //            throw new NotFoundException;
    //        }
    //
    //        $content = $crawler->filter('body')->outerHtml();
    //
    //        return RecipeResult::query()
    //            ->create(
    //                [
    //                    'url' => $this->sourceUrl,
    //                    // An error is thrown if the status_code is not a 200
    //                    'status_code' => 200,
    //                    'result' => $content,
    //                ]
    //            );
    //    }
    //
    //    private function process(RecipeResult $recipeResult): Recipe
    //    {
    //        $crawler = new Crawler($recipeResult->result);
    //
    //        $recipe = new Recipe([
    //            'name' => $crawler->filter('h1')->text(),
    //            'description' => $crawler->filter('[itemprop^="description"]')->text(),
    //            'serves' => $crawler->filter('[itemprop^="recipeYield"]')->text(),
    //            'preparation_time' => $crawler->filter('[itemprop^="totalTime"]')->text(),
    //            'course' => $crawler->filter('[itemprop^="recipeCategory"]')->text(),
    //            'nutritional_value' => $crawler->filter('[itemprop^="recipeCalories"]')->text(),
    //            'image_url' => '',
    //            'source_url' => $this->sourceUrl,
    //        ]);
    //
    //        $previousRecipe = Recipe::query()
    //            ->where('source_url', $this->sourceUrl)
    //            ->first();
    //
    //        if ($previousRecipe !== null) {
    //            $recipe->id = $previousRecipe->id;
    //        }
    //
    //        $steps = [];
    //
    //        foreach ($crawler->filter('[itemprop^="recipeInstructions"] li') as $step) {
    //            $steps[] = trim(str_replace('\n', '', $step->textContent));
    //        }
    //
    //        $recipe->steps = $steps;
    //
    //        // Extract the ingredients before writing the recipe to the database
    //        // So that the database won't have incomplete recipes when something goes wrong
    //        $ingredients = $this->extractIngredients(
    //            $crawler->filter('[itemprop^="recipeIngredient"]')
    //        );
    //
    //        $recipe->save();
    //
    //        $recipeResult
    //            ->recipe()
    //            ->associate($recipe)
    //            ->save();
    //
    //        $recipe->ingredients()->createMany(
    //            $ingredients
    //        );
    //
    //        try {
    //            $image_url = $crawler->filter('[itemprop^="image"]')->attr('src', '');
    //            $imageContent = file_get_contents($image_url);
    //            Storage::disk('public')
    //                ->put(sprintf('%s.jpg', $recipe->id), $imageContent);
    //            $recipe->image_url = sprintf('%s.jpg', $recipe->id);
    //            $recipe->save();
    //        } catch (Exception $e) {
    //            // Don't save an image
    //        }
    //
    //        return $recipe;
    //    }
    //
    //    private function extractIngredients(Crawler $crawler): array
    //    {
    //        return array_map(function ($ingredient) {
    //            $ingredient = trim($ingredient->textContent);
    //
    //            $response = Ollama::options([
    //                'num_thread' => 2,
    //                'temperature' => 0.4,
    //            ])
    //                ->tools([
    //                    [
    //                        'type' => 'function',
    //                        'function' => [
    //                            'name' => 'convert_to_grams',
    //                            'description' => 'Given a required ingredient for a recipe, this function returns the name of that ingredient and the amount of grams is needed of it.',
    //                            'parameters' => [
    //                                'type' => 'object',
    //                                'properties' => [
    //                                    'ingredient' => [
    //                                        'type' => 'string',
    //                                        'description' => 'The name of the ingredient provided without the amount.',
    //                                    ],
    //                                    'amount' => [
    //                                        'type' => 'number',
    //                                        'description' => 'A number of grams needed of the provided ingredient, e.g. 10, 500, 40. When no amount of grams is provided guess how much of it based on common sense e.g. an egg weighs 55 grams',
    //                                    ],
    //                                ],
    //                                'required' => ['ingredient', 'amount'],
    //                            ],
    //                        ],
    //                    ],
    //                ])
    //                ->chat([
    //                    ['role' => 'user', 'content' => $ingredient],
    //                ]);
    //
    //            return [
    //                'source' => $ingredient,
    //                'name' => $response['message']['tool_calls'][0]['function']['arguments']['ingredient'],
    //                'amount_in_grams' => (float) $response['message']['tool_calls'][0]['function']['arguments']['amount'],
    //            ];
    //        }, iterator_to_array($crawler));
    //    }
}
