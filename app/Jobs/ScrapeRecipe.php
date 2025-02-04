<?php

namespace App\Jobs;

use App\Exceptions\NotFoundException;
use App\Models\Recipe;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeRecipe implements ShouldQueue
{
    use Queueable;

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

        sleep(2);
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

        $recipe->save();

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

        $ingredients = [];

        foreach ($crawler->filter('[itemprop^="recipeIngredient"]') as $ingredient) {
            $ingredients[] = [
                'source' => trim($ingredient->textContent),
            ];
        }

        $recipe->ingredients()->createMany($ingredients);

        return $recipe;
    }
}
