<?php

use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//    return Inertia::render('Recipes', [
//        'message' => 'This is an Inertia page with Vue!',
//    ]);
// });

Route::get('/', function () {
    return redirect('/recipes');
});

Route::resource('recipes', controller: RecipeController::class)
    ->only(['index', 'show']);
