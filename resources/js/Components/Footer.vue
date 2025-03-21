<template>
    <footer class="bg-gradient-to-r from-purple-500 to-blue-400 text-white py-10">
      <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-10 px-6">
        <!-- Brand -->
        <div>
          <div class="flex items-center mb-4">
            <img src="/images/gursha-logo.png" alt="Gursha Logo" class="h-14 w-auto mr-2" />
            <span class="text-3xl font-bold">Gursha</span>
          </div>
          <p class="text-sm">Savor the rich flavors of Ethiopian cuisine at Gursha, where every meal tells a story.</p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-2">
            <li><Link href="/" class="hover:underline">Home</Link></li>
            <li><Link href="#menus" class="hover:underline">Menus</Link></li>
            <li><Link href="#tables" class="hover:underline">Tables</Link></li>
            <li><Link href="#about" class="hover:underline">About Us</Link></li>
            <li><Link href="#contact" class="hover:underline">Contact Us</Link></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
          <p class="text-sm">123 Ethiopian St, Addis Ababa</p>
          <p class="text-sm">Phone: +251 912 345 678</p>
          <p class="text-sm">Email: info@gursha.et</p>
        </div>
      </div>
      <div class="mt-8 text-center text-sm border-t border-white pt-4">
        &copy; {{ new Date().getFullYear() }} Gursha. All rights reserved.
      </div>
    </footer>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3';
  export default {
    components: { Link },
  };
  </script>