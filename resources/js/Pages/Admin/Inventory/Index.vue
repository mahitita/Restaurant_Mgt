<template>
    <AdminLayout>
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Ingredient Management</h1>

        <!-- Flash Messages -->
        <div v-if="flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
          {{ flash.success }}
        </div>
        <div v-if="flash.error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
          {{ flash.error }}
        </div>

        <!-- Header with Actions -->
        <div class="flex justify-between items-center mb-6">
          <div class="flex space-x-4">
            <button @click="goToCreate" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
              Add New Ingredient
            </button>
            <button @click="goToStockHistory" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
              View Stock History
            </button>
          </div>
          <div class="relative">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search Ingredients..."
              class="border border-gray-300 p-2 rounded-lg w-full max-w-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <span class="absolute top-3 right-3 text-gray-500">🔍</span>
          </div>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-100 text-gray-700 text-left">
                <th class="p-4">Name</th>
                <th class="p-4">Total Quantity</th>
                <th class="p-4">Remaining Quantity</th>
                <th class="p-4">Unit Cost</th>
                <th class="p-4">Unit</th>
                <th class="p-4">Threshold</th>
                <th class="p-4">Expiry Date</th>
                <th class="p-4">Status</th>
                <th class="p-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="inventory in filteredInventories"
                :key="inventory.id"
                :class="{ 'bg-yellow-50': inventory.low_stock && !inventory.expired, 'bg-red-50': inventory.expired }"
                class="border-b hover:bg-gray-50"
              >
                <td class="p-4">{{ inventory.name }}</td>
                <td class="p-4">{{ inventory.quantity }}</td>
                <td class="p-4">{{ inventory.remaining_quantity }}</td>
                <td class="p-4">Br {{ inventory.unit_cost }}</td>
                <td class="p-4">{{ inventory.unit }}</td>
                <td class="p-4">{{ inventory.threshold || '5' }}</td>
                <td class="p-4">{{ inventory.expiry_date || 'N/A' }}</td>
                <td class="p-4">
                  <span
                    :class="{
                      'text-red-600': inventory.expired,
                      'text-yellow-600': inventory.low_stock && !inventory.expired,
                      'text-green-600': !inventory.low_stock && !inventory.expired,
                    }"
                  >
                    {{ inventory.expired ? 'Expired' : inventory.low_stock ? 'Low Stock' : 'Normal' }}
                  </span>
                </td>
                <td class="p-4 flex space-x-2">
                  <button @click="goToEdit(inventory.id)" class="text-blue-600 hover:underline">Edit</button>
                  <button @click="deleteInventory(inventory)" class="text-red-600 hover:underline">Delete</button>
                  <button @click="showAddStockModal(inventory)" class="text-green-600 hover:underline">Add Stock</button>
                  <button @click="goToPurchaseHistory(inventory.id)" class="text-purple-600 hover:underline">Purchase History</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Add Stock Modal -->
        <div v-if="selectedInventory" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Add Stock to {{ selectedInventory.name }}</h2>
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-1">Amount</label>
              <input
                v-model.number="stockForm.amount"
                type="number"
                min="1"
                class="border border-gray-300 p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Amount"
                required
              />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-1">Total Cost (Br)</label>
              <input
                v-model.number="stockForm.total_cost"
                type="number"
                step="0.01"
                min="0"
                class="border border-gray-300 p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Total Cost"
                required
              />
            </div>
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-1">Supplier</label>
              <input
                v-model="stockForm.supplier"
                type="text"
                class="border border-gray-300 p-2 w-full rounded focus:outline-none focus:ring-2 focus:ring-green-500"
                placeholder="Supplier (optional)"
              />
            </div>
            <div class="flex justify-end space-x-2">
              <button @click.prevent="addStock" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                Add Stock
              </button>
              <button @click="closeModal" class="text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { router } from '@inertiajs/vue3';
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
        router.get(route('admin.inventory.create'));
      },
      goToEdit(inventoryId) {
        router.get(route('admin.inventory.edit', inventoryId));
      },
      goToStockHistory() {
        router.get(route('admin.inventory.stock-history'));
      },
      deleteInventory(inventory) {
        if (confirm(`Are you sure you want to delete ${inventory.name}?`)) {
          router.delete(route('admin.inventory.destroy', inventory.id));
        }
      },
      goToPurchaseHistory(inventoryId) {
        router.get(route('admin.inventory.purchase-history', inventoryId));
      },
      showAddStockModal(inventory) {
        this.selectedInventory = inventory;
        this.stockForm = { amount: '', total_cost: '', supplier: '' };
      },
      closeModal() {
        this.selectedInventory = null;
      },
      addStock() {
        const amount = Number(this.stockForm.amount);
        const totalCost = Number(this.stockForm.total_cost);

        if (amount > 0 && totalCost >= 0) {
          const payload = {
            amount: amount,
            total_cost: totalCost,
            supplier: this.stockForm.supplier || null,
          };
          router.post(route('admin.inventory.add-stock', this.selectedInventory.id), payload, {
            onSuccess: () => {
              this.selectedInventory = null;
            },
          });
        } else {
          alert('Please enter a valid amount and total cost.');
        }
      },
    },
  };
  </script>