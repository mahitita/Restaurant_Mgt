<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4">
      <div class="container mx-auto flex justify-between">
        <Link href="/" class="text-lg font-bold">Restaurant Mgt</Link>
        <div>
          <Link href="/dashboard" class="mr-4">Dashboard</Link>
          <Link href="/menu" class="mr-4">Menu</Link>
          <Link href="/orders">Orders</Link>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="container mx-auto p-6">
      <slot />
    </main>
  </div>
</template>
