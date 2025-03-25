<script setup lang="ts">
import { Recipe } from '../Pages/Recipes/Index.vue';
import IngredientRow from "./IngredientRow.vue";
interface Props {
    recipe: Recipe;
    ingredientsToMatch: string[];
}

defineProps<Props>();

</script>

<template>
    <a
        :href="'/recipes/'+recipe.id"
        class="border p-4 rounded-xl grid md:grid-cols-2 gap-4 hover:shadow"
    >
        <div class="flex items-center">
            <img loading="lazy" class="w-full min-h-64 object-cover rounded-xl"
                 :src="'/storage/' + recipe.image_url" :alt="recipe.name">
        </div>
        <div>
            <h2 class="text-2xl font-semibold mb-6">{{ recipe.name }}</h2>
            <p class="mb-6">{{ recipe.description }}</p>

            <span>Ingrediënten</span>
            <ul class="ml-6 list-disc">
                <IngredientRow
                    v-for="ingredient in recipe.ingredients"
                    :key="ingredient.id"
                    :ingredient="ingredient"
                    :ingredients-to-match="ingredientsToMatch"
                />
            </ul>
        </div>
    </a>

</template>

<style scoped>

</style>
