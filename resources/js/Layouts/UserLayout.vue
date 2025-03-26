<template>
    <div>
      <Header :auth="auth" />
      <main>
        <slot />
      </main>
      <Footer />
    </div>
  </template>

  <script setup>
  import Header from '../Components/Header.vue'; // Adjust path
  import Footer from '@/Components/Footer.vue';
  defineProps({
    auth: Object,
  });
  </script>