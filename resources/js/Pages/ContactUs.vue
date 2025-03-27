<!-- resources/js/Pages/ContactUs.vue -->
<template>
    <div class="min-h-screen bg-black text-white">
      <Header />
      <section class="py-16 container mx-auto">
        <h1 class="text-5xl font-bold text-center mb-12 animate-fade-in">Get in Touch</h1>
        <div class="grid grid-cols-2 gap-8">
          <div>
            <h2 class="text-3xl font-semibold mb-4">Visit Us</h2>
            <p class="text-lg mb-4">123 Spice Lane, Seattle, WA</p>
            <div class="relative h-96 w-full">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2689.504851897!2d-122.33207068436742!3d47.606209979184!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDfCsDM2JzIyLjQiTiAxMjLCsDE5JzU1LjQiVw!5e0!3m2!1sen!2sus!4v1617041234567"
                class="w-full h-full rounded-lg"
                allowfullscreen
                loading="lazy"
              ></iframe>
              <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-bounce">
                <img src="/images/spice-pin.png" alt="Spice Pin" class="h-12" />
              </div>
            </div>
          </div>
          <div>
            <h2 class="text-3xl font-semibold mb-4">Reserve a Table</h2>
            <form @submit.prevent="submitForm" class="space-y-4">
              <input type="text" placeholder="Name" class="w-full p-3 rounded bg-gray-800 text-white" required />
              <input type="email" placeholder="Email" class="w-full p-3 rounded bg-gray-800 text-white" required />
              <input type="date" class="w-full p-3 rounded bg-gray-800 text-white" required />
              <button type="submit" class="bg-gursha-gold text-black px-6 py-3 rounded-full w-full hover:scale-105 transition-transform duration-300">
                Book Now
              </button>
            </form>
          </div>
        </div>
      </section>
      <Footer />
    </div>
  </template>
  
  <script setup>
  import Header from '../Components/Header.vue';
  import Footer from '../Components/Footer.vue';
  import { router } from '@inertiajs/vue3';
  
  const submitForm = () => {
    console.log('Form submitted!');
    router.post('/reservations/book', { /* form data */ });
  };
  </script>