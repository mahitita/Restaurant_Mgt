<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">My Reservations</h2>
        <div v-if="reservations.length === 0" class="text-gray-500">
          You have no reservations yet.
        </div>
        <div v-else class="grid gap-6">
          <div
            v-for="reservation in reservations"
            :key="reservation.id"
            class="bg-white p-6 rounded-lg shadow-md"
          >
            <h3 class="text-xl font-bold mb-2">Table {{ reservation.table_number }}</h3>
            <p><strong>Seats:</strong> {{ reservation.seats }}</p>
            <p><strong>Reserved For:</strong> {{ reservation.reservation_time }}</p>
            <p><strong>Status:</strong> {{ reservation.status }}</p>
            <p><strong>Deposit Paid:</strong> ${{ reservation.deposit_amount }}</p>
          </div>
        </div>
        <button
          @click="$inertia.get(route('tables.index'))"
          class="bg-blue-500 text-white px-4 py-2 mt-6 rounded"
        >
          Reserve More Tables
        </button>
      </section>
    </UserLayout>
  </template>

  <script>
import UserLayout from "@/Layouts/UserLayout.vue";
  export default {
    components: { UserLayout },
    props: {
      reservations: Array,
    },
  };
  </script>
