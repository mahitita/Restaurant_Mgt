<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const sidebarOpen = ref(true) // Set to true for testing
</script>

<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div
      class="w-64 bg-gray-800 h-full p-4 text-white shadow-lg transition-transform duration-300 ease-in-out"
      :class="{ 'transform -translate-x-full': !sidebarOpen }"
    >
      <h1 class="text-3xl font-bold mb-6 text-center">Admin Panel</h1>
      <nav>
        <ul>
          <li>
            <Link href="/admin/dashboard" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-blue-600 rounded">
              <span class="material-icons">dashboard</span>
              <span class="ml-2">Dashboard</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/menu" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-blue-600 rounded">
              <span class="material-icons">restaurant_menu</span>
              <span class="ml-2">Menu</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/orders" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-blue-600 rounded">
              <span class="material-icons">receipt</span>
              <span class="ml-2">Orders</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/reservations" class="flex items-center py-2 px-4 transition-colors duration-200 hover:bg-blue-600 rounded">
              <span class="material-icons">event</span>
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
h1 {
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5); /* Add shadow to the title for depth */
}
nav ul {
  list-style: none; /* Remove default list styling */
  padding: 0; /* Remove padding */
}
nav li {
  margin-bottom: 10px; /* Space out the list items */
}
</style>
