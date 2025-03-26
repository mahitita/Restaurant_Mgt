<!-- resources/js/Pages/AboutUs.vue -->
<template>
    <div class="min-h-screen bg-black text-white">
      <Header />
      <section class="py-16 container mx-auto">
        <h1 class="text-5xl font-bold text-center mb-12 animate-fade-in">Our Story</h1>
        <div class="relative">
          <div class="parallax bg-cover bg-center h-96" style="background-image: url('/images/ethiopia-landscape.png')"></div>
          <p class="text-lg mt-8">Gursha began as a dream to bring Ethiopia’s communal dining to the world with a modern flair.</p>
        </div>
        <div class="mt-16">
          <h2 class="text-3xl font-semibold mb-8">Our Journey</h2>
          <div class="space-y-12">
            <div v-for="event in timeline" :key="event.year" class="flex items-center animate-slide-up">
              <div class="w-1/4 text-2xl font-bold text-gursha-gold">{{ event.year }}</div>
              <div class="w-3/4">{{ event.desc }}</div>
            </div>
          </div>
        </div>
      </section>
      <Footer />
    </div>
  </template>
  
  <script setup>
  import Header from '../Components/Header.vue';
  import Footer from '../Components/Footer.vue';
  
  const timeline = [
    { year: '2018', desc: 'Founded in a small Seattle kitchen.' },
    { year: '2020', desc: 'Opened our first location with a fusion menu.' },
    { year: '2025', desc: 'Celebrating a global taste revolution!' },
  ];
  </script>
  
  <style scoped>
  .parallax {
    background-attachment: fixed;
  }
  </style>