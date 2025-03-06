<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">User Management</h1>

        <!-- Success Message -->
        <div v-if="successMessage" class="p-3 bg-green-200 text-green-800 rounded mb-4">
          {{ successMessage }}
        </div>

        <Link :href="route('admin.users.create')" class="bg-blue-500 text-white px-3 py-2 rounded mb-4 inline-block">
          + Add User
        </Link>

        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 p-2">#</th>
              <th class="border border-gray-300 p-2">Name</th>
              <th class="border border-gray-300 p-2">Email</th>
              <th class="border border-gray-300 p-2">Role</th>
              <th class="border border-gray-300 p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, index) in users" :key="user.id">
              <td class="border border-gray-300 p-2">{{ index + 1 }}</td>
              <td class="border border-gray-300 p-2">{{ user.name }}</td>
              <td class="border border-gray-300 p-2">{{ user.email }}</td>
              <td class="border border-gray-300 p-2">{{ user.role }}</td>
              <td class="border border-gray-300 p-2">
                <Link :href="route('admin.users.edit', user.id)" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2">
                  Edit
                </Link>
                <button
                  @click="deleteUser(user.id)"
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
    props: { users: Array },
    data() {
      return { successMessage: "" };
    },
    methods: {
      deleteUser(id) {
        if (confirm("Are you sure?")) {
          router.delete(route("admin.users.destroy", id), {
            onSuccess: () => (this.successMessage = "User deleted successfully."),
          });
        }
      },
    },
  };
  </script>
