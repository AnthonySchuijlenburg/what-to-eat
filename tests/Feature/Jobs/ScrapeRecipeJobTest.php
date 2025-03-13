<?php

namespace Feature\Jobs;

use App\Jobs\ScrapeRecipeJob;
use App\Models\Recipe;
use App\Models\RecipeResult;
use App\Services\BrowserService;
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
            $browserService,
        );

        // Act
        $job->handle();

        // Assert
        $result = RecipeResult::all()->first();
        $this->assertInstanceOf(RecipeResult::class, $result);
        $this->assertEquals($content, $result->result);
        $this->assertEquals($statusCode, $result->status_code);
    }

    public function test_it_should_create_a_recipe_result_on_a_unsuccessful_scrape(): void
    {
        // Arrange
        $content = '<body><h1>Hello World!</h1></body>';
        $statusCode = 404;

        $browserService = $this->createMock(BrowserService::class);
        $browserService
            ->expects($this->once())
            ->method('makeRequest')
            ->willReturn(new Response($content, $statusCode));

        $job = new ScrapeRecipeJob(
            '',
            '',
            $browserService,
        );

        // Act
        $job->handle();

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
            $browserService,
        );

        // Act
        $job->handle();

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
            $browserService,
        );

        // Act
        $job->handle();

        // Assert
        $result = Recipe::with('ingredients')->latest()->first();
        $this->assertInstanceOf(Recipe::class, $result);
    }
}
