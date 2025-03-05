<template>
    <AdminLayout>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
        <div class="bg-blue-600 p-6 rounded-lg shadow-lg transform transition-transform hover:scale-105">
          <h3 class="text-xl font-semibold">Total Orders</h3>
          <p class="text-3xl font-bold">{{ totalOrders }}</p>
        </div>
        <div class="bg-green-600 p-6 rounded-lg shadow-lg transform transition-transform hover:scale-105">
          <h3 class="text-xl font-semibold">Revenue</h3>
          <p class="text-3xl font-bold">${{ revenue }}</p>
        </div>
        <div class="bg-yellow-600 p-6 rounded-lg shadow-lg transform transition-transform hover:scale-105">
          <h3 class="text-xl font-semibold">Users</h3>
          <p class="text-3xl font-bold">{{ users }}</p>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script>
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  export default {
    components: { AdminLayout },
    data() {
      return {
        totalOrders: 120,
        revenue: 5000,
        users: 50,
      };
    },
  };
  </script>
