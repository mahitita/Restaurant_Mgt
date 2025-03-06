<template>
    <UserLayout>
      <!-- Hero Section -->
      <section class="relative h-64 flex items-center justify-center bg-cover bg-center"
               style="background-image: url('/images/menu-hero.jpg')">
        <div class="bg-black bg-opacity-50 text-white text-center p-6 rounded-lg">
          <h1 class="text-4xl font-bold">Our Menu</h1>
          <p class="text-lg">Discover the best dishes made with love.</p>
        </div>
      </section>

      <!-- Category Filter -->
      <div class="container mx-auto py-8 px-4">
        <h2 class="text-2xl font-semibold mb-4">Filter by Category</h2>
        <div class="flex space-x-4 overflow-x-auto pb-4">
          <button
            v-for="category in categories"
            :key="category.id"
            @click="filterMenu(category.id)"
            class="px-4 py-2 border rounded bg-gray-200 hover:bg-red-500 hover:text-white transition">
            {{ category.name }}
          </button>
        </div>
      </div>

      <!-- Menu Items -->
      <section class="container mx-auto py-8 px-4">
        <div v-if="filteredMenus.length === 0" class="text-center text-gray-500">
          No items found in this category.
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="menu in filteredMenus" :key="menu.id" class="bg-white p-4 shadow rounded-lg">
            <img :src="menu.image" alt="menu.name" class="w-full h-40 object-cover rounded">
            <h3 class="text-xl font-semibold mt-4">{{ menu.name }}</h3>
            <p class="text-gray-600">{{ menu.description }}</p>
            <span class="text-red-500 font-bold text-lg">${{ menu.price }}</span>
            <button @click="addToCart(menu)" class="mt-3 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-700">
              Add to Cart
            </button>
          </div>
        </div>
      </section>
    </UserLayout>
  </template>

  <script>
  import UserLayout from "../Layouts/UserLayout.vue";
  import { usePage } from "@inertiajs/vue3";

  export default {
    components: { UserLayout },
    props: {
      menus: Array,
      categories: Array
    },
    data() {
      return {
        filteredMenus: this.menus
      };
    },
    methods: {
      filterMenu(categoryId) {
        this.filteredMenus = categoryId
          ? this.menus.filter(menu => menu.category_id === categoryId)
          : this.menus;
      },
      addToCart(menu) {
        alert(`Added ${menu.name} to cart!`);
      }
    }
  };
  </script>
