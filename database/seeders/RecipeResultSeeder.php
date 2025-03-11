<?php

namespace Database\Seeders;

use App\Models\RecipeResult;
use Illuminate\Database\Seeder;

class RecipeResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RecipeResult::factory()->chickpeasBurgers()->count(5)->create();
        RecipeResult::factory()->macaroni()->count(5)->create();
        RecipeResult::factory()->pitaWithDip()->count(5)->create();
    }
}
