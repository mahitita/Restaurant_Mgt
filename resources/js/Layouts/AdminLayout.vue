<template>
    <div class="flex h-screen bg-gray-100">
      <!-- Sidebar -->
      <Sidebar />

      <!-- Main Content -->
      <div class="flex-1 flex flex-col">
        <Navbar />
        <main class="flex-1 p-6 overflow-y-auto">
          <slot />
        </main>
      </div>
    </div>
  </template>

  <script>
  import Sidebar from '../Components/Sidebar.vue';
  import Navbar from '../Components/Navbar.vue';

  export default {
    components: { Sidebar, Navbar },
  };
  </script>
