<template>
    <header class="bg-gradient-to-r from-orange-600 to-yellow-500 text-white shadow-lg">
      <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <!-- Logo -->
        <Link href="/" class="text-3xl font-bold tracking-tight hover:text-yellow-200 transition duration-300">
          Gursha
        </Link>

        <!-- Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <Link href="/" class="nav-link">Home</Link>
          <Link href="/menu" class="nav-link">Menu</Link>
          <Link href="/tables" class="nav-link">Tables</Link>

          <!-- Cart Icon with Count -->
          <Link href="/cart" class="relative flex items-center group">
            <svg class="w-6 h-6 fill-current group-hover:text-yellow-200 transition duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
            <span v-if="cartItems.length > 0" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center group-hover:bg-red-700 transition duration-300">
              {{ totalItems }}
            </span>
          </Link>

          <!-- Guest Links -->
          <template v-if="!auth?.user">
            <Link href="/user/login" class="nav-link">Login</Link>
          </template>

          <!-- Authenticated Links -->
          <template v-else>
            
            <Link href="/reservations" class="nav-link">My Reservations</Link>
            <Link href="/orders" class="nav-link">My Orders</Link>
            <Link href="/user/logout" as="button" method="post" class="nav-link">Logout</Link>
            <Link href="/profile" class="flex items-center group">
              <div class="w-10 h-10 bg-yellow-400 text-orange-800 rounded-full flex items-center justify-center text-xl font-bold group-hover:bg-yellow-300 transition duration-300">
                {{ auth.user.name.charAt(0).toUpperCase() }}
              </div>
            </Link>
          </template>
        </nav>

        <!-- Mobile Menu Button -->
        <button @click="toggleMenu" class="md:hidden focus:outline-none">
          <svg class="w-8 h-8 fill-current hover:text-yellow-200 transition duration-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path v-if="!isMenuOpen" d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
            <path v-else d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <transition name="slide">
        <div v-if="isMenuOpen" class="md:hidden bg-orange-600 text-white px-4 py-6">
          <Link href="/" class="mobile-nav-link">Home</Link>
          <Link href="/menu" class="mobile-nav-link">Menu</Link>
          <Link href="/tables" class="mobile-nav-link">Tables</Link>
          <Link href="/cart" class="mobile-nav-link flex items-center">
            Cart
            <span v-if="cartItems.length > 0" class="ml-2 bg-red-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
              {{ totalItems }}
            </span>
          </Link>

          <!-- Guest Mobile Links -->
          <template v-if="!auth?.user">
            <Link href="/user/login" class="mobile-nav-link">Login</Link>
          </template>

          <!-- Authenticated Mobile Links -->
          <template v-else>
            <Link href="/reservations" class="mobile-nav-link">My Reservations</Link>
            <Link href="/orders" class="mobile-nav-link">My Orders</Link>
            <Link href="/user/logout" as="button" method="post" class="mobile-nav-link">Logout</Link>
            <Link href="/profile" class="mobile-nav-link flex items-center">
              Profile
              <div class="ml-2 w-8 h-8 bg-yellow-400 text-orange-800 rounded-full flex items-center justify-center text-lg font-bold">
                {{ auth.user.name.charAt(0).toUpperCase() }}
              </div>
            </Link>
          </template>
        </div>
      </transition>
    </header>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { Link, usePage } from '@inertiajs/vue3';
  import { useCartStore } from '../Stores/CartStore';
  import { storeToRefs } from 'pinia';

  // Access auth from Inertia's $page
  const page = usePage();
  const auth = page.props.auth;

  const cartStore = useCartStore();
  const { cartItems, totalItems } = storeToRefs(cartStore);

  const isMenuOpen = ref(false);

  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
  };
  </script>

  <style scoped>
  .nav-link {
    @apply text-lg font-medium hover:text-yellow-200 transition duration-300 transform hover:scale-105;
  }

  .mobile-nav-link {
    @apply block py-3 text-lg font-medium hover:text-yellow-200 transition duration-300 border-b border-orange-500 last:border-b-0;
  }

  .slide-enter-active,
  .slide-leave-active {
    transition: all 0.3s ease;
  }

  .slide-enter-from,
  .slide-leave-to {
    transform: translateY(-100%);
    opacity: 0;
  }
  </style>