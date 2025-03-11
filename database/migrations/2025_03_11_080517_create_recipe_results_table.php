<?php

use App\Models\Recipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Recipe::class)
                ->after('url')
                ->nullable();

            $table->string('url');
            $table->integer('status_code')->nullable();
            $table->longText('result')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_results');
    }
};
