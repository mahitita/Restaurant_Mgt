<template>
    <AdminLayout>
      <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header -->
        <header class="mb-8">
          <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900">Welcome, {{ auth?.user?.name ?? 'Admin' }}!</h1>
          <p class="text-gray-600 mt-2">Overview for {{ currentDate }}</p>
        </header>

        <!-- Quick Stats -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center space-x-4"
          >
            <div :class="`p-3 rounded-full ${stat.bgColor}`">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path v-if="stat.label === 'Total Orders'" d="M3 3h14a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V5a2 2 0 012-2zm2 4h10v2H5V7zm0 4h6v2H5v-2z" />
                <path v-else-if="stat.label === 'Revenue Today'" d="M4 9V3h12v6H4zm0 2h12v6H4v-6zm6-8v14M2 7h16" />
                <path v-else-if="stat.label === 'Active Reservations'" d="M6 2a4 4 0 00-4 4v8a4 4 0 004 4h8a4 4 0 004-4V6a4 4 0 00-4-4H6zm4 12a2 2 0 110-4 2 2 0 010 4z" />
                <path v-else-if="stat.label === 'Low Inventory'" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 110-12 6 6 0 010 12zm-1-9h2v4H9V7zm0 6h2v2H9v-2z" />
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-medium text-gray-600">{{ stat.label }}</h3>
              <p class="text-2xl font-bold" :class="stat.color">{{ stat.value }}</p>
            </div>
          </div>
        </section>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Recent Orders -->
          <section class="bg-white p-6 rounded-xl shadow-md col-span-1 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-800">Recent Orders</h2>
              <Link :href="route('admin.orders.index')" class="text-orange-600 hover:underline text-sm">View All</Link>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="p-3 text-left font-semibold">Order ID</th>
                    <th class="p-3 text-left font-semibold">Customer</th>
                    <th class="p-3 text-left font-semibold">Total</th>
                    <th class="p-3 text-left font-semibold">Status</th>
                    <th class="p-3 text-left font-semibold">Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="order in recentOrders" :key="order.id" class="border-b hover:bg-gray-50 transition">
                    <td class="p-3">{{ order.id }}</td>
                    <td class="p-3">{{ order.customer }}</td>
                    <td class="p-3">Br {{ order.total }}</td>
                    <td class="p-3">
                      <span class="px-2 py-1 rounded-full text-xs font-medium" :class="getStatusClass(order.status)">
                        {{ order.status }}
                      </span>
                    </td>
                    <td class="p-3">{{ order.created_at }}</td>
                  </tr>
                  <tr v-if="!recentOrders.length">
                    <td colspan="5" class="p-3 text-center text-gray-500">No recent orders.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- Pending Tasks -->
          <section class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Pending Tasks</h2>
            <ul class="space-y-3">
              <li class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                <span>Unprocessed Orders</span>
                <span class="font-semibold text-orange-600">{{ pendingTasks.unprocessed_orders }}</span>
              </li>
              <li class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                <span>Low Inventory Items</span>
                <span class="font-semibold text-red-600">{{ pendingTasks.low_inventory }}</span>
              </li>
              <li class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                <span>Pending Reservations</span>
                <span class="font-semibold text-blue-600">{{ pendingTasks.pending_reservations }}</span>
              </li>
            </ul>
          </section>

          <!-- Reservations -->
          <section class="bg-white p-6 rounded-xl shadow-md col-span-1 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
              <h2 class="text-xl font-semibold text-gray-800">Upcoming Reservations</h2>
              <Link :href="route('admin.reservations.index')" class="text-orange-600 hover:underline text-sm">View All</Link>
            </div>
            <div class="space-y-3">
              <div v-for="reservation in reservations" :key="reservation.id" class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <div>
                  <p class="font-medium text-gray-800">{{ reservation.customer }}</p>
                  <p class="text-sm text-gray-600">Table {{ reservation.table }} at {{ reservation.time }}</p>
                </div>
                <Link :href="route('admin.reservations.index', reservation.id)" class="text-orange-600 hover:underline text-sm">Details</Link>
              </div>
              <p v-if="!reservations.length" class="text-gray-500 text-center py-3">No upcoming reservations.</p>
            </div>
          </section>

          <!-- Revenue Chart -->
          <section class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Weekly Revenue</h2>
            <div class="h-48 flex items-end justify-between">
              <div v-for="(bar, index) in normalizedRevenueData" :key="index" class="flex-1 mx-1 bg-orange-600 rounded-t transition-all duration-300 hover:bg-orange-700 relative group">
                <div :style="{ height: `${bar.normalizedValue}%` }" class="w-full"></div>
                <span class="text-xs text-white text-center block mt-1">{{ bar.day }}</span>
                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                  ${{ bar.value }}
                </div>
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
            class="bg-orange-600 text-white p-4 rounded-lg shadow-md hover:bg-orange-700 hover:shadow-lg transition-all duration-300 text-center font-medium"
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
  const auth = props.auth ?? { user: null };

  const stats = ref([
    { label: 'Total Orders', value: props.ordersCount || 0, color: 'text-orange-600', bgColor: 'bg-orange-500' },
    { label: 'Revenue Today', value: `Br ${Number(props.revenue || 0)}`, color: 'text-green-600', bgColor: 'bg-green-500' },
    { label: 'Active Reservations', value: props.reservationsCount || 0, color: 'text-blue-600', bgColor: 'bg-blue-500' },
    { label: 'Low Inventory', value: props.lowInventoryCount || 0, color: 'text-red-600', bgColor: 'bg-red-500' },
  ]);

  const recentOrders = ref(props.recentOrders || []);
  const inventoryAlerts = ref(props.inventoryAlerts || []);
  const reservations = ref(props.reservations || []);
  const pendingTasks = ref(props.pendingTasks || { unprocessed_orders: 0, low_inventory: 0, pending_reservations: 0 });
  const revenueData = ref(props.revenueData || []);

  const quickLinks = [
    { label: 'Manage Inventory', href: route('admin.inventory.index') },
    { label: 'View Orders', href: route('admin.orders.index') },
    { label: 'Reservations', href: route('admin.reservations.index') },
    { label: 'Menu Settings', href: route('admin.menus') }, // New link
  ];

  const currentDate = computed(() => new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }));

  const normalizedRevenueData = computed(() => {
    const maxRevenue = Math.max(...revenueData.value.map(d => d.value), 1); // Avoid division by zero
    return revenueData.value.map(bar => ({
      ...bar,
      normalizedValue: (bar.value / maxRevenue) * 100, // Normalize to 0-100%
    }));
  });

  const getStatusClass = (status) => ({
    'pending': 'bg-yellow-100 text-yellow-800',
    'received': 'bg-orange-100 text-orange-800',
    'completed': 'bg-green-100 text-green-800',
    'processing': 'bg-blue-100 text-blue-800',
    'preparing': 'bg-purple-100 text-purple-800',
  }[status] || 'bg-gray-100 text-gray-800');
  </script>

  <style scoped>
  .animate-fade-in {
    animation: fadeIn 0.8s ease-in;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  </style>