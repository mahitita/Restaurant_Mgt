<template>
    <AdminLayout>
      <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-bold mb-6">Menu Management</h1>

        <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
          {{ flash.success }}
        </div>

        <div class="flex justify-between mb-4">
          <button @click="goToCreate" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-300">
            Add New Menu Item
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

        <table class="w-full border-collapse bg-gray-50 shadow-md rounded-lg">
          <thead>
            <tr class="bg-gray-200">
              <th class="p-3 text-left">Name</th>
              <th class="p-3 text-left">Category</th>
              <th class="p-3 text-left">Price</th>
              <th class="p-3 text-left">Prep Time</th>
              <th class="p-3 text-left">Cost</th>
              <th class="p-3 text-left">Ingredients</th>
              <th class="p-3 text-left">Available</th>
              <th class="p-3 text-left">Image</th>
              <th class="p-3 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="menu in filteredMenus" :key="menu.id" :class="{ 'bg-yellow-100': menu.low_stock }">
              <td class="p-3">{{ menu.name }}</td>
              <td class="p-3">{{ menu.category ? menu.category.name : 'N/A' }}</td>
              <td class="p-3">${{ menu.price }}</td>
              <td class="p-3">{{ menu.prep_time }} min</td>
              <td class="p-3">${{ menu.cost }}</td>
              <td class="p-3">
                <ul>
                  <li v-for="inv in menu.inventories" :key="inv.id">
                    {{ inv.name }} ({{ inv.pivot.quantity }} {{ inv.pivot.unit }})
                  </li>
                </ul>
              </td>
              <td class="p-3">{{ menu.available ? 'Yes' : 'No' }}</td>
              <td class="p-3">
                <img v-if="menu.image" :src="'/storage/' + menu.image" alt="Menu Image" class="w-16 h-16 object-cover" />
                <span v-else>No Image</span>
              </td>
              <td class="p-3">
                <button @click="goToEdit(menu.id)" class="text-blue-600 hover:text-blue-800">Edit</button>
                <button @click="deleteMenu(menu)" class="text-red-600 hover:text-red-800">Delete</button>
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
    name: 'AdminMenuIndex',
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
      goToCreate() {
        router.get('/admin/menus/create');
      },
      goToEdit(menuId) {
        router.get(`/admin/menus/${menuId}/edit`);
      },
      deleteMenu(menu) {
        if (confirm('Are you sure you want to delete this menu item?')) {
          Inertia.delete(`/admin/menus/${menu.id}`);
        }
      },
    },
  };
  </script>

  <style scoped>
  .container {
    width: 100%; /* Full width */
    max-width: none; /* Remove max-width restriction */
  }
  </style>