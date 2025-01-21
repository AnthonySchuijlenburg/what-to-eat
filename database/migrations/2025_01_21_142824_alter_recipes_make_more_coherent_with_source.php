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
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn([
                'serves',
                'variable_size',
            ]);

            $table->string('serves')->after('steps');
            $table->string('preparation_time')->after('serves');
            $table->string('course')->after('preparation_time');
            $table->string('nutritional_value')->after('course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn([
                'serves',
                'preparation_time',
                'course',
                'nutritional_value',
            ]);

            $table->boolean('variable_size')->after('steps');
            $table->integer('serves')->after('variable_size');
        });
    }
};
