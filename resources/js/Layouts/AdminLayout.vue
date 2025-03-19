<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden">
      <!-- Sidebar -->
      <Sidebar @toggle="syncSidebarState" :isCollapsed="isSidebarCollapsed" />

      <!-- Main Content -->
      <div
        class="flex flex-col flex-1 transition-all duration-300"
        :class="{ 'ml-64': !isSidebarCollapsed, 'ml-16': isSidebarCollapsed }"
      >
        <!-- Navbar -->
        <Navbar />

        <!-- Main Content Area -->
        <main class="flex-1 p-6 overflow-y-auto bg-gray-50">
          <slot />
        </main>
      </div>
    </div>
  </template>

  <script>
  import Sidebar from '../Components/Sidebar.vue';
  import Navbar from '../Components/Navbar.vue';
  import { ref } from 'vue';

  export default {
    components: { Sidebar, Navbar },
    setup() {
      const isSidebarCollapsed = ref(false);

      const syncSidebarState = (collapsed) => {
        isSidebarCollapsed.value = collapsed;
      };

      return { isSidebarCollapsed, syncSidebarState };
    },
  };
  </script>

  <style scoped>
  /* Custom scrollbar */
  main::-webkit-scrollbar {
    width: 8px;
  }

  main::-webkit-scrollbar-track {
    background: #f1f5f9;
  }

  main::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 4px;
  }
  </style>
