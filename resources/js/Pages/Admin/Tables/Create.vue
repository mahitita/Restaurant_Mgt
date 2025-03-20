<template>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
      <h1 class="text-3xl font-semibold mb-6">Create New Table</h1>

      <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
        {{ flash.success }}
      </div>

      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label class="block text-gray-700">Table Number</label>
          <input v-model="form.table_number" type="text" class="border p-2 w-full" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Seats</label>
          <input v-model.number="form.seats" type="number" min="1" class="border p-2 w-full" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Type</label>
          <select v-model="form.type" class="border p-2 w-full" required>
            <option value="rectangle">Rectangle</option>
            <option value="round">Round</option>
            <option value="oval">Oval</option>
            <option value="square">Square</option>
          </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition duration-300">Create Table</button>
        <button type="button" @click="goToIndex" class="ml-2 text-gray-500">Cancel</button>
      </form>
    </div>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    layout: AdminLayout,
    data() {
      return {
        form: {
          table_number: '',
          seats: '',
          type: 'rectangle',
        },
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
    methods: {
      submitForm() {
        Inertia.post('/admin/tables', this.form, {
          onSuccess: () => this.resetForm(),
        });
      },
      resetForm() {
        this.form = {
          table_number: '',
          seats: '',
          type: 'rectangle',
        };
      },
      goToIndex() {
        Inertia.visit('/admin/tables');
      },
    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 600px; /* Adjust as necessary */
  }
  </style>