<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecipeResult>
 */
class RecipeResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }

    public function configure()
    {
        return $this->faker->randomElement([
            $this->chickpeasBurgers(),
            $this->macaroni(),
            $this->pitaWithDip(),
        ]);
    }

    public function chickpeasBurgers(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'url' => 'https://www.voedingscentrum.nl/recepten/gezond-recept/kikkererwtenburgers-met-tzatziki-en-rauwkost.aspx',
                'status_code' => 200,
                'result' => File::get(storage_path('RecipePages/recipe_1.html')),
            ];
        });
    }

    public function macaroni(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'url' => 'https://www.voedingscentrum.nl/recepten/gezond-recept/romige-macaroni-met-venkel-en-courgette.aspx',
                'status_code' => 200,
                'result' => File::get(storage_path('RecipePages/recipe_2.html')),
            ];
        });
    }

    public function pitaWithDip(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'url' => 'https://www.voedingscentrum.nl/recepten/gezond-recept/volkoren-pita-met-yoghurt-spinaziedip-en-kikkererwten.aspx',
                'status_code' => 200,
                'result' => File::get(storage_path('RecipePages/recipe_3.html')),
            ];
        });
    }
}
