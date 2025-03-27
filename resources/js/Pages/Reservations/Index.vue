<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <h1 class="text-4xl font-bold mb-8 text-gursha-primary tracking-tight">Your Reservations</h1>

        <!-- Reservations List -->
        <div v-if="reservations.length" class="space-y-6">
          <div
            v-for="reservation in reservations"
            :key="reservation.id"
            class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
          >
            <div class="flex justify-between items-start">
              <div>
                <p class="text-lg font-semibold text-gray-800">Table {{ reservation.table_number }}</p>
                <p class="text-gray-600 mt-1">Time: {{ formatDateTime(reservation.reservation_time) }}</p>
                <div class="flex items-center mt-1">
                  <span class="text-gray-600">Status:</span>
                  <span
                    :class="{
                      'text-green-600': getStatus(reservation.status) === 'confirmed',
                      'text-red-600': getStatus(reservation.status) === 'cancelled',
                      'text-yellow-600': getStatus(reservation.status) === 'pending',
                    }"
                    class="ml-2 font-medium"
                  >
                    {{ (getStatus(reservation.status) || 'unknown').charAt(0).toUpperCase() + (getStatus(reservation.status) || 'unknown').slice(1) }}
                  </span>
                </div>
              </div>
              <button
                v-if="getStatus(reservation.status) === 'confirmed' && (!reservation.order_id || reservation.order_id === 0 || reservation.order_id === '') && !isPastReservation(reservation.reservation_time)"
                @click="preOrder(reservation.id)"
                class="bg-gursha-primary text-white px-6 py-2 rounded-full hover:bg-gursha-accent transition-colors duration-300"
              >
                Pre-Order
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-600 text-lg bg-white p-6 rounded-lg shadow-md text-center">
          No reservations yet.
        </div>

        <!-- Waitlist List -->
        <div v-if="waitlists.length" class="mt-12">
          <h2 class="text-3xl font-bold mb-6 text-gursha-primary tracking-tight">Your Waitlist</h2>
          <div class="space-y-6">
            <div
              v-for="waitlist in waitlists"
              :key="waitlist.id"
              class="relative bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
            >
              <!-- Notification Banner -->
              <div
                v-if="waitlist.status === 'seated' && waitlist.notified_at"
                class="absolute top-0 left-0 right-0 bg-green-500 text-white text-center py-2 rounded-t-lg text-sm font-medium"
              >
                Your table is available now! Please confirm within 15 minutes.
              </div>
              <div :class="{ 'pt-8': waitlist.status === 'seated' && waitlist.notified_at }">
                <div class="flex justify-between items-start">
                  <div>
                    <p class="text-lg font-semibold text-gray-800">Party Size: {{ waitlist.party_size }}</p>
                    <p class="text-gray-600 mt-1">Added: {{ formatDateTime(waitlist.added_at) }}</p>
                    <p class="text-gray-600 mt-1">
                      Preferred Table: {{ waitlist.table_id ? tables.find(t => t.id === waitlist.table_id)?.table_number : 'Any' }}
                    </p>
                    <div class="flex items-center mt-1">
                      <span class="text-gray-600">Status:</span>
                      <span
                        :class="{
                          'text-green-600': waitlist.status === 'seated',
                          'text-yellow-600': waitlist.status === 'waiting',
                          'text-red-600': waitlist.status === 'cancelled',
                        }"
                        class="ml-2 font-medium"
                      >
                        {{ (waitlist.status || 'unknown').charAt(0).toUpperCase() + (waitlist.status || 'unknown').slice(1) }}
                      </span>
                    </div>
                  </div>
                  <div class="flex space-x-3">
                    <button
                      v-if="waitlist.status === 'waiting'"
                      @click="cancelWaitlist(waitlist.id)"
                      class="bg-red-600 text-white px-6 py-2 rounded-full hover:bg-red-700 transition-colors duration-300"
                    >
                      Cancel Waitlist
                    </button>
                    <button
                      v-if="waitlist.status === 'seated'"
                      @click="confirmTable(waitlist.id)"
                      class="bg-gursha-primary text-white px-6 py-2 rounded-full hover:bg-gursha-accent transition-colors duration-300"
                    >
                      Confirm Table
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="mt-6 text-gray-600 text-lg bg-white p-6 rounded-lg shadow-md text-center">
          Not on any waitlist.
        </div>
      </div>
    </UserLayout>
  </template>

  <script setup>
  import UserLayout from '../../Layouts/UserLayout.vue';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    reservations: {
      type: Array,
      default: () => [],
    },
    waitlists: {
      type: Array,
      default: () => [],
    },
    tables: {
      type: Array,
      default: () => [],
    },
  });

  // Format date and time for display
  const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString('en-US', {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  // Map numeric statuses to strings (if applicable)
  const statusMap = {
    1: 'confirmed',
    2: 'pending',
    3: 'cancelled',
  };
  const getStatus = (status) => (typeof status === 'number' ? statusMap[status] || 'unknown' : (status || 'unknown').toLowerCase());

  // Check if the reservation time is in the past
  const isPastReservation = (reservationTime) => {
    const reservationDate = new Date(reservationTime + 'Z'); // Treat as UTC
    const currentDate = new Date();
    const isPast = reservationDate < currentDate;
    console.log('Reservation Time:', reservationTime);
    console.log('Parsed Reservation Date:', reservationDate);
    console.log('Current Date:', currentDate);
    console.log('Is Past Reservation:', isPast);
    if (isNaN(reservationDate)) {
      console.error('Invalid reservation time:', reservationTime);
      return true; // Hide the button if the date is invalid
    }
    return isPast;
  };

  // Debug each reservation
  props.reservations.forEach((reservation) => {
    console.log('Reservation:', reservation);
    console.log('Status Condition:', getStatus(reservation.status) === 'confirmed');
    console.log('Order ID Condition:', !reservation.order_id || reservation.order_id === 0 || reservation.order_id === '');
    console.log('Time Condition:', !isPastReservation(reservation.reservation_time));
  });

  const preOrder = (reservationId) => {
    router.visit(route('orders.preorder', { reservation_ids: reservationId }));
  };

  const cancelWaitlist = (waitlistId) => {
    if (confirm('Are you sure you want to cancel your waitlist entry?')) {
      router.delete(`/waitlists/${waitlistId}`, {
        onSuccess: () => router.reload({ only: ['waitlists'] }),
        onError: (errors) => alert('Failed to cancel waitlist: ' + JSON.stringify(errors)),
      });
    }
  };

  const confirmTable = (waitlistId) => {
    console.log('Initiating POST to confirm table for waitlist ID:', waitlistId);
    router.post(route('reservations.confirm-from-waitlist', waitlistId), {}, {
      preserveState: false,
      onSuccess: () => {
        console.log('POST successful, should render ConfirmFromWaitlist');
      },
      onError: (errors) => {
        console.error('POST failed:', errors);
        alert('Failed to proceed to confirmation: ' + JSON.stringify(errors));
      },
      onFinish: () => {
        console.log('POST request finished');
      },
    });
  };
  </script>

  <style scoped>
  .relative {
    position: relative;
  }
  </style>