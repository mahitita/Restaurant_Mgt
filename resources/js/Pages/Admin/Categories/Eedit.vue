<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ category: Object });
const name = ref(props.category.name);
const errors = ref({});

const updateCategory = () => {
    router.put(`/admin/categories/${props.category.id}`, { name: name.value }, {
        onError: (err) => errors.value = err
    });
};
</script>

<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Edit Category</h2>
        <form @submit.prevent="updateCategory">
            <div class="mb-4">
                <label class="block font-bold">Category Name:</label>
                <input v-model="name" type="text" class="border p-2 w-full">
                <span v-if="errors.name" class="text-red-500">{{ errors.name }}</span>
            </div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
</template>
