<!-- resources/js/Pages/Admin/Stocks/Create.vue -->
<template>
    <div class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50">
      <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Stock Item</h2>
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              placeholder="Enter item name"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition"
              :class="{ 'border-red-500': form.errors.name }"
            />
            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
          </div>
          <div class="mb-4">
            <label for="quantity" class="block text-gray-700 font-semibold mb-2">Quantity</label>
            <input
              id="quantity"
              type="number"
              v-model.number="form.quantity"
              min="0"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition"
              :class="{ 'border-red-500': form.errors.quantity }"
            />
            <p v-if="form.errors.quantity" class="text-red-500 text-sm mt-1">{{ form.errors.quantity }}</p>
          </div>
          <div class="mb-4">
            <label for="price" class="block text-gray-700 font-semibold mb-2">Price</label>
            <input
              id="price"
              type="number"
              step="0.01"
              v-model.number="form.price"
              min="0"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition"
              :class="{ 'border-red-500': form.errors.price }"
            />
            <p v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</p>
          </div>
          <div class="mb-4">
            <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              placeholder="Enter description (optional)"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition"
              :class="{ 'border-red-500': form.errors.description }"
            ></textarea>
            <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="$emit('close')"
              class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
              :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            >
              {{ form.processing ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>

  <script setup>
  import { useForm } from '@inertiajs/vue3';

  const emit = defineEmits(['close']);

  const form = useForm({
    name: '',
    quantity: 0,
    price: 0,
    description: '',
  });

  const submit = () => {
    form.post(route('admin.stocks.store'), {
      onSuccess: () => {
        form.reset();
        emit('close');
      },
    });
  };
  </script>