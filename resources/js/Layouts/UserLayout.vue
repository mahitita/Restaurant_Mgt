<template>
    <div class="min-h-screen bg-gray-100 flex flex-col">
      <Header />
      <main class="flex-grow">
        <slot />
      </main>
      <Footer />
    </div>
  </template>

  <script>
  import Header from '../Components/Header.vue';
  import Footer from '../Components/Footer.vue';

  export default {
    components: {
      Header,
      Footer,
    },
  };
  </script>