<template>
    <header class="bg-white shadow p-4 flex justify-between items-center">
      <h1 class="text-lg font-semibold text-gray-800">Admin Dashboard</h1>
      <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded transition hover:bg-red-600">Logout</button>
    </header>
  </template>

  <script>
  import { router } from '@inertiajs/vue3';

  export default {
    methods: {
      logout() {
        router.post('/logout');
      }
    }
  };
  </script>
