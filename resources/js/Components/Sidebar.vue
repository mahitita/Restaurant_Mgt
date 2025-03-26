<template>
    <aside
      :class="['fixed inset-y-0 left-0 z-20 h-screen bg-gray-800 text-white shadow-lg transition-all duration-300', sidebarWidth]"
    >
      <div class="flex items-center justify-between p-6 border-b border-gray-700">
        <h2 v-if="!isCollapsed" class="text-2xl font-bold">Admin Panel</h2>
        <button @click="toggleSidebar" class="text-white focus:outline-none">
          <svg
            v-if="isCollapsed"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
          <svg
            v-else
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav>
        <ul class="mt-4">
          <li v-for="item in navItems" :key="item.route">
            <Link
              :href="route(item.route)"
              class="flex items-center py-3 px-6 hover:bg-gray-700 hover:text-white transition duration-200"
              :class="{ 'bg-gray-700 text-white': isActive(item.route) }"
            >
              <span class="mr-3">{{ item.icon }}</span>
              <span v-if="!isCollapsed">{{ item.name }}</span>
            </Link>
          </li>
        </ul>
      </nav>
    </aside>
  </template>

  <script>
  import { ref, computed } from 'vue';
  import { Link, usePage } from '@inertiajs/vue3';

  export default {
    components: { Link },
    props: {
      isCollapsed: Boolean,
    },
    setup(props, { emit }) {
      const toggleSidebar = () => {
        emit('toggle', !props.isCollapsed);
      };

      const sidebarWidth = computed(() => (props.isCollapsed ? 'w-16' : 'w-64'));

      const navItems = [
        { name: 'Dashboard', route: 'admin.dashboard', icon: '🏠' },
        { name: 'Categories', route: 'admin.categories', icon: '📂' },
        { name: 'Orders', route: 'admin.orders.index', icon: '🛎️' },
        { name: 'Menus', route: 'admin.menus', icon: '🍽️' },
        { name: 'Tables', route: 'admin.tables.index', icon: '🪑' },
        { name: 'Reservations', route: 'admin.reservations.index', icon: '📝' },
        { name: 'Ingredients', route: 'admin.inventory.index', icon: '🍴 ' },
        { name: 'Inventory', route: 'admin.stocks.index', icon: '📦 ' },
        { name: 'Profit Report', route: 'admin.menus.profit-report', icon: '📈' },
        { name: 'Users', route: 'admin.users.index', icon: '👥 ' },
        { name: 'Ingredients-History', route: 'admin.inventory.stock-history', icon: '📜' },
        { name: 'Waitlist', route: 'admin.waitlists.index', icon: '⏳  ' },
        


        



      ];

      const page = usePage();
      const isActive = (routeName) => page.url.includes(routeName);

      return { toggleSidebar, sidebarWidth, navItems, isActive };
    },
  };
  </script>

  <style scoped>
  svg {
    transition: transform 0.3s;
  }
  </style>
