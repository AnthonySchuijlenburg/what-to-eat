<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::all()->count() === 0) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);
        }
    }
}
