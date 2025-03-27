<!-- resources/js/Pages/Orders/MyOrders.vue -->
<template>
    <div class="container mx-auto py-8 px-4">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">My Orders</h1>
        </div>

        <!-- Success/Error Messages -->
        <transition name="fade">
          <div
            v-if="$page.props.flash?.success"
            class="p-4 bg-green-100 text-green-800 rounded-lg mb-6 flex justify-between items-center"
          >
            <span>{{ $page.props.flash.success }}</span>
            <button @click="$page.props.flash.success = null" class="text-green-800 hover:text-green-600">
              ✕
            </button>
          </div>
        </transition>
        <transition name="fade">
          <div
            v-if="$page.props.flash?.error"
            class="p-4 bg-red-100 text-red-800 rounded-lg mb-6 flex justify-between items-center"
          >
            <span>{{ $page.props.flash.error }}</span>
            <button @click="$page.props.flash.error = null" class="text-red-800 hover:text-red-600">
              ✕
            </button>
          </div>
        </transition>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-50 text-gray-700">
                <th class="p-4 text-left font-semibold">#</th>
                <th class="p-4 text-left font-semibold">Order Type</th>
                <th class="p-4 text-left font-semibold">Details</th>
                <th class="p-4 text-left font-semibold">Items</th>
                <th class="p-4 text-left font-semibold">Total Price</th>
                <th class="p-4 text-left font-semibold">Status</th>
                <th class="p-4 text-left font-semibold">Priority</th>
                <th class="p-4 text-left font-semibold">Wait Time (min)</th>
                <th class="p-4 text-left font-semibold">Ordered At</th>
                <th class="p-4 text-left font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(order, index) in orders.data"
                :key="order.id"
                class="border-b hover:bg-gray-50 transition"
                :class="{ 'bg-yellow-100': order.is_priority }"
              >
                <td class="p-4 text-gray-600">{{ index + 1 + (orders.current_page - 1) * orders.per_page }}</td>
                <td class="p-4 text-gray-800 capitalize">{{ order.order_type }}</td>
                <td class="p-4 text-gray-600">
                  <span v-if="order.order_type === 'dine-in'">Table: {{ order.table_id || 'N/A' }}</span>
                  <span v-else-if="order.order_type === 'takeout'">Pickup: {{ order.pickup_time || 'N/A' }}</span>
                  <span v-else-if="order.order_type === 'delivery'">Address: {{ order.delivery_address || 'N/A' }}</span>
                </td>
                <td class="p-4 text-gray-800">
                  <ul>
                    <li v-for="item in order.items" :key="item.name">
                      {{ item.name }} (x{{ item.quantity }}) - £{{ item.price }}
                    </li>
                  </ul>
                </td>
                <td class="p-4 text-gray-800">£{{ order.total_price }}</td>
                <td class="p-4 text-gray-800 capitalize">{{ order.status }}</td>
                <td class="p-4 text-gray-800">
                  <span
                    class="px-4 py-2 rounded-lg"
                    :class="order.is_priority ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'"
                  >
                    {{ order.is_priority ? 'High' : 'Normal' }}
                  </span>
                </td>
                <td class="p-4 text-gray-800">{{ order.estimated_wait_minutes || 'N/A' }}</td>
                <td class="p-4 text-gray-600">{{ order.ordered_at }}</td>
                <td class="p-4">
                  <button
                    @click="router.get(route('orders.track', order.id))"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                  >
                    Track Order
                  </button>
                </td>
              </tr>
              <tr v-if="!orders.data || orders.data.length === 0">
                <td colspan="10" class="p-4 text-center text-gray-500">No orders found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-between items-center">
          <span class="text-gray-600">
            Showing {{ orders.from || 0 }} to {{ orders.to || 0 }} of {{ orders.total || 0 }} orders
          </span>
          <div class="flex space-x-2">
            <button
              v-for="link in orders.links"
              :key="link.label"
              @click="router.get(link.url)"
              v-html="link.label"
              :class="[
                'px-4 py-2 rounded-lg',
                link.active ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700',
                !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300',
              ]"
              :disabled="!link.url"
            />
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, watch } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';

  const props = defineProps({
    orders: Object,
  });

  watch(
    () => usePage().props.flash,
    (newFlash) => {
      if (newFlash?.success || newFlash?.error) {
        setTimeout(() => {
          usePage().props.flash.success = null;
          usePage().props.flash.error = null;
        }, 3000);
      }
    },
    { deep: true }
  );
  </script>

  <style scoped>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.5s;
  }
  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }

  tr:hover {
    background-color: #f9fafb;
  }
  </style>