<a href="{{ route('recipes.show', ['recipe' => $recipe->id]) }}" class="border p-4 rounded-xl grid md:grid-cols-2 gap-4 hover:shadow">
    <div class="flex items-center">
        <img loading="lazy" class="w-full min-h-64 object-cover rounded-xl" src="{{config('app.url').'/storage/'.$recipe->image_url}}" alt="{{$recipe->name}}">
    </div>
    <div>
        <h2 class="text-2xl font-semibold mb-6">{{$recipe->name}}</h2>
        <p class="mb-6">{{$recipe->description}}</p>

        <span>Ingrediënten</span>
        <ul class="ml-6 list-disc">
            @foreach($recipe->ingredients as $ingredient)
                <li>{{$ingredient->source}}</li>
            @endforeach
        </ul>
    </div>

</a>
