<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
  <AppLayout>
    <div class="bg-cover bg-center h-screen" style="background-image: url('path/to/your/hero-image.jpg');">
      <div class="flex items-center justify-center h-full bg-black bg-opacity-50">
        <div class="text-center text-white">
          <h1 class="text-5xl md:text-6xl font-bold">Welcome to Our Restaurant Management System</h1>
          <p class="text-lg md:text-xl mt-4">Manage orders, reservations, and queues efficiently.</p>
          <router-link to="/features" class="mt-6 inline-block bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition">
            Discover Features
          </router-link>
        </div>
      </div>
    </div>

    <div class="py-10 px-4">
      <h2 class="text-3xl font-bold text-center">Key Features</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded shadow p-6 text-center">
          <h3 class="text-xl font-semibold">Order Management</h3>
          <p class="text-gray-600 mt-2">Take orders for dine-in, takeout, and delivery.</p>
        </div>
        <div class="bg-white rounded shadow p-6 text-center">
          <h3 class="text-xl font-semibold">Menu Management</h3>
          <p class="text-gray-600 mt-2">Add and manage food items and categories.</p>
        </div>
        <div class="bg-white rounded shadow p-6 text-center">
          <h3 class="text-xl font-semibold">Table Reservation</h3>
          <p class="text-gray-600 mt-2">Book tables in advance for a seamless experience.</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.bg-cover {
  background-size: cover;
}
</style>
