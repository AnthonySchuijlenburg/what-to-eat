<?php

namespace App\Jobs;

use App\Exceptions\NotFoundException;
use App\Models\Recipe;
use Carbon\Carbon;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeRecipe implements ShouldQueue
{
    use Queueable;

    public int $timeout = 240;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $sourceUrl,
        private readonly string $lastModification
    ) {}

    /**
     * Execute the job.
     *
     * @throws NotFoundException
     */
    public function handle(): void
    {
        $recipe = Recipe::query()->where('source_url', $this->sourceUrl)->first();

        if (
            $recipe &&
            new Carbon($recipe->updated_at) > new Carbon($this->lastModification)
        ) {
            return;
        }

        $content = $this->scrape();
        $this->process($content);
    }

    /**
     * @throws NotFoundException
     */
    private function scrape(): string
    {
        $browser = new HttpBrowser(HttpClient::create());
        $crawler = $browser->request('GET', $this->sourceUrl);

        if ($crawler->getUri() === config('app.recipes_source_base_url').'/nl/404.aspx') {
            throw new NotFoundException;
        }

        return $crawler->filter('body')->outerHtml();
    }

    /**
     * @throws \Exception
     */
    private function process(string $content): Recipe
    {
        $crawler = new Crawler($content);

        $recipe = new Recipe([
            'name' => $crawler->filter('h1')->text(),
            'description' => $crawler->filter('[itemprop^="description"]')->text(),
            'serves' => $crawler->filter('[itemprop^="recipeYield"]')->text(),
            'preparation_time' => $crawler->filter('[itemprop^="totalTime"]')->text(),
            'course' => $crawler->filter('[itemprop^="recipeCategory"]')->text(),
            'nutritional_value' => $crawler->filter('[itemprop^="recipeCalories"]')->text(),
            'image_url' => '',
            'source_url' => $this->sourceUrl,
        ]);

        $steps = [];

        foreach ($crawler->filter('[itemprop^="recipeInstructions"] li') as $step) {
            $steps[] = trim(str_replace('\n', '', $step->textContent));
        }

        $recipe->steps = $steps;

        // Extract the ingredients before writing the recipe to the database
        // So that the database won't have incomplete recipes when something goes wrong
        $ingredients = $this->extractIngredients(
            $recipe,
            $crawler->filter('[itemprop^="recipeIngredient"]'));

        $recipe->save();

        $recipe->ingredients()->createMany(
            $ingredients
        );

        try {
            $image_url = $crawler->filter('[itemprop^="image"]')->attr('src', '');
            $imageContent = file_get_contents($image_url);
            Storage::disk('public')
                ->put(sprintf('%s.jpg', $recipe->id), $imageContent);
            $recipe->image_url = sprintf('%s.jpg', $recipe->id);
            $recipe->save();
        } catch (\Exception $e) {
            // Don't save an image
        }

        return $recipe;
    }

    private function extractIngredients(Recipe $recipe, Crawler $crawler): array
    {
        $ingredients = array_map(fn ($ingredient) => trim($ingredient->textContent), iterator_to_array($crawler));

        $prompt = 'instructions: '.implode('', $recipe->steps)."\n".
            'ingredients: ["'.implode('", "', $ingredients).'"]';

        $response = Ollama::agent(
            '
                        You are a backend service only allowed to respond in a structured JSON-format.
                        You will be provided with a list of instructions followed by a list of ingredients, all in Dutch.
                        Your response must be in Dutch.

                        Never use abbreviations, when provided with "el" or "g" use "eetlepel" or "gram".
                        Never use unicode, always writhe fractions out fully as "1/2" or "1/4".
                        This format consist of the following entries: {"name": "name", "amount": "amount", "amount_in_grams": amount_in_grams}.

                        Given:
                        instructions: "Meng alle ingredienten."
                        ingredients: ["citroensap", "1 shot wodka", "een paar blokjes ijs", "100ml sprite", "½ el suiker"]

                        Expected response:
                        [
                            {"source": "citroensap","name": "citroensap", "amount": "1 scheutje", "amount_in_grams": 5},
                            {"source": "1 shot wodka","name": "wodka", "amount": "1 shot", "amount_in_grams": 30},
                            {"source": "een paar blokjes ijs","name": "ijs", "amount": "een paar blokjes", "amount_in_grams": 50},
                            {"source": "100 milliliter sprite","name": "sprite", "amount": "100 milliliter", "amount_in_grams": 100},
                            {"source": 1/2 eetlepel suiker","name": "suiker", "amount": "1/2 eetlepel", "amount_in_grams": 2.5},
                        ]
                    '
        )
            ->options([
                'num_thread' => 2,
                'temperature' => 0.4,
            ])
            ->prompt($prompt)
            ->stream(false)
            ->ask();

        return json_decode($response['response'], true);
    }
}
