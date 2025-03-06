<template>
    <UserLayout>
      <!-- Hero Section -->
      <section class="relative h-screen flex items-center justify-center bg-cover bg-center"
               style="background-image: url('/images/restaurant-hero.jpg')">
        <div class="bg-black bg-opacity-50 text-white text-center p-6 rounded-lg">
          <h1 class="text-5xl font-bold mb-4">Welcome to Royal Feast</h1>
          <p class="text-lg mb-6">Experience world-class dining with the best chefs in town.</p>
          <router-link :href="route('menu')" class="bg-red-500 px-6 py-3 rounded-lg text-white text-lg">
            View Menu
          </router-link>
        </div>
      </section>

      <!-- Featured Dishes Section -->
      <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
          <h2 class="text-3xl font-semibold text-center mb-8">Our Special Dishes</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="dish in featuredDishes" :key="dish.id" class="bg-white p-4 shadow rounded-lg">
              <img :src="dish.image" alt="dish.name" class="w-full h-40 object-cover rounded">
              <h3 class="text-xl font-semibold mt-4">{{ dish.name }}</h3>
              <p class="text-gray-600">{{ dish.description }}</p>
              <span class="text-red-500 font-bold text-lg">${{ dish.price }}</span>
            </div>
          </div>
        </div>
      </section>

      <!-- Call to Action -->
      <section class="py-12 bg-red-500 text-white text-center">
        <h2 class="text-3xl font-bold">Reserve Your Table Now</h2>
        <p class="text-lg mt-2">Enjoy the finest dining experience with us.</p>
        <router-link :href="route('reservation')" class="mt-4 bg-white text-red-500 px-6 py-3 rounded-lg inline-block">
          Make a Reservation
        </router-link>
      </section>
    </UserLayout>
  </template>

  <script>
  import UserLayout from "../Layouts/UserLayout.vue";

  export default {
    components: { UserLayout },
    data() {
      return {
        featuredDishes: [
          { id: 1, name: "Grilled Steak", description: "Juicy grilled steak with special sauce", price: 25, image: "/images/steak.jpg" },
          { id: 2, name: "Pasta Primavera", description: "Fresh veggies and pasta in a creamy sauce", price: 18, image: "/images/pasta.jpg" },
          { id: 3, name: "Sushi Platter", description: "A selection of the finest sushi rolls", price: 30, image: "/images/sushi.jpg" }
        ]
      };
    }
  };
  </script>
