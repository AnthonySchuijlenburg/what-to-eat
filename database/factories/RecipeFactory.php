<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'description' => fake()->sentences(fake()->numberBetween(2, 6), true),
            'steps' => fake()->sentences(fake()->numberBetween(5, 10)),
            'serves' => fake()->numberBetween(2, 10),
            'preparation_time' => fake()->numberBetween(10, 60).' minutes',
            'course' => fake()->randomElement(['starter', 'main', 'dessert']),
            'nutritional_value' => fake()->numberBetween(100, 500).' Kcal',
            'image_url' => fake()->imageUrl(),
            'source_url' => fake()->url(),
        ];
    }

    public function withIngredients($amount = 5): self
    {
        return $this->has(Ingredient::factory()->count($amount));
    }
}
