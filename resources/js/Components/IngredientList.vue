<script setup lang="ts">
import {onMounted, ref} from "vue";

interface Props {
    request: object;
}

const props = defineProps<Props>();

const items = ref<string[]>([]);
const item = ref<string>('');

onMounted(() => {
    if (!!props.request['ingredients']) {
        items.value = props.request['ingredients'];
    }
})

function addItem() {
    if ('' !== item.value) {
        items.value.push(item.value);
        item.value = '';
    }
}

function removeItem(index: number) {
    items.value = items.value.filter((_, i) => index !== i);
}
</script>
<template>
    <div class="w-full">
        <div class="w-full flex justify-between items-end gap-2">
            <input v-model="item" type="text" class="w-full border rounded px-2 py-1">
            <button @click="addItem" type="button" class="bg-blue-500 text-white px-3 py-1 rounded">Add</button>
        </div>

        <ul>
            <li v-for="(item, index) in items" :key="index" class="flex justify-between bg-gray-100 p-2 my-1 rounded">
                {{ item }}
                <button type="button" @click="removeItem(index)" class="text-red-500">X</button>

                <!-- Hidden input to submit items as part of the form -->
                <input type="hidden" name="ingredients[]" :value="item">
            </li>
        </ul>
    </div>
</template>
