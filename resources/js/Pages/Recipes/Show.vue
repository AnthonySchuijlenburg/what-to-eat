<script setup lang="ts">
export interface Recipe {
    id: string
    name: string;
    image_url: string;
    description: string;
    serves: string;
    preparation_time: string;
    nutritional_value: string;
    steps: string[];
    ingredients: Ingredient[];
}

export interface Ingredient {
    id: string;
    recipe_id: string;
    created_at: string;
    updated_at: string;
    source: string;
}

interface Props {
    recipe: Recipe;
}

defineProps<Props>();
</script>

<template>
    <div>
        <h1 class="text-2xl">
            {{recipe.name}}
        </h1>
        <a href="{{ route('recipes.index') }}" class="text-xs text-gray-500 hover:underline">← Terug naar het overzicht</a>
        <img class="my-10 w-full min-h-64 object-cover rounded-xl" :src="'/storage/' + recipe.image_url" alt="{{recipe.name}}">

        <p class="text-xl">{{recipe.description}}</p>
        <div class="mb-6 flex gap-4 w-full justify-between">
            <span>{{recipe.serves}}</span>
            <span>{{recipe.preparation_time}}</span>
            <span>{{recipe.nutritional_value}}</span>
        </div>

        <h2 class="text-xl">Ingrediënten</h2>
        <ul class="ml-6 mb-6 list-disc grid grid-cols-2">
            <li
                v-for="ingredient in recipe.ingredients"
                :key="ingredient.id"
            >
                {{ingredient.source}}
            </li>
        </ul>

        <h2 class="text-xl">Bereidingswijze</h2>
        <ol class="ml-6 list-decimal">
            <li v-for="step in recipe.steps" :key="step">{{step}}</li>
        </ol>
    </div>
</template>
