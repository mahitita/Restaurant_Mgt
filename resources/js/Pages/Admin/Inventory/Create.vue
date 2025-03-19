<template>
    <AdminLayout>
      <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Create New Inventory Item</h1>

        <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
          {{ flash.success }}
        </div>

        <form @submit.prevent="submitForm">
          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Name</label>
            <input v-model="form.name" type="text" class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500" required />
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Quantity</label>
            <input v-model="form.quantity" type="number" class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500" required />
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Unit Cost</label>
            <input v-model="form.unit_cost" type="number" step="0.01" class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500" required />
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Unit</label>
            <select v-model="form.unit" class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500" required>
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
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Threshold</label>
            <input v-model="form.threshold" type="number" class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500" required />
          </div>

          <div class="mb-5">
            <label class="block text-gray-700 font-semibold mb-1">Expiry Date</label>
            <input v-model="form.expiry_date" type="date" class="border-2 border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500" />
          </div>

          <div class="flex">
            <button type="submit" class="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition duration-200">Create Inventory Item</button>
            <button type="button" @click="goToIndex" class="ml-4 text-gray-600 underline">Cancel</button>
          </div>
        </form>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  export default {
    layout: AdminLayout,
    data() {
      return {
        form: {
          name: '',
          quantity: '',
          unit_cost: '',
          unit: 'unit',
          threshold: 5,
          expiry_date: '',
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
        Inertia.post('/admin/inventory', this.form, {
          onSuccess: () => this.resetForm(),
          onError: errors => console.log('Errors:', errors),
        });
      },
      resetForm() {
        this.form = {
          name: '',
          quantity: '',
          unit_cost: '',
          unit: 'unit',
          threshold: 5,
          expiry_date: '',
        };
      },
      goToIndex() {
        Inertia.visit('/admin/inventory');
      },
    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 600px;
  }
  </style>