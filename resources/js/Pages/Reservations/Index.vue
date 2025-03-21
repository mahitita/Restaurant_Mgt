<template>
  <UserLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-4xl font-bold mb-8 text-gursha-primary">Your Reservations</h1>

      <!-- Reservations List -->
      <div v-if="reservations.length" class="space-y-6">
        <div v-for="reservation in reservations" :key="reservation.id" class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-lg font-semibold text-gray-800">Table {{ reservation.table_number }}</p>
          <p class="text-gray-600">Time: {{ reservation.reservation_time }}</p>
          <p class="text-gray-600">Status: {{ reservation.status }}</p>
          <button
            v-if="reservation.status === 'confirmed' && !reservation.order_id"
            @click="preOrder(reservation.id)"
            class="mt-4 bg-gursha-primary text-white px-6 py-2 rounded-full hover:bg-gursha-accent"
          >
            Pre-Order
          </button>
        </div>
      </div>
      <div v-else class="text-gray-600 text-lg">No reservations yet.</div>

      <!-- Waitlist List -->
      <div v-if="waitlists.length" class="mt-12">
        <h2 class="text-3xl font-bold mb-6 text-gursha-primary">Your Waitlist</h2>
        <div v-for="waitlist in waitlists" :key="waitlist.id" class="bg-white p-6 rounded-lg shadow-md relative">
          <p class="text-lg font-semibold text-gray-800">Party Size: {{ waitlist.party_size }}</p>
          <p class="text-gray-600">Added: {{ new Date(waitlist.added_at).toLocaleString() }}</p>
          <p class="text-gray-600">Preferred Table: {{ waitlist.table_id ? tables.find(t => t.id === waitlist.table_id)?.table_number : 'Any' }}</p>
          <p class="text-gray-600">Status: {{ waitlist.status }}</p>
          <!-- Notification Banner -->
          <div
            v-if="waitlist.status === 'seated' && waitlist.notified_at"
            class="absolute top-0 left-0 right-0 bg-green-500 text-white text-center py-2 rounded-t-lg"
          >
            Your table is available now! Please confirm within 15 minutes.
          </div>
          <button
            v-if="waitlist.status === 'waiting'"
            @click="cancelWaitlist(waitlist.id)"
            class="mt-4 bg-red-600 text-white px-6 py-2 rounded-full hover:bg-red-700"
          >
            Cancel Waitlist
          </button>
          <button
            v-if="waitlist.status === 'seated'"
            @click="confirmTable(waitlist.id)"
            class="mt-4 bg-gursha-primary text-white px-6 py-2 rounded-full hover:bg-gursha-accent"
          >
            Confirm Table
          </button>
        </div>
      </div>
      <div v-else class="mt-6 text-gray-600 text-lg">Not on any waitlist.</div>
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