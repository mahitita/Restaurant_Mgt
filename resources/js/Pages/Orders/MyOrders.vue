<template>
    <UserLayout>
      <div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md p-6">
          <!-- Header -->
          <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900">My Orders</h1>
            <Link href="/" class="text-orange-600 hover:text-orange-700 font-medium transition-colors">
              Back to Home
            </Link>
          </div>

          <!-- Flash Messages -->
          <transition name="fade">
            <div
              v-if="$page.props.flash?.success"
              class="mb-6 p-4 bg-green-50 text-green-700 rounded-lg flex justify-between items-center shadow-sm"
            >
              <span>{{ $page.props.flash.success }}</span>
              <button @click="$page.props.flash.success = null" class="text-green-700 hover:text-green-900">
                ✕
              </button>
            </div>
          </transition>
          <transition name="fade">
            <div
              v-if="$page.props.flash?.error"
              class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg flex justify-between items-center shadow-sm"
            >
              <span>{{ $page.props.flash.error }}</span>
              <button @click="$page.props.flash.error = null" class="text-red-700 hover:text-red-900">
                ✕
              </button>
            </div>
          </transition>

          <!-- Orders List -->
          <div v-if="orders.data && orders.data.length > 0" class="space-y-6">
            <div
              v-for="(order, index) in orders.data"
              :key="order.id"
              class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-shadow shadow-sm"
              :class="{ 'bg-yellow-50': order.is_priority }"
            >
              <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Order Number -->
                <div>
                  <p class="text-sm text-gray-500">Order #</p>
                  <p class="font-semibold text-gray-800">
                    {{ index + 1 + (orders.current_page - 1) * orders.per_page }}
                  </p>
                </div>

                <!-- Order Type & Details -->
                <div>
                  <p class="text-sm text-gray-500">Type</p>
                  <p class="font-semibold text-gray-800 capitalize">{{ order.order_type }}</p>
                  <p class="text-sm text-gray-600 mt-1">
                    <span v-if="order.order_type === 'dine-in'">Table: {{ order.table_id || 'N/A' }}</span>
                    <span v-else-if="order.order_type === 'takeout'">Pickup: {{ order.pickup_time || 'N/A' }}</span>
                    <span v-else-if="order.order_type === 'delivery'">Address: {{ order.delivery_address || 'N/A' }}</span>
                  </p>
                </div>

                <!-- Items -->
                <div>
                  <p class="text-sm text-gray-500">Items</p>
                  <ul class="text-sm text-gray-700">
                    <li v-for="item in order.items" :key="item.name" class="truncate">
                      {{ item.name }} (x{{ item.quantity }}) - Br {{ item.price }}
                    </li>
                  </ul>
                </div>

                <!-- Total Price & Priority -->
                <div>
                  <p class="text-sm text-gray-500">Total</p>
                  <p class="font-semibold text-gray-800">Br {{ order.total_price }}</p>
                  <p class="text-sm text-gray-500 mt-1">Priority</p>
                  <span
                    class="inline-block px-2 py-1 text-xs font-medium rounded-full"
                    :class="order.is_priority ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'"
                  >
                    {{ order.is_priority ? 'High' : 'Normal' }}
                  </span>
                </div>

                <!-- Ordered At & Action -->
                <div class="flex flex-col justify-between items-end">
                  <div>
                    <p class="text-sm text-gray-500">Ordered</p>
                    <p class="text-sm text-gray-700">{{ formatDate(order.ordered_at) }}</p>
                  </div>
                  <!-- <button
                    @click="router.get(route('orders.track', order.id))"
                    class="mt-2 px-4 py-1 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors text-sm"
                  >
                    Track Order
                  </button> -->
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <p class="text-gray-600 text-lg">No orders found.</p>
            <Link href="/menu" class="mt-4 inline-block text-orange-600 hover:text-orange-700 font-semibold">
              Start Ordering
            </Link>
          </div>

          <!-- Pagination -->
          <div v-if="orders.data && orders.data.length > 0" class="mt-8 flex justify-between items-center">
            <span class="text-sm text-gray-600">
              Showing {{ orders.from || 0 }} to {{ orders.to || 0 }} of {{ orders.total || 0 }} orders
            </span>
            <div class="flex space-x-2">
              <button
                v-for="link in orders.links"
                :key="link.label"
                @click="router.get(link.url)"
                v-html="link.label"
                :class="[
                  'px-3 py-1 rounded-md text-sm font-medium',
                  link.active ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700',
                  !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300',
                ]"
                :disabled="!link.url"
              />
            </div>
          </div>
        </div>
      </div>
    </UserLayout>
  </template>

  <script setup>
  import { ref, watch } from 'vue';
  import { router, usePage, Link } from '@inertiajs/vue3';
  import UserLayout from '@/Layouts/UserLayout.vue';

  const props = defineProps({
    orders: Object,
  });

  const formatDate = (date) => {
    return new Date(date).toLocaleString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

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
    transition: opacity 0.3s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  </style>