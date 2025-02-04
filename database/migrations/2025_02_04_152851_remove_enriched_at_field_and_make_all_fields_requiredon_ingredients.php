<?php

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
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('amount')->change();
            $table->integer('amount_in_grams')->change();

            $table->dropColumn('enriched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('amount')->nullable()->change();
            $table->integer('amount_in_grams')->nullable()->change();

            $table->timestamp('enriched_at')->nullable()->after('amount_in_grams');
        });
    }
};
