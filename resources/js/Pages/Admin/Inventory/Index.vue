<template>
    <AdminLayout>
      <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-semibold mb-6">Inventory Management</h1>

        <!-- Flash messages -->
        <div v-if="flash.success" class="bg-green-100 p-3 mb-4 text-green-800 rounded-md">
          {{ flash.success }}
        </div>
        <div v-if="flash.error" class="bg-red-100 p-3 mb-4 text-red-800 rounded-md">
          {{ flash.error }}
        </div>

        <div class="flex justify-between mb-4">
          <button @click="goToCreate" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-300">
            Add New Inventory Item
          </button>
          <div class="relative">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search Inventory..."
              class="border p-2 rounded-lg w-64"
            />
            <button class="absolute top-2 right-2 text-gray-500">🔍</button>
          </div>
        </div>

        <!-- Inventory Table -->
        <table class="w-full table-auto bg-gray-50 shadow-md rounded-lg">
          <thead class="bg-gray-200">
            <tr>
              <th class="p-3 text-left">Name</th>
              <th class="p-3 text-left">Quantity</th>
              <th class="p-3 text-left">Unit Cost</th>
              <th class="p-3 text-left">Unit</th>
              <th class="p-3 text-left">Threshold</th>
              <th class="p-3 text-left">Expiry Date</th>
              <th class="p-3 text-left">Status</th>
              <th class="p-3 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="inventory in filteredInventory" :key="inventory.id" :class="{'bg-yellow-100': inventory.low_stock, 'bg-red-100': inventory.expired}">
              <td class="p-3">{{ inventory.name }}</td>
              <td class="p-3">{{ inventory.quantity }}</td>
              <td class="p-3">${{ inventory.unit_cost }}</td>
              <td class="p-3">{{ inventory.unit }}</td>
              <td class="p-3">{{ inventory.threshold }}</td>
              <td class="p-3">{{ inventory.expiry_date || 'N/A' }}</td>
              <td class="p-3">
                <span v-if="inventory.expired" class="text-red-600">Expired</span>
                <span v-else-if="inventory.low_stock" class="text-yellow-600">Low Stock</span>
                <span v-else class="text-green-600">Normal</span>
              </td>
              <td class="p-3 flex space-x-2">
                <button @click="goToEdit(inventory.id)" class="text-blue-600 hover:text-blue-800">Edit</button>
                <button @click="deleteInventory(inventory)" class="text-red-600 hover:text-red-800">Delete</button>
                <button @click="showAddStockModal(inventory)" class="text-green-600 hover:text-green-800">Add Stock</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Add Stock Modal -->
        <div v-if="selectedInventory" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
          <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Add Stock to {{ selectedInventory.name }}</h2>
            <input v-model="stockAmount" type="number" min="1" class="border p-2 mb-4 w-full" placeholder="Amount" />
            <div class="flex justify-end space-x-2">
              <button @click="addStock" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">Add</button>
              <button @click="selectedInventory = null" class="bg-gray-600 text-white py-2 px-4 rounded hover:bg-gray-700">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  export default {
    props: {
      inventories: Array,
    },
    components: {
      AdminLayout,
    },
    data() {
      return {
        selectedInventory: null,
        stockAmount: '',
        searchQuery: '',
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
      filteredInventory() {
        return this.inventories.filter(inventory =>
          inventory.name.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      },
    },
    methods: {
      goToCreate() {
        Inertia.visit('/admin/inventory/create');
      },
      goToEdit(inventoryId) {
        Inertia.visit(`/admin/inventory/${inventoryId}/edit`);
      },
      deleteInventory(inventory) {
        if (confirm(`Delete ${inventory.name}?`)) {
          Inertia.delete(`/admin/inventory/${inventory.id}`);
        }
      },
      showAddStockModal(inventory) {
        this.selectedInventory = inventory;
        this.stockAmount = '';
      },
      addStock() {
        if (this.stockAmount > 0) {
          Inertia.post(`/admin/inventory/${this.selectedInventory.id}/add-stock`, {
            amount: this.stockAmount,
          }, {
            onSuccess: () => this.selectedInventory = null,
          });
        }
      },
    },
  };
  </script>

  <style scoped>
  .alert-success {
    @apply bg-green-100 text-green-700 p-3 rounded mb-4;
  }

  .alert-error {
    @apply bg-red-100 text-red-700 p-3 rounded mb-4;
  }

  .btn-primary {
    @apply bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition;
  }

  .btn-secondary {
    @apply text-blue-500 hover:underline;
  }

  .btn-danger {
    @apply text-red-500 hover:underline;
  }

  .btn-success {
    @apply text-green-500 hover:underline;
  }

  .btn-cancel {
    @apply text-gray-500 hover:underline;
  }

  .badge-danger {
    @apply bg-red-500 text-white px-2 py-1 rounded;
  }

  .badge-warning {
    @apply bg-yellow-500 text-white px-2 py-1 rounded;
  }

  .badge-success {
    @apply bg-green-500 text-white px-2 py-1 rounded;
  }

  .modal-overlay {
    @apply fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50;
  }

  .modal-content {
    @apply bg-white p-8 rounded-lg shadow-lg w-full max-w-lg;
  }
  </style>