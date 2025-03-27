<!-- resources/js/Pages/Admin/Stocks/Index.vue -->
<template>
    <AdminLayout>
      <div class="container mx-auto py-8 px-4">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Stock Inventory</h1>
            <button
              @click="showCreateModal = true"
              class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
            >
              + Add Stock Item
            </button>
          </div>

          <!-- Success Message -->
          <transition name="fade">
            <div
              v-if="$page.props.flash?.success"
              class="p-4 bg-green-100 text-green-800 rounded-lg mb-6 flex justify-between items-center"
            >
              <span>{{ $page.props.flash.success }}</span>
              <button @click="$page.props.flash.success = null" class="text-green-800 hover:text-green-600">
                ✕
              </button>
            </div>
          </transition>

          <!-- Stock Table -->
          <div class="overflow-x-auto">
            <table class="w-full border-collapse">
              <thead>
                <tr class="bg-gray-50 text-gray-700">
                  <th class="p-4 text-left font-semibold">#</th>
                  <th class="p-4 text-left font-semibold">Name</th>
                  <th class="p-4 text-left font-semibold">Quantity</th>
                  <th class="p-4 text-left font-semibold">Price</th>
                  <th class="p-4 text-left font-semibold">Description</th>
                  <th class="p-4 text-left font-semibold">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(stock, index) in stocks"
                  :key="stock.id"
                  class="border-b hover:bg-gray-50 transition"
                >
                  <td class="p-4 text-gray-600">{{ index + 1 }}</td>
                  <td class="p-4 text-gray-800">{{ stock.name }}</td>
                  <td class="p-4 text-gray-800">{{ stock.quantity }}</td>
                  <td class="p-4 text-gray-800">Br {{ stock.price }}</td>
                  <td class="p-4 text-gray-600">{{ stock.description || 'N/A' }}</td>
                  <td class="p-4">
                    <button
                      @click="editStock(stock)"
                      class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition mr-2"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteStock(stock.id)"
                      class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
                <tr v-if="stocks.length === 0">
                  <td colspan="6" class="p-4 text-center text-gray-500">No stock items found.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Create Stock Modal -->
          <CreateForm v-if="showCreateModal" @close="showCreateModal = false" />

          <!-- Edit Stock Modal -->
          <EditForm v-if="showEditModal" :stock="selectedStock" @close="showEditModal = false" />
        </div>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import CreateForm from './Create.vue';
  import EditForm from './Edit.vue';

  defineProps({
    stocks: Array,
  });

  const showCreateModal = ref(false);
  const showEditModal = ref(false);
  const selectedStock = ref(null);

  const editStock = (stock) => {
    selectedStock.value = stock;
    showEditModal.value = true;
  };

  const deleteStock = (id) => {
    if (confirm('Are you sure you want to delete this stock item?')) {
      router.delete(`/admin/stocks/${id}`, {
        onSuccess: () => {
          // Success message is handled by the backend redirect
        },
      });
    }
  };
  </script>

  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.5s;
  }
  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }

  tr:hover {
    background-color: #f9fafb;
  }
  </style>