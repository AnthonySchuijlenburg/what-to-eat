@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-4xl">
        <a href="{{ route('recipes.index') }}">Recipes</a>
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <form method="GET" action="{{ route('recipes.index') }}" class="w-full h-fit mb-4 flex flex-col items-start border rounded p-4">
            <h2 class="text-2xl">Filters</h2>
            <livewire:ingredient-list :ingredients="request('ingredients')" />

            <x-filter :options="$filters['courses']" id="courses" label="Soort gerecht" />
            <x-filter :options="$filters['preparation_times']" id="preparation_times"  label="Bereidingstijd" />
            <x-filter :options="$filters['serves']" id="serves" label="Porties" />

            <input type="text" name="name" placeholder="Search by name"
                   value="{{ request('name') }}" class="input h-10" />
            <div class="w-full my-4 flex items-center gap-2">
                <button type="submit" class="btn h-10">Search</button>
                <a href="{{ route('recipes.index') }}" class="btn h-10">Clear</a>
            </div>
        </form>

        <div class="flex flex-col gap-6 w-full lg:col-span-2">
            @forelse($recipes as $recipe)
                <x-recipe-card :recipe="$recipe" />
            @empty
                <h2>No Recipes</h2>
            @endforelse
        </div>
    </div>

@endsection
