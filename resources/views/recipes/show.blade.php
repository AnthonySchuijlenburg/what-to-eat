@extends('layouts.app')

@section('content')
        <h1 class="text-2xl">
            {{$recipe->name}}
        </h1>
    <a href="{{ route('recipes.index') }}" class="text-xs text-gray-500 hover:underline">← Terug naar het overzicht</a>
    <img class="my-10 w-full min-h-64 object-cover rounded-xl" src="{{config('app.url').'/storage/'.$recipe->image_url}}" alt="{{$recipe->name}}">

        <p class="text-xl">{{$recipe->description}}</p>
        <div class="mb-6 flex gap-4 w-full justify-between">
            <span>{{$recipe->serves}}</span>
            <span>{{$recipe->preparation_time}}</span>
            <span>{{$recipe->serves}}</span>
            <span>{{$recipe->nutritional_value}}</span>
        </div>

        <h2 class="text-xl">Ingrediënten</h2>
        <ul class="ml-6 mb-6 list-disc grid grid-cols-2">
            @foreach($recipe->ingredients as $ingredient)
                <li>{{$ingredient->source}}</li>
            @endforeach
        </ul>

        <h2 class="text-xl">Bereidingswijze</h2>
        <ol class="ml-6 list-decimal">
            @foreach($recipe->steps as $step)
                <li>{{$step}}</li>
            @endforeach
        </ol>

@endsection
