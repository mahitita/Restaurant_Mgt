<template>
    <div>
      <!-- Navbar -->
      <nav class="bg-white shadow-lg p-4">
        <div class="container mx-auto flex justify-between items-center">
          <router-link :href="route('home')" class="text-2xl font-bold text-red-500">Royal Feast</router-link>
          <div>
            <router-link :href="route('menu')" class="text-gray-700 px-4">Menu</router-link>
            <router-link :href="route('reservation')" class="text-gray-700 px-4">Reservations</router-link>
            <router-link :href="route('login')" class="bg-red-500 text-white px-4 py-2 rounded">Login</router-link>
          </div>
        </div>
      </nav>

      <!-- Page Content -->
      <main class="mt-6">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="bg-gray-900 text-white text-center py-6 mt-12">
        <p>&copy; 2025 Royal Feast. All Rights Reserved.</p>
      </footer>
    </div>
  </template>

  <script>
  export default {};
  </script>
