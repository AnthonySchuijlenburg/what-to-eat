<?php

namespace Console\Commands;

use App\Models\ScrapedRecipe;
use Tests\Feature\ExampleTest;

class ScrapeRecipeTest extends ExampleTest
{
    public function it_should_not_run_if_there_are_no_recipes_to_scrape(): void
    {
        $this->artisan('recipe:fetch')
            ->expectsOutput('No sources where available to scrape.')
            ->assertSuccessful();
    }

    public function it_should_not_run_if_there_are_no_left_to_scrape(): void
    {
        ScrapedRecipe::factory()->create();

        $this->artisan('recipe:fetch')
            ->expectsOutput('No sources where available to scrape.')
            ->assertSuccessful();
    }
}
