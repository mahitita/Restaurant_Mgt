<template>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
      <h1 class="text-3xl font-semibold mb-6">User Management</h1>

      <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="bg-red-100 p-3 mb-4 rounded">
        {{ flash.error }}
      </div>

      <div class="flex mb-4 space-x-2">
        <button @click="goToCreate" class="bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition duration-300">
          Add New User
        </button>
        <input
          v-model="searchQuery"
          type="text"
          class="border p-2 rounded w-full max-w-md"
          placeholder="Search by name, email, or phone..."
        />
      </div>

      <table class="w-full border-collapse shadow-md">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Email</th>
            <th class="p-3 text-left">Phone</th>
            <th class="p-3 text-left">Role</th>
            <th class="p-3 text-left">Created At</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in filteredUsers" :key="user.id">
            <td class="p-3">{{ user.name }}</td>
            <td class="p-3">{{ user.email || 'N/A' }}</td>
            <td class="p-3">{{ user.phone }}</td>
            <td class="p-3">
              <select v-model="user.role" @change="updateRole(user)" class="border p-1 rounded">
                <option value="customer">Customer</option>
                <option value="manager">Manager</option>
                <option value="waiter">Waiter</option>
                <option value="chef">Chef</option>
                <option value="cashier">Cashier</option>
              </select>
            </td>
            <td class="p-3">{{ user.created_at }}</td>
            <td class="p-3">
              <button @click="goToEdit(user.id)" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
              <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    layout: AdminLayout,
    props: {
      users: Array,
    },
    data() {
      return {
        searchQuery: '',
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
      filteredUsers() {
        if (!this.searchQuery) return this.users;
        const query = this.searchQuery.toLowerCase();
        return this.users.filter(user =>
          user.name.toLowerCase().includes(query) ||
          (user.email && user.email.toLowerCase().includes(query)) ||
          user.phone.toLowerCase().includes(query)
        );
      },
    },
    methods: {
      goToCreate() {
        Inertia.visit('/admin/users/create');
      },
      goToEdit(userId) {
        Inertia.visit(`/admin/users/${userId}/edit`);
      },
      deleteUser(user) {
        if (confirm(`Are you sure you want to delete ${user.name}?`)) {
          Inertia.delete(`/admin/users/${user.id}`);
        }
      },
      updateRole(user) {
        Inertia.put(`/admin/users/${user.id}`, { role: user.role }, {
          preserveState: true,
          onError: (errors) => {
            console.log('Role update error:', errors);
            user.role = this.users.find(u => u.id === user.id).role; // Revert on error
          },
        });
      },
    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 1200px; /* Adjust as necessary */
  }
  </style>