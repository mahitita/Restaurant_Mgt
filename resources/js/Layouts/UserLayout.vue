<template>
    <div>
      <!-- Navbar -->
      <nav class="bg-white shadow-lg p-4">
        <div class="container mx-auto flex justify-between items-center">
          <Link :href="route('home')" class="text-2xl font-bold text-red-500">Royal Feast</Link>
          <div>
            <Link :href="route('menu')" class="text-gray-700 px-4">Menu</Link>
            <Link :href="route('reservation')" class="text-gray-700 px-4">Reservations</Link>
            <Link :href="route('login')" class="bg-red-500 text-white px-4 py-2 rounded">Login</Link>
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
    import { Link } from '@inertiajs/vue3';
  export default {};
  </script>
