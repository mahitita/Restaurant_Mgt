<template>
    <AdminLayout>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
      <h1 class="text-3xl font-semibold mb-6">Table Management</h1>

      <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="bg-red-100 p-3 mb-4 rounded">
        {{ flash.error }}
      </div>

      <div class="flex mb-4 space-x-2">
  <button @click="goToCreate" class="bg-blue-500 text-white p-2 rounded">
    Add New Table
  </button>
  <button @click="goToWaitlists" class="bg-blue-500 text-white p-2 rounded">Manage Waitlist</button>
  <input v-model="searchQuery" type="text" class="border p-2 rounded w-full max-w-md" placeholder="Search by table number..." />
</div>

      <table class="w-full border-collapse shadow-md">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-3 text-left">Table Number</th>
            <th class="p-3 text-left">Seats</th>
            <th class="p-3 text-left">Type</th>
            <th class="p-3 text-left">X</th>
            <th class="p-3 text-left">Y</th>
            <th class="p-3 text-left">Width</th>
            <th class="p-3 text-left">Height</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Reserved Today</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="table in filteredTables" :key="table.id">
            <td class="p-3">{{ table.table_number }}</td>
            <td class="p-3">{{ table.seats }}</td>
            <td class="p-3">{{ table.type }}</td>
            <td class="p-3">{{ table.x_coordinate }}</td>
            <td class="p-3">{{ table.y_coordinate }}</td>
            <td class="p-3">{{ table.width }}</td>
            <td class="p-3">{{ table.height }}</td>
            <td class="p-3">
              <select v-model="table.status" @change="updateStatus(table)" class="border p-1">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="reserved">Reserved</option>
              </select>
            </td>
            <td class="p-3">{{ table.reserved_today ? 'Yes' : 'No' }}</td>
            <td class="p-3">
              <button @click="goToEdit(table.id)" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
              <button @click="deleteTable(table)" class="text-red-600 hover:text-red-800">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    </AdminLayout>
  </template>

  <script>
  import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    layout: AdminLayout,
    props: {
      tables: Array,
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
      filteredTables() {
        if (!this.searchQuery) return this.tables;
        const query = this.searchQuery.toLowerCase();
        return this.tables.filter(table =>
          table.table_number.toString().toLowerCase().includes(query)
        );
      },
    },
    methods: {
      goToCreate() {
        router.get('/admin/tables/create');
      },
      goToWaitlists() {
    router.get('/admin/waitlists');
  },
      goToEdit(tableId) {
        router.get(`/admin/tables/${tableId}/edit`);
      },
      deleteTable(table) {
        if (confirm(`Are you sure you want to delete table ${table.table_number}?`)) {
          Inertia.delete(`/admin/tables/${table.id}`);
        }
      },
      updateStatus(table) {
        Inertia.put(`/admin/tables/${table.id}/status`, {
          status: table.status,
          date: new Date().toISOString().split('T')[0], // Today's date
        }, {
          preserveState: true,
          onError: (errors) => {
            console.log('Status update error:', errors);
            // Revert status on error (e.g., reservation conflict)
            table.status = this.tables.find(t => t.id === table.id).status;
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