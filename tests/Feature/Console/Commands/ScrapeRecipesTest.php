<?php

namespace Console\Commands;

use App\Models\ScrapedRecipe;
use Tests\Feature\ExampleTest;

class ScrapeRecipesTest extends ExampleTest
{
    public function it_should_not_run_if_there_are_no_recipes_to_scrape(): void
    {
        $this->artisan('recipes:fetch')
            ->expectsOutput('No sources where available to scrape.')
            ->assertSuccessful();
    }

    public function it_should_not_run_if_there_are_no_left_to_scrape(): void
    {
        ScrapedRecipe::factory()->create();

        $this->artisan('recipes:fetch')
            ->expectsOutput('No sources where available to scrape.')
            ->assertSuccessful();
    }
}
