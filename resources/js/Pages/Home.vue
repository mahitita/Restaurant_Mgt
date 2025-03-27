<template>
    <UserLayout>
      <!-- 1. Hero Section -->
      <section class="relative bg-cover bg-center h-screen" :style="{ backgroundImage: `url('/image/h1.jpg')` }">
        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
          <div class="text-center text-white">
            <h1 class="text-5xl md:text-7xl font-bold mb-4 animate-fade-in">Savor the Flavor</h1>
            <p class="text-xl md:text-2xl mb-6">Indulge in our delicious dishes, crafted just for you.</p>
            <Link href="/menu" class="bg-orange-600 text-white px-8 py-4 rounded-full text-lg hover:bg-orange-700 transition">
              Explore Our Menu
            </Link>
          </div>
        </div>
      </section>
  
      <!-- 2. Welcome Message -->
      <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4 text-center">
          <h2 class="text-4xl font-semibold text-gray-800 mb-4">Welcome to Our Restaurant</h2>
          <p class="text-lg text-gray-600 mb-6">Experience a culinary journey with fresh ingredients and bold flavors.</p>
          <img src="/image/gursha.jpg" alt="Welcome Dish" class="mx-auto w-full max-w-md rounded-lg shadow-lg" />
        </div>
      </section>
  
      <!-- 3. Featured Dishes -->
      <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
          <h2 class="text-4xl font-semibold text-gray-800 text-center mb-12">Featured Dishes</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="dish in featuredDishes" :key="dish.id" class="text-center">
              <img :src="dish.image" :alt="dish.name" class="w-full h-64 object-cover rounded-lg shadow-md mb-4" />
              <h3 class="text-2xl font-semibold text-gray-800">{{ dish.name }}</h3>
              <p class="text-gray-600">{{ dish.description }}</p>
            </div>
          </div>
          <div class="text-center mt-8">
            <Link href="/menu" class="text-orange-600 hover:underline text-lg">See Full Menu</Link>
          </div>
        </div>
      </section>
  
      <!-- 4. Why Choose Us -->
      <section class="py-16 bg-gray-200">
        <div class="container mx-auto px-4 text-center">
          <h2 class="text-4xl font-semibold text-gray-800 mb-12">Why Choose Us?</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
              <img src="/image/hero 1.jpg" alt="Fresh" class="w-24 h-24 mx-auto mb-4 rounded-full" />
              <h3 class="text-xl font-semibold text-gray-800">Fresh Ingredients</h3>
              <p class="text-gray-600">Sourced daily for maximum flavor.</p>
            </div>
            <div>
              <img src="/images/chef.jpg" alt="Expert Chefs" class="w-24 h-24 mx-auto mb-4 rounded-full" />
              <h3 class="text-xl font-semibold text-gray-800">Expert Chefs</h3>
              <p class="text-gray-600">Masters of culinary arts.</p>
            </div>
            <div>
              <img src="/images/ambiance.jpg" alt="Ambiance" class="w-24 h-24 mx-auto mb-4 rounded-full" />
              <h3 class="text-xl font-semibold text-gray-800">Great Ambiance</h3>
              <p class="text-gray-600">Dine in style and comfort.</p>
            </div>
          </div>
        </div>
      </section>
  
      <!-- 5. Special Offers -->
      <section class="py-16 bg-orange-100">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
          <img src="/images/special-offer.jpg" alt="Special Offer" class="w-full md:w-1/2 h-64 object-cover rounded-lg shadow-md mb-6 md:mb-0 md:mr-6" />
          <div class="text-center md:text-left">
            <h2 class="text-4xl font-semibold text-gray-800 mb-4">Special Offer</h2>
            <p class="text-lg text-gray-600 mb-6">Get 20% off your first order this week!</p>
            <Link href="/menu" class="bg-orange-600 text-white px-6 py-3 rounded-full hover:bg-orange-700 transition">Order Now</Link>
          </div>
        </div>
      </section>
  
      <!-- 6. Our Story -->
      <section class="py-16 bg-white">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
          <div class="md:w-1/2 text-center md:text-left mb-6 md:mb-0">
            <h2 class="text-4xl font-semibold text-gray-800 mb-4">Our Story</h2>
            <p class="text-lg text-gray-600">Started in 2010, we’ve been serving happiness on plates with a passion for food and community.</p>
          </div>
          <img src="/image/gu.jpg" alt="Our Story" class="w-full md:w-1/2 h-64 object-cover rounded-lg shadow-md" />
        </div>
      </section>
  
      <!-- 7. Customer Reviews -->
      <section class="py-16 bg-gray-100 relative">
        <div class="absolute inset-0 bg-cover bg-center opacity-10" :style="{ backgroundImage: `url('/images/reviews-bg.jpg')` }"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
          <h2 class="text-4xl font-semibold text-gray-800 mb-12">What Our Customers Say</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div v-for="review in reviews" :key="review.id" class="bg-white p-6 rounded-lg shadow-md">
              <p class="text-gray-600 italic mb-4">"{{ review.text }}"</p>
              <p class="text-gray-800 font-semibold">{{ review.name }}</p>
            </div>
          </div>
        </div>
      </section>
  
      <!-- 8. Book a Table -->
      <section class="py-16 bg-orange-200">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
          <img src="/images/dining-table.jpg" alt="Dining Table" class="w-full md:w-1/2 h-64 object-cover rounded-lg shadow-md mb-6 md:mb-0 md:mr-6" />
          <div class="text-center md:text-left">
            <h2 class="text-4xl font-semibold text-gray-800 mb-4">Book a Table</h2>
            <p class="text-lg text-gray-600 mb-6">Reserve your spot for an unforgettable dining experience.</p>
            <Link href="/tables" class="bg-orange-600 text-white px-6 py-3 rounded-full hover:bg-orange-700 transition">Book Now</Link>
          </div>
        </div>
      </section>
  
      <!-- 9. Gallery -->
      <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
          <h2 class="text-4xl font-semibold text-gray-800 text-center mb-12">Our Culinary Creations</h2>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <img v-for="(img, index) in galleryImages" :key="index" :src="img" :alt="'Dish ' + (index + 1)" class="w-full h-48 object-cover rounded-lg shadow-md" />
          </div>
        </div>
      </section>
  
      <!-- 10. Events & Catering -->
      <section class="py-16 bg-gray-200">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
          <div class="md:w-1/2 text-center md:text-left mb-6 md:mb-0">
            <h2 class="text-4xl font-semibold text-gray-800 mb-4">Events & Catering</h2>
            <p class="text-lg text-gray-600">Host your next event with us or let us cater your special occasion.</p>
          </div>
          <img src="/images/catering.jpg" alt="Catering" class="w-full md:w-1/2 h-64 object-cover rounded-lg shadow-md" />
        </div>
      </section>
  
      <!-- 11. Contact Us -->
      <section class="py-16 bg-orange-100">
        <div class="container mx-auto px-4 text-center">
          <h2 class="text-4xl font-semibold text-gray-800 mb-4">Get in Touch</h2>
          <p class="text-lg text-gray-600 mb-6">Visit us at 123 Flavor Street, or call (555) 123-4567.</p>
          <img src="/images/contact-food.jpg" alt="Contact" class="mx-auto w-full max-w-md rounded-lg shadow-lg" />
        </div>
      </section>
  
      <!-- 12. Social Media -->
      <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
          <h2 class="text-4xl font-semibold text-gray-800 mb-4">Follow Us</h2>
          <div class="flex justify-center space-x-6">
            <a href="#" class="text-orange-600 hover:text-orange-700 text-3xl"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-orange-600 hover:text-orange-700 text-3xl"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-orange-600 hover:text-orange-700 text-3xl"><i class="fab fa-twitter"></i></a>
          </div>
          <img src="/images/social-food.jpg" alt="Social Media" class="mt-6 mx-auto w-full max-w-md rounded-lg shadow-lg" />
        </div>
      </section>
    </UserLayout>
  </template>
  
  <script setup>
  import UserLayout from '../Layouts/UserLayout.vue';
  import { Link } from '@inertiajs/vue3';
  
  // Sample data (replace with dynamic data from backend if needed)
  const featuredDishes = [
    { id: 1, name: 'Margherita Pizza', description: 'Fresh tomatoes and mozzarella.', image: '/image/pizza.jpg' },
    { id: 2, name: 'Grilled Salmon', description: 'Served with lemon butter sauce.', image: '/image/hitfo.jpg' },
    { id: 3, name: 'Chocolate Lava Cake', description: 'Warm and gooey perfection.', image: '/image/cake.jpg' },
  ];
  
  const reviews = [
    { id: 1, name: 'Jane Doe', text: 'The best meal I’ve had in years!' },
    { id: 2, name: 'John Smith', text: 'Amazing service and delicious food.' },
    { id: 3, name: 'Emily Brown', text: 'A must-visit for food lovers!' },
  ];
  
  const galleryImages = [
    '/images/dish1.jpg',
    '/images/dish2.jpg',
    '/images/dish3.jpg',
    '/images/dish4.jpg',
    '/images/dish5.jpg',
    '/images/dish6.jpg',
  ];
  </script>
  
  <style scoped>
  /* Add some animations */
  .animate-fade-in {
    animation: fadeIn 1s ease-in;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  /* Ensure Font Awesome is included in your project */
  @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
  </style>