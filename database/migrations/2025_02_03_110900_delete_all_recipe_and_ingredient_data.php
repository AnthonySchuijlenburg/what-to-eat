<?php

use App\Models\Recipe;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Recipe::query()->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible, all data is lost
    }
};
