<script setup lang="ts">
interface Props {
    request: object;
    label: string;
    options: object
    id: string,
}
defineProps<Props>();
</script>

<template>
    <div class="flex flex-col my-4">
        <h3 class="text-xl mb-2">{{label}}</h3>
        <template v-for="(label, option) in options" :key="option">
            <label class="flex items-center gap-2 hover:cursor-pointer">
                <input
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500  focus:ring-2"
                    type="checkbox"
                    :name="id+'[]'"
                    :value="option"
                    :checked="!!request[id] && request[id].includes(option)"
                >
                {{label}}
            </label>
        </template>
    </div>
</template>
