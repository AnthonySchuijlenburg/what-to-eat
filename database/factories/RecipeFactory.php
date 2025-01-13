<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
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
            'variable_size' => $variable = fake()->boolean(),
            'serves' => $variable ? 1 : fake()->numberBetween(2, 10),
            'source' => fake()->url(),
            'image_url' => fake()->imageUrl(),
        ];
    }
}
