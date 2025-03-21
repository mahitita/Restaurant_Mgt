<template>
    <UserLayout>
      <!-- Hero Section -->
      <section class="relative h-80 flex items-center justify-center bg-cover bg-center" style="background-image: url('/images/menu-hero.jpg')">
        <div class="bg-black bg-opacity-50 text-white text-center p-8 rounded-lg">
          <h1 class="text-5xl font-bold">Gursha Menu</h1>
          <p class="text-xl mt-2">Savor the Flavors of Ethiopia</p>
        </div>
      </section>

      <!-- Category Filter -->
      <div class="container mx-auto py-12 px-4">
        <h2 class="text-3xl font-semibold mb-6 text-gray-800">Browse by Category</h2>
        <div class="flex flex-wrap gap-4 overflow-x-auto pb-4">
          <button
            @click="filterMenu(null)"
            :class="{ 'bg-orange-600 text-white': !selectedCategory, 'bg-gray-200 text-gray-700': selectedCategory }"
            class="px-6 py-2 rounded-full hover:bg-orange-500 transition"
          >
            All
          </button>
          <button
            v-for="category in categories"
            :key="category.id"
            @click="filterMenu(category.id)"
            :class="{ 'bg-orange-600 text-white': selectedCategory === category.id, 'bg-gray-200 text-gray-700': selectedCategory !== category.id }"
            class="px-6 py-2 rounded-full hover:bg-orange-500 transition"
          >
            {{ category.name }}
          </button>
        </div>
      </div>

      <!-- Success Message -->
      <transition name="fade">
        <div v-if="showSuccess" class="fixed top-20 right-5 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
          {{ successMessage }}
        </div>
      </transition>

      <!-- Menu Items -->
      <section class="container mx-auto py-8 px-4">
        <div v-if="filteredMenus.length === 0" class="text-center text-gray-500 text-lg">
          No items available in this category.
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
          <div v-for="menu in filteredMenus" :key="menu.id" class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition">
            <img :src="menu.image" :alt="menu.name" class="w-full h-48 object-cover" />
            <div class="p-6">
              <h3 class="text-xl font-semibold text-gray-800">{{ menu.name }}</h3>
              <p class="text-gray-600 mt-2">{{ menu.description }}</p>
              <p class="text-orange-600 font-bold text-lg mt-2">${{ menu.price }}</p>
              <button @click="addToCart(menu)" class="mt-4 bg-orange-600 text-white px-4 py-2 rounded-full hover:bg-orange-700">
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </section>
    </UserLayout>
  </template>

  <script>
  import UserLayout from '../Layouts/UserLayout.vue';
  import { useCartStore } from '../Stores/CartStore';
  import { ref } from 'vue';

  export default {
    components: { UserLayout },
    props: {
      menus: Array,
      categories: Array,
    },
    setup(props) {
      const cartStore = useCartStore();
      const filteredMenus = ref(props.menus);
      const selectedCategory = ref(null);
      const showSuccess = ref(false);
      const successMessage = ref('');

      const filterMenu = (categoryId) => {
        selectedCategory.value = categoryId;
        filteredMenus.value = categoryId
          ? props.menus.filter(menu => menu.category_id === categoryId)
          : props.menus;
      };

      const addToCart = (menu) => {
        cartStore.addToCart(menu);
        successMessage.value = `${menu.name} added to cart!`;
        showSuccess.value = true;
        setTimeout(() => {
          showSuccess.value = false;
        }, 3000);
      };

      return {
        filteredMenus,
        selectedCategory,
        filterMenu,
        addToCart,
        showSuccess,
        successMessage,
      };
    },
  };
  </script>

  <style scoped>
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
  }
  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }
  </style>