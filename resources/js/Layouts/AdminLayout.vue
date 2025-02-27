<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const sidebarOpen = ref(false)
</script>

<template>
  <div class="flex">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 h-screen p-4 text-white shadow-md transition-transform duration-300 ease-in-out" :class="{ 'transform -translate-x-full': !sidebarOpen }">
      <h1 class="text-2xl font-bold mb-6 text-center">Admin Panel</h1>
      <nav>
        <ul>
          <li>
            <Link href="/admin/dashboard" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-gray-700 rounded">
              <span class="material-icons">i</span>
              <span class="ml-2">Dashboard</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/menu" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-gray-700 rounded">
              <span class="material-icons">i</span>
              <span class="ml-2">Menu</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/orders" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-gray-700 rounded">
              <span class="material-icons">i</span>
              <span class="ml-2">Orders</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/reservations" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-gray-700 rounded">
              <span class="material-icons">i</span>
              <span class="ml-2">Reservations</span>
            </Link>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6 bg-gray-100 min-h-screen">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.material-icons {
  font-size: 24px; /* Adjust icon size */
}
</style>
