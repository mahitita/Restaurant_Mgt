<template>
    <AdminLayout>
      <div class="p-6 bg-gray-100 min-h-screen">
        <!-- Welcome Header -->
        <header class="mb-8">
          <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 animate-fade-in">
            Welcome, {{ auth.user.name }}!
          </h1>
          <p class="text-gray-600 mt-2">Here’s what’s happening at Gursha today, {{ currentDate }}.</p>
        </header>
  
        <!-- Quick Stats -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div v-for="stat in stats" :key="stat.label" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-700">{{ stat.label }}</h3>
            <p class="text-3xl font-bold" :class="stat.color">{{ stat.value }}</p>
          </div>
        </section>
  
        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Recent Orders -->
          <section class="bg-white p-6 rounded-lg shadow-md col-span-1 lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Orders</h2>
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-200">
                  <tr>
                    <th class="p-3 text-left">Order ID</th>
                    <th class="p-3 text-left">Customer</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in recentOrders" :key="order.id" class="border-b hover:bg-gray-50 transition">
                    <td class="p-3">{{ order.id }}</td>
                    <td class="p-3">{{ order.customer }}</td>
                    <td class="p-3">${{ order.total }}</td>
                    <td class="p-3">
                      <span :class="getStatusClass(order.status)">{{ order.status }}</span>
                    </td>
                    <td class="p-3">
                      <Link :href="route('admin.orders.show', order.id)" class="text-orange-600 hover:underline">View</Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
  
          <!-- Inventory Alerts -->
          <section class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Inventory Alerts</h2>
            <ul class="space-y-3">
              <li v-for="item in inventoryAlerts" :key="item.id" class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                <span>{{ item.name }} ({{ item.quantity }} left)</span>
                <Link :href="route('admin.inventory.index')" class="text-orange-600 hover:underline text-sm">Restock</Link>
              </li>
              <li v-if="!inventoryAlerts.length" class="text-gray-500 text-center">All good!</li>
            </ul>
          </section>
  
          <!-- Reservation Overview -->
          <section class="bg-white p-6 rounded-lg shadow-md col-span-1 lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Upcoming Reservations</h2>
            <div class="space-y-3">
              <div v-for="reservation in reservations" :key="reservation.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                  <p class="font-medium">{{ reservation.customer }}</p>
                  <p class="text-sm text-gray-600">Table {{ reservation.table }} at {{ reservation.time }}</p>
                </div>
                <!-- <Link :href="route('admin.reservations.index')" class="text-orange-600 hover:underline">Manage</Link> -->
              </div>
              <p v-if="!reservations.length" class="text-gray-500 text-center">No upcoming reservations.</p>
            </div>
          </section>
  
          <!-- Revenue Chart (Placeholder) -->
          <section class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Daily Revenue</h2>
            <div class="h-48 flex items-end justify-between">
              <div v-for="(bar, index) in revenueData" :key="index" class="flex-1 mx-1 bg-orange-600 rounded-t" :style="{ height: `${bar.value}%` }">
                <span class="text-xs text-white text-center block mt-1">{{ bar.day }}</span>
              </div>
            </div>
          </section>
        </div>
  
        <!-- Quick Links -->
        <section class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <Link
            v-for="link in quickLinks"
            :key="link.label"
            :href="link.href"
            class="bg-orange-600 text-white p-4 rounded-lg shadow-md hover:bg-orange-700 hover:shadow-lg transform hover:scale-105 transition-all duration-300 text-center"
          >
            {{ link.label }}
          </Link>
        </section>
      </div>
    </AdminLayout>
  </template>
  
  <script setup>
  import AdminLayout from '@/Layouts/AdminLayout.vue';
  import { Link, usePage } from '@inertiajs/vue3';
  import { ref, computed } from 'vue';
  
  const { props } = usePage();
  const auth = props.auth;
  
  // Dynamic Data (replace with real data from props)
  const stats = ref([
    { label: 'Total Orders', value: props.ordersCount || 25, color: 'text-orange-600' },
    { label: 'Revenue Today', value: `$${(props.revenue || 1250).toFixed(2)}`, color: 'text-green-600' },
    { label: 'Active Reservations', value: props.reservationsCount || 8, color: 'text-blue-600' },
    { label: 'Low Inventory', value: props.lowInventoryCount || 3, color: 'text-red-600' },
  ]);
  
  const recentOrders = ref(props.recentOrders || [
    { id: 1, customer: 'John Doe', total: 45.50, status: 'pending' },
    { id: 2, customer: 'Jane Smith', total: 32.00, status: 'completed' },
    { id: 3, customer: 'Emily Brown', total: 60.75, status: 'processing' },
  ]);
  
  const inventoryAlerts = ref(props.inventoryAlerts || [
    { id: 1, name: 'Flour', quantity: 5 },
    { id: 2, name: 'Tomatoes', quantity: 2 },
  ]);
  
  const reservations = ref(props.reservations || [
    { id: 1, customer: 'Mike Lee', table: 5, time: '6:00 PM' },
    { id: 2, customer: 'Sara Kim', table: 3, time: '7:30 PM' },
  ]);
  
  const revenueData = ref([
    { day: 'Mon', value: 60 },
    { day: 'Tue', value: 80 },
    { day: 'Wed', value: 50 },
    { day: 'Thu', value: 70 },
    { day: 'Fri', value: 90 },
    { day: 'Sat', value: 100 },
    { day: 'Sun', value: 85 },
  ]);
  
  const quickLinks = [
    { label: 'Manage Inventory', href: route('admin.inventory.index') },
    { label: 'View Orders', href: route('admin.orders.index') },
  ];
  
  const currentDate = computed(() => new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }));
  
  const getStatusClass = (status) => ({
    'pending': 'text-yellow-600 font-semibold',
    'completed': 'text-green-600 font-semibold',
    'processing': 'text-blue-600 font-semibold',
  }[status] || 'text-gray-600');
  </script>
  
  <style scoped>
  .animate-fade-in {
    animation: fadeIn 1s ease-in;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  </style>