<template>
    <aside :class="sidebarClass" class="bg-gray-800 text-white shadow-lg transition-all duration-300">
      <div class="flex items-center justify-between p-4">
        <h2 v-if="!isCollapsed" class="text-xl font-bold">Admin Panel</h2>
        <button @click="toggleSidebar" class="text-white focus:outline-none">
          <span class="material-icons">menu</span>
        </button>
      </div>
      <nav :class="!isCollapsed ? 'block' : 'hidden'">
        <ul>
          <li>
            <Link :href="route('admin.dashboard')" class="flex items-center py-2 px-4 hover:bg-gray-700 transition-all">
              <span class="material-icons mr-2">dashboard</span>
              <span v-if="!isCollapsed">Dashboard</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/menu" class="flex items-center py-2 px-4 hover:bg-gray-700 transition-all">
              <span class="material-icons mr-2">restaurant_menu</span>
              <span v-if="!isCollapsed">Menu</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/orders" class="flex items-center py-2 px-4 hover:bg-gray-700 transition-all">
              <span class="material-icons mr-2">receipt</span>
              <span v-if="!isCollapsed">Orders</span>
            </Link>
          </li>
        </ul>
      </nav>
    </aside>
  </template>

  <script>
  import { ref, computed } from 'vue';
  import { Link } from '@inertiajs/vue3';

  export default {
    components: { Link },
    setup() {
      const isCollapsed = ref(false); // Sidebar is expanded by default

      const toggleSidebar = () => {
        isCollapsed.value = !isCollapsed.value;
      };

      const sidebarClass = computed(() => {
        return isCollapsed.value ? 'w-16' : 'w-64'; // Change width based on collapsed state
      });

      return { isCollapsed, toggleSidebar, sidebarClass };
    }
  };
  </script>

  <style scoped>
  .material-icons {
    font-size: 24px; /* Adjust icon size */
  }
  </style>
