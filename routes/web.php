<?php

use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/recipes');
});

Route::resource('recipes', controller: RecipeController::class)
    ->only(['index', 'show']);
