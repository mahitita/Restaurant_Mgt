<script setup>
import { router } from '@inertiajs/vue3';

defineProps({ menus: Array });

const deleteMenu = (id) => {
    if (confirm('Are you sure?')) {
        router.delete(`/Admin/Menu/${id}`);
    }
};
</script>

<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Menu Items</h2>
        <router-link href="/Admin/Menu/create" class="bg-blue-500 text-white px-4 py-2 rounded">New Menu Item</router-link>
        <table class="w-full mt-4 border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">Name</th>
                    <th class="p-2">Category</th>
                    <th class="p-2">Price</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="menu in menus" :key="menu.id">
                    <td class="p-2 border">{{ menu.name }}</td>
                    <td class="p-2 border">{{ menu.category.name }}</td>
                    <td class="p-2 border">{{ menu.price  }}</td>
                    <td class="p-2 border">
                        <router-link :href="`/Admin/Menu/${menu.id}/edit`" class="text-blue-500">Edit</router-link>
                        <button @click="deleteMenu(menu.id)" class="text-red-500 ml-4">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
