<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Add New User</h1>

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block font-semibold">Name</label>
            <input v-model="form.name" type="text" class="border p-2 w-full rounded" />
          </div>

          <div class="mb-4">
            <label class="block font-semibold">Email</label>
            <input v-model="form.email" type="email" class="border p-2 w-full rounded" />
          </div>

          <div class="mb-4">
            <label class="block font-semibold">Password</label>
            <input v-model="form.password" type="password" class="border p-2 w-full rounded" />
          </div>

          <div class="mb-4">
            <label class="block font-semibold">Role</label>
            <select v-model="form.role" class="border p-2 w-full rounded">
              <option value="admin">Admin</option>
              <option value="user">User</option>
            </select>
          </div>

          <button class="bg-blue-500 text-white px-3 py-2 rounded">Save User</button>
        </form>
      </div>
    </AdminLayout>
  </template>

  <script>
  import AdminLayout from "@/Layouts/AdminLayout.vue";
  import { router } from "@inertiajs/vue3";

  export default {
    components: { AdminLayout },
    data() {
      return {
        form: { name: "", email: "", password: "", role: "user" },
      };
    },
    methods: {
      submit() {
        router.post(route("admin.users.store"), this.form);
      },
    },
  };
  </script>
