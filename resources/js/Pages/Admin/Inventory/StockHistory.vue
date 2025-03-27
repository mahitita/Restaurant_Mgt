<template>
    <AdminLayout>
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Inventory Stock History</h1>

        <!-- Header with Actions -->
        <div class="flex justify-between items-center mb-6">
          <Link :href="route('admin.inventory.index')" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            Back to Inventory
          </Link>
          <div class="relative">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search Inventory..."
              class="border border-gray-300 p-2 rounded-lg w-full max-w-xs focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <span class="absolute top-3 right-3 text-gray-500">🔍</span>
          </div>
        </div>

        <!-- Stock History Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-100 text-gray-700 text-left">
                <th class="p-4">Name</th>
                <th class="p-4">Total Quantity</th>
                <th class="p-4">Remaining Quantity</th>
                <th class="p-4">Initial Stock</th>
                <th class="p-4">Total Stock Added</th>
                <th class="p-4">Unit</th>
                <th class="p-4">Stock History</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="inventory in filteredInventories" :key="inventory.id" class="border-b hover:bg-gray-50">
                <td class="p-4">{{ inventory.name }}</td>
                <td class="p-4">{{ inventory.quantity }}</td>
                <td class="p-4">{{ inventory.remaining_quantity }}</td>
                <td class="p-4">{{ inventory.initial_stock }}</td>
                <td class="p-4">{{ inventory.total_stock_added }}</td>
                <td class="p-4">{{ inventory.unit }}</td>
                <td class="p-4">
                  <ul class="space-y-1">
                    <li
                      v-for="log in inventory.logs"
                      :key="log.created_at"
                      :class="{ 'text-red-600': log.action === 'deducted', 'text-green-600': log.action === 'added' }"
                    >
                      {{ log.action === 'added' ? 'Added' : 'Deducted' }} {{ log.quantity }} units on {{ log.created_at }} (Reason: {{ log.reason }})
                    </li>
                    <li v-if="!inventory.logs.length" class="text-gray-500">No history available</li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  export default {
    layout: AdminLayout,
    components: { Link },
    props: {
      inventories: Array,
    },
    data() {
      return {
        searchQuery: '',
      };
    },
    computed: {
      filteredInventories() {
        if (!this.searchQuery) return this.inventories;
        const query = this.searchQuery.toLowerCase();
        return this.inventories.filter(inventory =>
          inventory.name.toLowerCase().includes(query)
        );
      },
    },
  };
  </script>