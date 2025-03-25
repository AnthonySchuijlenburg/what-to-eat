<script setup lang="ts">
import FilterItem from "./FilterItem.vue";

interface Props {
    request: object;
    courseFilters: object;
    preparationTimeFilters: object;
    servingSizeFilters: object;
}

defineProps<Props>();
</script>

<template>
    <form class="w-full h-fit mb-4 flex flex-col items-start border rounded p-4">
        <h2 class="text-2xl">Filters</h2>

        <FilterItem
            :request="request"
            :options="courseFilters"
            id="courses"
            label="Soort gerecht"
        />
        <FilterItem
            :request="request"
            :options="preparationTimeFilters"
            id="preparation_times"
            label="Bereidingstijd"
        />
        <FilterItem
            :request="request"
            :options="servingSizeFilters"
            id="serves"
            label="Porties"
        />

        <input type="text" name="name" placeholder="Search by name"
               value="" class="input h-10"/>
        <div class="w-full my-4 flex items-center gap-2">
            <button type="submit" class="btn h-10">Search</button>
            <a href="/recipes" class="btn h-10">Clear</a>
        </div>
    </form>
</template>
