<template>
    <header class="bg-white shadow-md p-4 flex justify-between items-center sticky top-0 z-10">
      <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
      <button
        @click="logout"
        class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition"
      >
        Logout
      </button>
    </header>
  </template>

  <script>
  import { router } from '@inertiajs/vue3';

  export default {
    methods: {
      logout() {
        router.post('/admin/logout');
      },
    },
  };
  </script>
