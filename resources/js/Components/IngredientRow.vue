<script setup lang="ts">
import { Ingredient } from '../Pages/Recipes/Index.vue';
import {computed} from "vue";
interface Props {
    ingredient: Ingredient;
    ingredientsToMatch: string[];
}
const props = defineProps<Props>();

const match = computed<boolean>(function() {
    for (const ingredient of props.ingredientsToMatch) {
        if (props.ingredient.source.includes(ingredient)) {
            return true
        }
    }

    return false;
});
</script>

<template>
    <li :class="{'text-green-600': match}">{{ingredient.source}}</li>
</template>
