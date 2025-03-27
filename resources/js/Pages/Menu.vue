<template>
    <UserLayout>
      <!-- Hero Section -->
      <section class="relative h-64 md:h-80 flex items-center justify-center bg-cover bg-center" :style="{ backgroundImage: `url('/images/menu-hero.jpg')` }">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
          <div class="text-center text-white p-6 animate-fade-in">
            <h1 class="text-4xl md:text-5xl font-extrabold drop-shadow-lg">Gursha Menu</h1>
            <p class="text-lg md:text-xl mt-2 font-light">Savor the Flavors of Ethiopia</p>
          </div>
        </div>
      </section>
  
      <!-- Category Filter -->
      <div class="container mx-auto py-8 px-4 sticky top-0 bg-white z-10 shadow-sm">
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-4 text-center">Browse by Category</h2>
        <div class="flex flex-wrap justify-center gap-3 overflow-x-auto pb-2">
          <button
            @click="filterMenu(null)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all duration-300',
              !selectedCategory ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-orange-100'
            ]"
          >
            All
          </button>
          <button
            v-for="category in categories"
            :key="category.id"
            @click="filterMenu(category.id)"
            :class="[
              'px-4 py-2 rounded-full text-sm font-medium transition-all duration-300',
              selectedCategory === category.id ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-orange-100'
            ]"
          >
            {{ category.name }}
          </button>
        </div>
      </div>
  
      <!-- Success Message -->
      <transition name="fade">
        <div v-if="showSuccess" class="fixed top-16 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 flex items-center">
          <i class="fas fa-check-circle mr-2"></i>
          {{ successMessage }}
        </div>
      </transition>
  
      <!-- Menu Items -->
      <section class="container mx-auto py-8 px-4">
        <div v-if="filteredMenus.length === 0" class="text-center text-gray-500 text-lg">
          No items available in this category.
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <div
            v-for="menu in filteredMenus"
            :key="menu.id"
            class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 hover:shadow-lg transition-all duration-300"
          >
            <img :src="menu.image" :alt="menu.name" class="w-full h-28 object-cover" />
            <div class="p-3 text-center">
              <h3 class="text-md font-semibold text-gray-800 truncate">{{ menu.name }}</h3>
              <p class="text-xs text-gray-500 line-clamp-2">{{ menu.description }}</p>
              <p class="text-orange-600 font-bold text-sm mt-1">${{ menu.price }}</p>
              <button
                @click="addToCart(menu)"
                class="mt-2 bg-orange-600 text-white text-xs px-3 py-1 rounded-full hover:bg-orange-700 transition"
              >
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
  import { router } from '@inertiajs/vue3';
  
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
        }, 2000);
        router.post(route('cart.add', menu.id));
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
  /* Fade Animation for Success Message */
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
  }
  .fade-enter-from, .fade-leave-to {
    opacity: 0;
  }
  
  /* Fade-in Animation for Hero */
  .animate-fade-in {
    animation: fadeIn 1s ease-in;
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  /* Ensure Font Awesome is included */
  @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
  </style>