<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const name = ref('');
const price = ref('');
const category_id = ref('');
const description = ref('');
const image = ref(null);
const errors = ref({});

const categories = defineProps({ categories: Array });

const submit = () => {
    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('price', price.value);
    formData.append('category_id', category_id.value);
    formData.append('description', description.value);
    if (image.value) {
        formData.append('image', image.value);
    }

    router.post('/admin/menu', formData, {
        onError: (err) => errors.value = err
    });
};
</script>

<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Create Menu Item</h2>
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block font-bold">Name:</label>
                <input v-model="name" type="text" class="border p-2 w-full">
                <span v-if="errors.name" class="text-red-500">{{ errors.name }}</span>
            </div>
            <div class="mb-4">
                <label class="block font-bold">Price:</label>
                <input v-model="price" type="number" class="border p-2 w-full">
                <span v-if="errors.price" class="text-red-500">{{ errors.price }}</span>
            </div>
            <div class="mb-4">
                <label class="block font-bold">Category:</label>
                <select v-model="category_id" class="border p-2 w-full">
                    <option v-for="category in categories" :value="category.id" :key="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <span v-if="errors.category_id" class="text-red-500">{{ errors.category_id }}</span>
            </div>
            <div class="mb-4">
                <label class="block font-bold">Description:</label>
                <textarea v-model="description" class="border p-2 w-full"></textarea>
            </div>
            <div class="mb-4">
                <label class="block font-bold">Image:</label>
                <input type="file" @change="event => image.value = event.target.files[0]" class="border p-2 w-full">
            </div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</template>
