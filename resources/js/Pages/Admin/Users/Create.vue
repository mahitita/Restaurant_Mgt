<!-- resources/js/Pages/Admin/Users/Create.vue -->
<template>
    <AdminLayout>
      <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-semibold mb-6">Create New User</h1>

        <div v-if="$page.props.flash?.success" class="bg-green-100 p-3 mb-4 rounded">
          {{ $page.props.flash.success }}
        </div>
        <!-- <div v-if="form.errors" class="bg-red-100 p-3 mb-4 rounded">
          <p v-for="(error, field) in form.errors" :key="field">{{ field }}: {{ error }}</p>
        </div> -->

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="border p-2 w-full"
              required
              :class="{ 'border-red-500': form.errors.name }"
            />
            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="border p-2 w-full"
              :class="{ 'border-red-500': form.errors.email }"
            />
            <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Phone</label>
            <input
              v-model="form.phone"
              type="text"
              class="border p-2 w-full"
              required
              :class="{ 'border-red-500': form.errors.phone }"
            />
            <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</p>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Password</label>
            <input
              v-model="form.password"
              type="password"
              class="border p-2 w-full"
              required
              :class="{ 'border-red-500': form.errors.password }"
            />
            <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Role</label>
            <select
              v-model="form.role"
              class="border p-2 w-full"
              required
              :class="{ 'border-red-500': form.errors.role }"
            >
              <option value="customer">Customer</option>
              <option value="admin">Admin</option>
              <option value="waiter">Waiter</option>
              <option value="chef">Chef</option>
              <option value="cashier">Cashier</option>
            </select>
            <p v-if="form.errors.role" class="text-red-500 text-sm mt-1">{{ form.errors.role }}</p>
          </div>
          <button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition duration-300"
            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
          >
            {{ form.processing ? 'Creating...' : 'Create User' }}
          </button>
          <button
            type="button"
            @click="router.get('/admin/users')"
            class="ml-2 bg-gray-300 text-gray-700 p-2 rounded hover:bg-gray-400"
            :disabled="form.processing"
          >
            Cancel
          </button>
        </form>
      </div>
    </AdminLayout>
  </template>

  <script setup>
  import { useForm, router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  defineOptions({
    layout: AdminLayout,
  });

  const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'customer',
  });

  const submit = () => {
    form.post('/admin/users', {
      onSuccess: () => {
        form.reset();
        router.get('/admin/users');
      },
    });
  };
  </script>

  <style scoped>
  .container {
    max-width: 600px;
  }
  </style>