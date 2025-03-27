<template>

      <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Create New Ingridient </h1>

        <div v-if="$page.props.flash?.success" class="bg-green-100 p-3 mb-4 rounded">
          {{ $page.props.flash.success }}
        </div>

        <form @submit.prevent="submit">
          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
              required
              :class="{ 'border-red-500': form.errors.name }"
            />
            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Quantity</label>
            <input
              v-model.number="form.quantity"
              type="number"
              class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
              required
              :class="{ 'border-red-500': form.errors.quantity }"
            />
            <p v-if="form.errors.quantity" class="text-red-500 text-sm mt-1">{{ form.errors.quantity }}</p>
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Unit Cost</label>
            <input
              v-model.number="form.unit_cost"
              type="number"
              step="0.01"
              class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
              required
              :class="{ 'border-red-500': form.errors.unit_cost }"
            />
            <p v-if="form.errors.unit_cost" class="text-red-500 text-sm mt-1">{{ form.errors.unit_cost }}</p>
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Unit</label>
            <select
              v-model="form.unit"
              class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
              required
              :class="{ 'border-red-500': form.errors.unit }"
            >
              <option value="unit">Unit</option>
              <option value="kg">Kilogram</option>
              <option value="liter">Liter</option>
              <option value="g">Gram</option>
              <option value="ml">Milliliter</option>
              <option value="box">Box</option>
              <option value="bag">Bag</option>
              <option value="bottle">Bottle</option>
              <option value="carton">Carton</option>
              <option value="pack">Pack</option>
            </select>
            <p v-if="form.errors.unit" class="text-red-500 text-sm mt-1">{{ form.errors.unit }}</p>
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Threshold</label>
            <input
              v-model.number="form.threshold"
              type="number"
              class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
              required
              :class="{ 'border-red-500': form.errors.threshold }"
            />
            <p v-if="form.errors.threshold" class="text-red-500 text-sm mt-1">{{ form.errors.threshold }}</p>
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Expiry Date</label>
            <input
              v-model="form.expiry_date"
              type="date"
              class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
              :class="{ 'border-red-500': form.errors.expiry_date }"
            />
            <p v-if="form.errors.expiry_date" class="text-red-500 text-sm mt-1">{{ form.errors.expiry_date }}</p>
          </div>

          <div class="flex">
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition duration-200"
              :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            >
              {{ form.processing ? 'Creating...' : 'Create Ingridient' }}
            </button>
            <button
              type="button"
              @click="router.get('/admin/inventory')"
              class="ml-4 text-gray-600 underline"
              :disabled="form.processing"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>

  </template>

  <script setup>
  import { useForm, router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  defineOptions({
    layout: AdminLayout,
  });

  const form = useForm({
    name: '',
    quantity: '',
    unit_cost: '',
    unit: 'unit',
    threshold: 5,
    expiry_date: '',
  });

  const submit = () => {
    form.post('/admin/inventory', {
      onSuccess: () => {
        form.reset();
        router.get('/admin/inventory'); // Redirect to index on success
      },
    });
  };
  </script>

  <style scoped>
  .container {
    max-width: 600px;
  }
  </style>