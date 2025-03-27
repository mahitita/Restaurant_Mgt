<template>
    <AdminLayout>
      <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-semibold mb-6">Profit Report</h1>

        <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
          {{ flash.success }}
        </div>

        <div class="mb-4 flex justify-between">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search Menu..."
            class="border p-2 rounded-lg w-64"
          />
        </div>

        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-200">
              <th class="p-2">Name</th>
              <th class="p-2">Category</th>
              <th class="p-2">Price</th>
              <th class="p-2">Cost</th>
              <th class="p-2">Profit</th>
              <th class="p-2">Profit Margin (%)</th>
              <th class="p-2">Available</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="menu in filteredMenus" :key="menu.id">
              <td class="p-2">{{ menu.name }}</td>
              <td class="p-2">{{ menu.category }}</td>
              <td class="p-2">${{ menu.price }}</td>
              <td class="p-2">${{ menu.cost }}</td>
              <td class="p-2" :class="{ 'text-green-500': menu.profit > 0, 'text-red-500': menu.profit < 0 }">
                ${{ menu.profit }}
              </td>
              <td class="p-2">{{ menu.profit_margin }}%</td>
              <td class="p-2">{{ menu.available ? 'Yes' : 'No' }}</td>
            </tr>
          </tbody>
        </table>

        <button @click="goToMenus" class="mt-4 text-blue-500">Back to Menu List</button>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    Layout: AdminLayout,
    props: {
      menus: Array,
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
      filteredMenus() {
        return this.menus.filter(menu =>
          menu.name.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      },
    },
    methods: {
      goToMenus() {
        router.get('/admin/menus');
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
  </style>