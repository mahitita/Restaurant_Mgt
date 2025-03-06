<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Menu Items</h1>

        <!-- Add Menu Button -->
        <Link
          :href="route('admin.menus.create')"
          class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          + Add Menu Item
        </Link>

        <!-- Success Message -->
        <div v-if="successMessage" class="p-3 bg-green-200 text-green-800 rounded mb-4">
          {{ successMessage }}
        </div>

        <!-- Menu Table -->
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 p-2">#</th>
              <th class="border border-gray-300 p-2">Name</th>
              <th class="border border-gray-300 p-2">Category</th>
              <th class="border border-gray-300 p-2">Price</th>
              <th class="border border-gray-300 p-2">Image</th>
              <th class="border border-gray-300 p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(menu, index) in menus" :key="menu.id">
              <td class="border border-gray-300 p-2">{{ index + 1 }}</td>
              <td class="border border-gray-300 p-2">{{ menu.name }}</td>
              <td class="border border-gray-300 p-2">{{ menu.category.name }}</td>
              <td class="border border-gray-300 p-2">${{ menu.price }}</td>
              <td class="border border-gray-300 p-2">
                <img v-if="menu.image" :src="`/storage/${menu.image}`" class="w-16 h-16 object-cover rounded" />
                <span v-else>No Image</span>
              </td>
              <td class="border border-gray-300 p-2">
                <Link
                  :href="route('admin.menus.edit', menu.id)"
                  class="bg-yellow-500 text-white px-3 py-1 rounded mr-2"
                >
                  Edit
                </Link>
                <button
                  @click="deleteMenu(menu.id)"
                  class="bg-red-500 text-white px-3 py-1 rounded"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </AdminLayout>
  </template>

  <script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
  import { router, Link } from "@inertiajs/vue3";

  export default {
    components: { AdminLayout, Link },
    props: { menus: Array },
    data() {
      return { successMessage: "" };
    },
    methods: {
      deleteMenu(id) {
        if (confirm("Are you sure?")) {
          router.delete(route("admin.menus.destroy", id), {
            onSuccess: () => (this.successMessage = "Menu item deleted successfully."),
          });
        }
      },
    },
  };
  </script>
