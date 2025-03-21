<template>
    <UserLayout>
      <!-- Hero Section -->
      <section class="relative h-[80vh] bg-cover bg-center" :style="{ backgroundImage: 'url(/images/hero.jpg)' }">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
          <div class="text-center text-white">
            <h1 class="text-5xl md:text-6xl font-bold mb-4 animate-fade-in">Welcome to Gursha</h1>
            <p class="text-xl md:text-2xl mb-6">Savor the flavors of Ethiopia</p>
            <Link href="#tables" class="bg-gursha-primary text-white px-6 py-3 rounded-full text-lg hover:bg-opacity-80 transition-all duration-300">
              Reserve a Table
            </Link>
          </div>
        </div>
      </section>

      <!-- Menus Section -->
      <section id="menus" class="py-16 bg-white">
        <div class="container mx-auto px-4">
          <h2 class="text-4xl font-bold text-center text-gursha-secondary mb-12">Our Menus</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="menu in menus" :key="menu.id" class="group bg-gray-100 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
              <img :src="menu.image || '/images/menu1.jpg'" alt="Menu" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" />
              <div class="p-4">
                <h3 class="text-xl font-semibold text-gursha-secondary">{{ menu.name }}</h3>
                <p class="text-gursha-primary font-bold">${{ menu.price }}</p>
              </div>
            </div>
          </div>
          <div class="text-center mt-8">
            <Link href="/menus" class="text-gursha-primary hover:underline">View All Menus</Link>
          </div>
        </div>
      </section>

      <!-- Tables Section -->
      <section id="tables" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
          <h2 class="text-4xl font-bold text-center text-gursha-secondary mb-12">Available Tables</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="table in tables" :key="table.id" class="bg-white rounded-lg shadow-lg p-4 hover:shadow-xl transition-all duration-300">
              <div :class="getTableStyle(table)" class="w-full h-32 flex items-center justify-center text-white font-bold">
                {{ table.table_number }} ({{ table.seats }} Seats)
              </div>
            </div>
          </div>
          <div class="text-center mt-8">
            <Link href="/tables" class="bg-gursha-primary text-white px-6 py-3 rounded-full hover:bg-opacity-80 transition-all duration-300">
              Reserve Now
            </Link>
          </div>
        </div>
      </section>

      <!-- About Us Section -->
      <section id="about" class="py-16 bg-white">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
          <img src="/images/about.jpg" alt="About Gursha" class="w-full md:w-1/2 rounded-lg shadow-lg mb-6 md:mb-0 md:mr-8" />
          <div>
            <h2 class="text-4xl font-bold text-gursha-secondary mb-4">About Gursha</h2>
            <p class="text-gray-700">
              Gursha is more than a restaurant—it’s a celebration of Ethiopian culture and cuisine. Named after the traditional act of sharing food, we bring people together with authentic dishes made from the finest ingredients. Join us for an unforgettable dining experience.
            </p>
          </div>
        </div>
      </section>

      <!-- Contact Us Section -->
      <section id="contact" class="py-16 bg-gursha-secondary text-white">
        <div class="container mx-auto px-4 text-center">
          <h2 class="text-4xl font-bold mb-8">Contact Us</h2>
          <p class="text-lg mb-4">Have questions? Reach out to us!</p>
          <p class="mb-2">123 Ethiopian St, Addis Ababa</p>
          <p class="mb-2">Phone: +251 912 345 678</p>
          <p class="mb-6">Email: info@gursha.et</p>
          <Link href="/contact" class="bg-gursha-primary text-white px-6 py-3 rounded-full hover:bg-opacity-80 transition-all duration-300">
            Send a Message
          </Link>
        </div>
      </section>
    </UserLayout>
  </template>

  <script>
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
  export default {
    components: { Link, UserLayout },
    props: {
      menus: Array,
      tables: Array,
    },
    methods: {
      getTableStyle(table) {
        return {
          'bg-green-500': table.status === 'available',
          'rounded-full': table.type === 'round',
          'rounded-lg': table.type !== 'round',
        };
      },
    },
  };
  </script>

  <style>
  .animate-fade-in {
    animation: fadeIn 1s ease-in;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  </style>