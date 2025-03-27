<template>
    <AdminLayout>
      <div class="container mx-auto p-6 bg-gray-50 min-h-screen">
        <div class="bg-white p-6 rounded-xl shadow-md">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Profit Report</h1>
            <button @click="goToMenus" class="text-orange-600 hover:text-orange-700 font-medium transition-colors">
              Back to Menu List
            </button>
          </div>

          <!-- Flash Message -->
          <transition name="fade">
            <div v-if="flashMessage.success" class="p-4 bg-green-50 text-green-700 rounded-lg mb-6 shadow-sm flex justify-between items-center">
              <span>{{ flashMessage.success }}</span>
              <button @click="clearFlash" class="text-green-700 hover:text-green-900">✕</button>
            </div>
          </transition>

          <!-- Search Bar -->
          <div class="mb-6">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search Menu..."
              class="w-full sm:w-72 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all"
            />
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm text-gray-700">
              <thead>
                <tr class="bg-gray-100 text-gray-800">
                  <th class="p-4 text-left font-semibold">Name</th>
                  <th class="p-4 text-left font-semibold">Category</th>
                  <th class="p-4 text-left font-semibold">Price</th>
                  <th class="p-4 text-left font-semibold">Cost</th>
                  <th class="p-4 text-left font-semibold">Profit</th>
                  <th class="p-4 text-left font-semibold">Profit Margin</th>
                  <th class="p-4 text-left font-semibold">Available</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="menu in filteredMenus"
                  :key="menu.id"
                  class="border-b hover:bg-gray-50 transition-colors"
                >
                  <td class="p-4">{{ menu.name }}</td>
                  <td class="p-4 capitalize">{{ menu.category }}</td>
                  <td class="p-4">${{ Number(menu.price).toFixed(2) }}</td>
                  <td class="p-4">${{ Number(menu.cost).toFixed(2) }}</td>
                  <td
                    class="p-4 font-medium"
                    :class="{ 'text-green-600': menu.profit > 0, 'text-red-600': menu.profit < 0 }"
                  >
                    ${{ Number(menu.profit).toFixed(2) }}
                  </td>
                  <td class="p-4">{{ Number(menu.profit_margin).toFixed(1) }}%</td>
                  <td class="p-4">
                    <span
                      class="px-2 py-1 rounded-full text-xs font-medium"
                      :class="menu.available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                    >
                      {{ menu.available ? 'Yes' : 'No' }}
                    </span>
                  </td>
                </tr>
                <tr v-if="!filteredMenus.length">
                  <td colspan="7" class="p-4 text-center text-gray-500">No menu items found.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { ref, computed } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  const props = defineProps({
    menus: Array,
  });

  const searchQuery = ref('');
  const page = usePage();

  // Make flash reactive with a computed property
  const flashMessage = computed(() => page.props.flash || { success: null });

  // Clear flash message
  const clearFlash = () => {
    page.props.flash.success = null;
  };

  const filteredMenus = computed(() => {
    return props.menus.filter(menu =>
      menu.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });

  const goToMenus = () => {
    router.get('/admin/menus');
  };
  </script>

  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  </style>