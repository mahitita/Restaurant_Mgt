<template>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
      <h1 class="text-3xl font-semibold mb-6">Edit User: {{ user.name }}</h1>

      <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
        {{ flash.success }}
      </div>

      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label class="block text-gray-700">Name</label>
          <input v-model="form.name" type="text" class="border p-2 w-full" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Email (Optional)</label>
          <input v-model="form.email" type="email" class="border p-2 w-full" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Phone</label>
          <input v-model="form.phone" type="text" class="border p-2 w-full" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Password (Leave blank to keep current)</label>
          <input v-model="form.password" type="password" class="border p-2 w-full" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Role</label>
          <select v-model="form.role" class="border p-2 w-full" required>
            <option value="customer">Customer</option>
            <option value="manager">Manager</option>
            <option value="waiter">Waiter</option>
            <option value="chef">Chef</option>
            <option value="cashier">Cashier</option>
          </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition duration-300">Update User</button>
        <button type="button" @click="goToIndex" class="ml-2 bg-gray-300 text-gray-700 p-2 rounded hover:bg-gray-400">Cancel</button>
      </form>
    </div>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    layout: AdminLayout,
    props: {
      user: Object,
    },
    data() {
      return {
        form: { ...this.user, password: '' },
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
    methods: {
      submitForm() {
        Inertia.put(`/admin/users/${this.user.id}`, this.form);
      },
      goToIndex() {
        Inertia.visit('/admin/users');
      },
    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 600px; /* Adjust as necessary */
  }
  </style>