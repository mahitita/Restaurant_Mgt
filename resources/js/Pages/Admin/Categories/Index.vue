<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ categories: Array });

const deleteCategory = (id) => {
    if (confirm('Are you sure?')) {
        router.delete(`/admin/categories/${id}`);
    }
};
</script>

<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Categories</h2>
        <router-link href="/admin/categories/create" class="bg-blue-500 text-white px-4 py-2 rounded">New Category</router-link>
        <table class="w-full mt-4 border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Name</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="category in categories" :key="category.id">
                    <td class="p-2 border">{{ category.name }}</td>
                    <td class="p-2 border">
                        <router-link :href="`/admin/categories/${category.id}/edit`" class="text-blue-500">Edit</router-link>
                        <button @click="deleteCategory(category.id)" class="text-red-500 ml-4">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
