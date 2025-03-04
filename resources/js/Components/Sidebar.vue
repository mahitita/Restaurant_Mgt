<!-- resources/js/components/Sidebar.vue -->
<template>
    <aside class="w-64 bg-white border-r border-gray-200">
      <div class="p-4 text-lg font-bold text-gray-800">Restaurant Admin</div>
      <nav class="mt-6">
        <ul>
          <li class="hover:bg-blue-500 hover:text-white transition duration-200">
            <inertia-link :href="route('dashboard')" class="flex items-center py-2 px-4 text-gray-600">
              <span class="material-icons">dashboard</span>
              Dashboard
            </inertia-link>
          </li>
          <li class="hover:bg-blue-500 hover:text-white transition duration-200">
            <inertia-link :href="route('menu')" class="flex items-center py-2 px-4 text-gray-600">
              <span class="material-icons">restaurant_menu</span>
              Menu Management
            </inertia-link>
          </li>
          <!-- Add more links as needed -->
        </ul>
      </nav>
    </aside>
  </template>

  <script>
  export default {
    name: "Sidebar",
  };
  </script>

  <style scoped>
  .material-icons {
    margin-right: 8px;
  }
  </style>
