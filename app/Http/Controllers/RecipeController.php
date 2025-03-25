<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Recipes/Index', [
            'recipes' => Inertia::defer(fn () => $this->getRecipes(), 'recipes'),
            'ingredientsToMatch' => fn () => $request['ingredients'] ?? [],
            'request' => $request,
            'courseFilters' => fn () => $this->getCourses(),
            'preparationTimeFilters' => fn () => $this->getPrepTimes(),
            'servingSizeFilters' => fn () => $this->getServings(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        $recipe = Recipe::query()
            ->where('id', $id)
            ->first();

        return Inertia::render('Recipes/Show', ['recipe' => $recipe]);
    }

    private function getRecipes(): Collection
    {
        $request = request();
        $name = $request->input('name');
        $course = $request->input('course');
        $preparationTime = $request->input('preparation_time');
        $serves = $request->input('serves');

        $ingredients = $request->input('ingredients', []);

        return Recipe::query()
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', '%'.$name.'%');
            })
            ->when($course, function ($query) use ($course) {
                return $query->whereIn('course', $course);
            })
            ->when($preparationTime, function ($query) use ($preparationTime) {
                return $query->whereIn('preparation_time', $preparationTime);
            })
            ->when($serves, function ($query) use ($serves) {
                return $query->whereIn('serves', $serves);
            })
            ->when($ingredients, function ($query) use ($ingredients) {
                $query->withCount([
                    'ingredients as matching_ingredients_count' => function ($query) use ($ingredients) {
                        $query->where(function ($q) use ($ingredients) {
                            foreach ($ingredients as $ingredient) {
                                $q->orWhere('name', 'like', '%'.strtolower($ingredient).'%');
                            }
                        });
                    },
                ])
                    ->orderByDesc('matching_ingredients_count');

                return $query;
            })
            ->with('ingredients')
            ->latest()
            ->get();
    }

    private function getPrepTimes(): array
    {
        $options = Recipe::query()
            ->select('preparation_time')
            ->distinct()
            ->get()
            ->sortBy('preparation_time')
            ->toArray();

        $options = array_map(fn ($item) => [$item['preparation_time'] => ucfirst($item['preparation_time'])], $options);

        return array_merge(...$options);
    }

    private function getCourses(): array
    {
        $options = Recipe::query()
            ->select('course')
            ->whereNot('course', '=', '')
            ->distinct()
            ->get()
            ->sortBy('course')
            ->toArray();

        $options = array_map(fn ($item) => [$item['course'] => ucfirst($item['course'])], $options);

        return array_merge(...$options);
    }

    private function getServings(): array
    {
        $options = Recipe::query()
            ->select('serves')
            ->whereNot('serves', '=', '')
            ->distinct()
            ->get()
            ->sortBy('serves')
            ->toArray();

        $options = array_map(fn ($item) => [$item['serves'] => ucfirst($item['serves'])], $options);

        return array_merge(...$options);
    }
}
