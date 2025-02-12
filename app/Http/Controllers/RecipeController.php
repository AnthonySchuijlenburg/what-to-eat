<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Factory|Application
    {
        $name = $request->input('name');
        $course = $request->input('course');
        $preparationTime = $request->input('preparation_time');
        $serves = $request->input('serves');

        $ingredients = $request->input('ingredients', []);

        $recipes = Recipe::query()
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

        return view('recipes.index', [
            'recipes' => $recipes,
            'filters' => [
                'courses' => $this->getCourses(),
                'preparation_times' => $this->getPrepTimes(),
                'serves' => $this->getServings(),
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View|Factory|Application
    {
        $recipe = Recipe::query()
            ->where('id', $id)
            ->first();

        return view('recipes.show', ['recipe' => $recipe]);
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
