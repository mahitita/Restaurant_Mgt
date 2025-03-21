<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Your Reservations</h1>
  
        <div v-if="reservations.length === 0" class="text-center text-gray-500 text-lg">
          You have no reservations yet.
        </div>
  
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="reservation in reservations" :key="reservation.id" class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-lg font-semibold text-gray-800">Table {{ reservation.table_number }}</p>
            <p class="text-gray-600">Seats: {{ reservation.seats }}</p>
            <p class="text-gray-600">Time: {{ reservation.reservation_time }}</p>
            <p class="text-gray-600">Status: {{ reservation.status }}</p>
            <p class="text-gray-600">Deposit Paid: ${{ reservation.deposit_amount }}</p>
            <button
              v-if="reservation.status === 'confirmed'"
              @click="goToPreOrder(reservation.id)"
              class="mt-4 bg-orange-600 text-white px-4 py-2 rounded-full hover:bg-orange-700"
            >
              Pre-Order
            </button>
          </div>
        </div>
      </div>
    </UserLayout>
  </template>
  
  <script setup>
  import UserLayout from '../../Layouts/UserLayout.vue';
  import { router } from '@inertiajs/vue3';
  
  defineProps({
    reservations: {
      type: Array,
      default: () => [],
    },
  });
  
  const goToPreOrder = (reservationId) => {
    router.visit(route('orders.preorder', { reservation_ids: reservationId }));
  };
  </script>