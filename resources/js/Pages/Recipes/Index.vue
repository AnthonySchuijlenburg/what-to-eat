<script setup lang="ts">
import {Deferred} from "@inertiajs/vue3";
import RecipeCard from "../../Components/RecipeCard.vue";
import FilterBar from "../../Components/FilterBar.vue";

export interface Recipe {
    id: string
    name: string;
    image_url: string;
    description: string;
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
    recipes?: Recipe[];
    ingredientsToMatch: string[];
    request: object;
    courseFilters: object;
    preparationTimeFilters: object;
    servingSizeFilters: object;
}

defineProps<Props>();
</script>

<template>
    <div>
        <h1 class="mb-10 text-4xl">
            <a href="/recipes">Recipes</a>
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <FilterBar
                :request="request"
                :course-filters="courseFilters"
                :preparation-time-filters="preparationTimeFilters"
                :serving-size-filters="servingSizeFilters"
            />

            <div class="flex flex-col gap-6 w-full lg:col-span-2">
                <Deferred data="recipes">
                    <template #fallback>
                        <span>Loading...</span>
                    </template>
                    <RecipeCard
                        v-for="recipe in recipes"
                        :key="recipe.id"
                        :recipe="recipe"
                        :ingredients-to-match="[]"
                    />
                    <h2 v-if="recipes.length === 0">No Recipes</h2>
                </Deferred>
            </div>
        </div>

    </div>
</template>
