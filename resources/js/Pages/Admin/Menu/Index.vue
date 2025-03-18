<template>
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Menu Management</h1>

      <div v-if="flash.success" class="bg-green-100 p-2 mb-4 rounded">
        {{ flash.success }}
      </div>

      <router-link to="/admin/menus/create" class="bg-blue-500 text-white p-2 rounded mb-4 inline-block">
        Add New Menu Item
      </router-link>

      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2">Name</th>
            <th class="p-2">Category</th>
            <th class="p-2">Price</th>
            <th class="p-2">Description</th>
            <th class="p-2">Image</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="menu in menus" :key="menu.id">
            <td class="p-2">{{ menu.name }}</td>
            <td class="p-2">{{ menu.category.name }}</td>
            <td class="p-2">${{ menu.price }}</td>
            <td class="p-2">{{ menu.description || 'N/A' }}</td>
            <td class="p-2">
              <img v-if="menu.image" :src="'/storage/' + menu.image" alt="Menu Image" class="w-16 h-16 object-cover" />
              <span v-else>No Image</span>
            </td>
            <td class="p-2">
              <router-link :to="'/admin/menus/' + menu.id + '/edit'" class="text-blue-500 mr-2">Edit</router-link>
              <button @click="deleteMenu(menu)" class="text-red-500">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';

  export default {
    props: {
      menus: Array,
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
    methods: {
      deleteMenu(menu) {
        if (confirm('Are you sure you want to delete this menu item?')) {
          Inertia.delete(`/admin/menus/${menu.id}`, {
            onSuccess: () => {
              console.log('Menu deleted');
            },
          });
        }
      },
    },
  };
  </script>