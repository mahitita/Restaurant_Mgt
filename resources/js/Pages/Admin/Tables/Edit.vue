<!-- resources/js/Pages/Admin/Tables/Edit.vue -->
<template>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
      <h1 class="text-3xl font-semibold mb-6">Edit Table</h1>

      <div v-if="$page.props.flash?.success" class="bg-green-100 p-3 mb-4 rounded">
        {{ $page.props.flash.success }}
      </div>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-gray-700">Table Number</label>
          <input
            v-model="form.table_number"
            type="text"
            class="border p-2 w-full"
            required
            :class="{ 'border-red-500': form.errors.table_number }"
          />
          <p v-if="form.errors.table_number" class="text-red-500 text-sm mt-1">{{ form.errors.table_number }}</p>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Seats</label>
          <input
            v-model.number="form.seats"
            type="number"
            min="1"
            class="border p-2 w-full"
            required
            :class="{ 'border-red-500': form.errors.seats }"
          />
          <p v-if="form.errors.seats" class="text-red-500 text-sm mt-1">{{ form.errors.seats }}</p>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">X Coordinate</label>
          <input
            v-model.number="form.x_coordinate"
            type="number"
            class="border p-2 w-full"
            required
            :class="{ 'border-red-500': form.errors.x_coordinate }"
          />
          <p v-if="form.errors.x_coordinate" class="text-red-500 text-sm mt-1">{{ form.errors.x_coordinate }}</p>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Y Coordinate</label>
          <input
            v-model.number="form.y_coordinate"
            type="number"
            class="border p-2 w-full"
            required
            :class="{ 'border-red-500': form.errors.y_coordinate }"
          />
          <p v-if="form.errors.y_coordinate" class="text-red-500 text-sm mt-1">{{ form.errors.y_coordinate }}</p>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Width (px, min 50)</label>
          <input
            v-model.number="form.width"
            type="number"
            min="50"
            class="border p-2 w-full"
            required
            :class="{ 'border-red-500': form.errors.width }"
          />
          <p v-if="form.errors.width" class="text-red-500 text-sm mt-1">{{ form.errors.width }}</p>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Height (px, min 50)</label>
          <input
            v-model.number="form.height"
            type="number"
            min="50"
            class="border p-2 w-full"
            required
            :class="{ 'border-red-500': form.errors.height }"
          />
          <p v-if="form.errors.height" class="text-red-500 text-sm mt-1">{{ form.errors.height }}</p>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Type</label>
          <select v-model="form.type" class="border p-2 w-full" required :class="{ 'border-red-500': form.errors.type }">
            <option value="rectangle">Rectangle</option>
            <option value="round">Round</option>
            <option value="oval">Oval</option>
            <option value="square">Square</option>
          </select>
          <p v-if="form.errors.type" class="text-red-500 text-sm mt-1">{{ form.errors.type }}</p>
        </div>
        <button
          type="submit"
          :disabled="form.processing"
          class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition duration-300"
          :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
        >
          {{ form.processing ? 'Updating...' : 'Update Table' }}
        </button>
        <button
          type="button"
          @click="router.get('/admin/tables')"
          class="ml-2 text-gray-500"
          :disabled="form.processing"
        >
          Cancel
        </button>
      </form>
    </div>
  </template>

  <script setup>
  import { useForm, router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  // Define props
  const props = defineProps({
    table: Object,
  });

  // Set the layout
  defineOptions({
    layout: AdminLayout,
  });

  // Initialize the form with the table data
  const form = useForm({
    table_number: props.table.table_number,
    seats: props.table.seats,
    x_coordinate: props.table.x_coordinate,
    y_coordinate: props.table.y_coordinate,
    width: props.table.width,
    height: props.table.height,
    type: props.table.type,
  });

  const submit = () => {
    form.put(`/admin/tables/${props.table.id}`, {
      onSuccess: () => router.get('/admin/tables'),
    });
  };
  </script>

  <style scoped>
  .container {
    max-width: 600px; /* Adjust as necessary */
  }
  </style>