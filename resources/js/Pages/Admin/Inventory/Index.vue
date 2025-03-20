<template>
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Inventory Management</h1>

      <div v-if="flash.success" class="bg-green-100 p-2 mb-4 rounded">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="bg-red-100 p-2 mb-4 rounded">
        {{ flash.error }}
      </div>

      <div class="flex justify-between mb-4">
        <button @click="goToCreate" class="bg-blue-500 text-white p-2 rounded">
          Add New Inventory Item
        </button>
        <div class="relative">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search Menu..."
              class="border p-2 rounded-lg w-full max-w-xs"
            />
            <button class="absolute top-2 right-2 text-gray-500">🔍</button>
          </div>
      </div>

      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2">Name</th>
            <th class="p-2">Quantity</th>
            <th class="p-2">Unit Cost</th>
            <th class="p-2">Unit</th>
            <th class="p-2">Threshold</th>
            <th class="p-2">Expiry Date</th>
            <th class="p-2">Status</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="inventory in filteredInventories"
            :key="inventory.id"
            :class="{ 'bg-yellow-100': inventory.low_stock, 'bg-red-100': inventory.expired }"
          >
            <td class="p-2">{{ inventory.name }}</td>
            <td class="p-2">{{ inventory.quantity }}</td>
            <td class="p-2">${{ inventory.unit_cost }}</td>
            <td class="p-2">{{ inventory.unit }}</td>
            <td class="p-2">{{ inventory.threshold }}</td>
            <td class="p-2">{{ inventory.expiry_date || 'N/A' }}</td>
            <td class="p-2">
              <span v-if="inventory.expired">Expired</span>
              <span v-else-if="inventory.low_stock">Low Stock</span>
              <span v-else>Normal</span>
            </td>
            <td class="p-2">
              <button @click="goToEdit(inventory.id)" class="text-blue-500 mr-2">Edit</button>
              <button @click="deleteInventory(inventory)" class="text-red-500 mr-2">Delete</button>
              <button @click="showAddStockModal(inventory)" class="text-green-500 mr-2">Add Stock</button>
              <button @click="goToPurchaseHistory(inventory.id)" class="text-purple-500">History</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Add Stock Modal -->
      <div v-if="selectedInventory" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded w-96">
          <h2 class="text-xl mb-4">Add Stock to {{ selectedInventory.name }}</h2>
          <div class="mb-4">
            <label class="block text-gray-700">Amount</label>
            <input
              v-model.number="stockForm.amount"
              type="number"
              min="1"
              class="border p-2 w-full"
              placeholder="Amount"
              required
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Total Cost ($)</label>
            <input
              v-model.number="stockForm.total_cost"
              type="number"
              step="0.01"
              min="0"
              class="border p-2 w-full"
              placeholder="Total Cost"
              required
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Supplier</label>
            <input
              v-model="stockForm.supplier"
              type="text"
              class="border p-2 w-full"
              placeholder="Supplier (optional)"
            />
          </div>
          <div class="flex justify-end">
            <button
              @click.prevent="addStock"
              class="bg-green-500 text-white p-2 rounded mr-2"
            >
              Add
            </button>
            <button
              @click="closeModal"
              class="text-gray-500 p-2"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    layout: AdminLayout,
    props: {
      inventories: Array,
    },
    data() {
      return {
        selectedInventory: null,
        stockForm: {
          amount: '',
          total_cost: '',
          supplier: '',
        },
        searchQuery: '',
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
      filteredInventories() {
        if (!this.searchQuery) return this.inventories;
        const query = this.searchQuery.toLowerCase();
        return this.inventories.filter(inventory =>
          inventory.name.toLowerCase().includes(query)
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
        if (confirm(`Are you sure you want to delete ${inventory.name}?`)) {
          Inertia.delete(`/admin/inventory/${inventory.id}`);
        }
      },
      goToPurchaseHistory(inventoryId) {
        Inertia.visit(`/admin/inventory/${inventoryId}/purchase-history`);
      },
      showAddStockModal(inventory) {
        console.log('Opening modal for:', inventory.name);
        this.selectedInventory = inventory;
        this.stockForm = { amount: '', total_cost: '', supplier: '' };
      },
      closeModal() {
        console.log('Closing modal');
        this.selectedInventory = null;
      },
      addStock() {
        console.log('Add button clicked with:', this.stockForm);
        const amount = Number(this.stockForm.amount);
        const totalCost = Number(this.stockForm.total_cost);

        if (amount > 0 && totalCost >= 0) {
          const payload = {
            amount: amount,
            total_cost: totalCost,
            supplier: this.stockForm.supplier || null,
          };
          console.log('Sending POST request:', {
            url: `/admin/inventory/${this.selectedInventory.id}/add-stock`,
            data: payload,
          });
          Inertia.post(`/admin/inventory/${this.selectedInventory.id}/add-stock`, payload, {
            onSuccess: () => {
              console.log('Stock added successfully');
              this.selectedInventory = null;
            },
            onError: (errors) => {
              console.log('Errors:', errors);
            },
            onFinish: () => {
              console.log('Request finished');
            },
          });
        } else {
          console.log('Validation failed:', { amount, totalCost });
        }
      },
    },
  };
  </script>