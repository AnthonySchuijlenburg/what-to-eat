<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScrapedRecipe>
 */
class ScrapedRecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source' => fake()->url(),
            'content' => fake()->sentences(10, true),
            'scraped_at' => $scraped = fake()->dateTimeThisMonth(),
            'processed_at' => (new Carbon($scraped))->addDays(2),
            'last_modified_at' => (new Carbon($scraped))->subMonths(6),
        ];
    }

    public function unScraped(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'scraped_at' => null,
                'processed_at' => null,
                'last_modified_at' => fake()->dateTimeThisYear(),
            ];
        });
    }

    public function outdated(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'scraped_at' => $scraped = fake()->dateTimeThisYear(),
                'processed_at' => (new Carbon($scraped))->addDays(2),
                'last_modified_at' => (new Carbon($scraped))->addWeeks(4),
            ];
        });
    }
}
