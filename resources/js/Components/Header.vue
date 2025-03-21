<template>
    <header class="bg-gradient-to-r from-gursha-secondary to-gursha-accent text-white shadow-xl sticky top-0 z-50">
      <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
          <Link href="/" class="flex items-center group">
            <img
              src="/images/gursha-logo.png"
              alt="Gursha Logo"
              class="h-14 w-auto mr-3 transform group-hover:scale-110 transition-transform duration-300"
            />
            <span class="text-3xl font-extrabold tracking-tight text-gursha-light group-hover:text-gursha-primary transition-colors duration-300">
              Gursha
            </span>
          </Link>
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-8">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="text-lg font-medium text-white hover:text-gursha-primary hover:scale-105 transform transition-all duration-200"
          >
            {{ link.label }}
          </Link>
        </div>

        <!-- Auth Links -->
        <div class="hidden md:flex items-center space-x-4">
          <Link
            v-if="$page.props.auth.user"
            href="/dashboard"
            class="text-lg font-medium hover:text-gursha-primary transition-colors duration-200"
          >
            Dashboard
          </Link>
          <Link
            v-if="$page.props.auth.user"
            href="/logout"
            method="post"
            as="button"
            class="text-lg font-medium hover:text-gursha-primary transition-colors duration-200"
          >
            Logout
          </Link>
          <Link v-else href="/login" class="text-lg font-medium hover:text-gursha-primary transition-colors duration-200">
            Login
          </Link>
          <Link
            v-else
            href="/register"
            class="bg-gursha-primary text-white px-6 py-2 rounded-full font-semibold hover:bg-opacity-90 hover:shadow-lg transform hover:scale-105 transition-all duration-300"
          >
            Register
          </Link>
        </div>

        <!-- Mobile Menu Button -->
        <button @click="toggleMenu" class="md:hidden focus:outline-none p-2 rounded-full hover:bg-gursha-primary transition-colors duration-200">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              :d="menuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"
            />
          </svg>
        </button>
      </nav>

      <!-- Mobile Menu -->
      <transition name="slide">
        <div v-if="menuOpen" class="md:hidden bg-gursha-secondary px-6 py-4 shadow-lg">
          <div class="flex flex-col space-y-4">
            <Link
              v-for="link in navLinks"
              :key="link.href"
              :href="link.href"
              class="text-lg font-medium text-white hover:text-gursha-primary hover:pl-2 transition-all duration-200"
              @click="toggleMenu"
            >
              {{ link.label }}
            </Link>
            <Link
              v-if="$page.props.auth.user"
              href="/dashboard"
              class="text-lg font-medium hover:text-gursha-primary hover:pl-2 transition-all duration-200"
              @click="toggleMenu"
            >
              Dashboard
            </Link>
            <Link
              v-if="$page.props.auth.user"
              href="/logout"
              method="post"
              as="button"
              class="text-lg font-medium hover:text-gursha-primary hover:pl-2 transition-all duration-200"
              @click="toggleMenu"
            >
              Logout
            </Link>
            <Link
              v-else
              href="/login"
              class="text-lg font-medium hover:text-gursha-primary hover:pl-2 transition-all duration-200"
              @click="toggleMenu"
            >
              Login
            </Link>
            <Link
              v-else
              href="/register"
              class="bg-gursha-primary text-white px-6 py-2 rounded-full font-semibold text-center hover:bg-opacity-90 transition-all duration-300"
              @click="toggleMenu"
            >
              Register
            </Link>
          </div>
        </div>
      </transition>
    </header>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3';

  export default {
    components: { Link },
    data() {
      return {
        menuOpen: false,
        navLinks: [
          { href: '/', label: 'Home' },
          { href: '#menus', label: 'Menus' },
          { href: '#tables', label: 'Tables' },
          { href: '#about', label: 'About Us' },
          { href: '#contact', label: 'Contact Us' },
        ],
      };
    },
    methods: {
      toggleMenu() {
        this.menuOpen = !this.menuOpen;
      },
    },
  };
  </script>

  <style scoped>
  .slide-enter-active,
  .slide-leave-active {
    transition: transform 0.3s ease;
  }
  .slide-enter-from,
  .slide-leave-to {
    transform: translateY(-100%);
  }
  </style>