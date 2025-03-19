<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ScrapeRecipeJob;
use App\Models\Recipe;
use App\Models\RecipeResult;
use App\Services\BrowserService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Symfony\Component\BrowserKit\Response;
use Tests\TestCase;

class ScrapeRecipeJobTest extends TestCase
{
    public function test_it_should_create_a_recipe_result_on_successful_scrape(): void
    {
        // Arrange
        $content = File::get(storage_path('RecipePages/recipe_1.html'));
        $statusCode = 200;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $job = new ScrapeRecipeJob(
            '',
            '',
        );

        // Act
        $job->handle($browserService);

        // Assert
        $result = RecipeResult::all()->first();
        $this->assertInstanceOf(RecipeResult::class, $result);
        $this->assertEquals($content, $result->result);
        $this->assertEquals($statusCode, $result->status_code);
    }

    public function test_it_should_create_a_recipe_result_on_a_unsuccessful_scrape(): void
    {
        // Arrange
        $content = '<body><h1>Hello World!</h1><a href="'.ScrapeRecipeJob::NOT_FOUND_URL.'"></a></body>';
        $statusCode = 404;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $job = new ScrapeRecipeJob(
            '',
            '',
        );

        // Act
        $job->handle($browserService);

        // Assert
        $result = RecipeResult::all()->first();
        $this->assertInstanceOf(RecipeResult::class, $result);
        $this->assertEquals($content, $result->result);
        $this->assertEquals($statusCode, $result->status_code);
    }

    public function test_it_should_not_create_a_recipe_result_if_there_is_already_a_recent_result(): void
    {
        // Arrange
        $browserService = $this->createMock(BrowserService::class);

        $recipeResult = RecipeResult::factory()
            ->macaroni()->create([
                'created_at' => '01-01-2022',
                'updated_at' => '01-01-2022',
            ]);

        $job = new ScrapeRecipeJob(
            $recipeResult->url,
            '01-01-2020',
        );

        // Act
        $job->handle($browserService);

        // Assert
        $this->assertDatabaseCount('recipe_results', 1);
    }

    public function test_it_should_create_a_recipe_from_a_result(): void
    {
        // Arrange
        $content = File::get(storage_path('RecipePages/recipe_1.html'));
        $statusCode = 200;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $job = new ScrapeRecipeJob(
            '',
            '',
        );

        // Act
        $job->handle($browserService);

        // Assert
        $result = Recipe::with('ingredients')->latest()->first();
        $this->assertInstanceOf(Recipe::class, $result);
    }

    public function test_it_should_create_a_recipe_from_a_result_newer_result(): void
    {
        // Arrange
        $content = File::get(storage_path('RecipePages/recipe_1.html'));
        $statusCode = 200;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        RecipeResult::factory()->macaroni()->create();
        $oldRecipe = Recipe::factory()->create([
            'created_at' => '01-01-2022',
            'updated_at' => '01-01-2022',
        ]);

        $job = new ScrapeRecipeJob(
            $oldRecipe->source_url,
            '01-01-2025',
        );

        // Act
        $job->handle($browserService);

        // Assert
        $result = Recipe::with('ingredients')->latest()->first();
        $this->assertInstanceOf(Recipe::class, $result);
        $this->assertTrue((new Carbon($result->updated_at))->isCurrentDay());
        $this->assertDatabaseCount('recipe_results', 2);
    }
}
