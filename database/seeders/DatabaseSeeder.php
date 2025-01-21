<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (User::all()->count() === 0) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);
        }

        //        $this->call([
        //            RecipeSeeder::class,
        //        ]);
    }
}
