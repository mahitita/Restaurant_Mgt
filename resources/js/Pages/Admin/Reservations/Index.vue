<template>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
      <h1 class="text-3xl font-semibold mb-6">Reservation Management</h1>

      <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="bg-red-100 p-3 mb-4 rounded">
        {{ flash.error }}
      </div>

      <div class="flex mb-4 space-x-2">
        <button @click="goToTables" class="bg-blue-600 text-white p-3 rounded hover:bg-blue-700 transition duration-300">
          Back to Tables
        </button>
        <input
          v-model="searchQuery"
          type="text"
          class="border p-2 rounded w-full max-w-md"
          placeholder="Search by table number or user..."
        />
      </div>

      <table class="w-full border-collapse shadow-md">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-3 text-left">Table Number</th>
            <th class="p-3 text-left">User</th>
            <th class="p-3 text-left">Reservation Time</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="reservation in filteredReservations" :key="reservation.id">
            <td class="p-3">{{ reservation.table_number }}</td>
            <td class="p-3">{{ reservation.user_name }}</td>
            <td class="p-3">{{ reservation.reservation_time }}</td>
            <td class="p-3">
              <span :class="{'text-green-600': reservation.status === 'Confirmed', 'text-red-600': reservation.status === 'Cancelled'}">
                {{ reservation.status }}
              </span>
            </td>
            <td class="p-3">
              <button @click="goToEdit(reservation.id)" class="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
              <button @click="cancelReservation(reservation)" class="text-red-600 hover:text-red-800">Cancel</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <script>
  import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    layout: AdminLayout,
    props: {
      reservations: Array,
    },
    data() {
      return {
        searchQuery: '',
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
      filteredReservations() {
        if (!this.searchQuery) return this.reservations;
        const query = this.searchQuery.toLowerCase();
        return this.reservations.filter(reservation =>
          reservation.table_number.toLowerCase().includes(query) ||
          reservation.user_name.toLowerCase().includes(query)
        );
      },
    },
    methods: {
      goToTables() {
        router.get('/admin/tables');
      },
      goToEdit(reservationId) {
        router.get(`/admin/reservations/${reservationId}/edit`);
      },
      cancelReservation(reservation) {
        if (confirm(`Are you sure you want to cancel reservation for table ${reservation.table_number}?`)) {
          Inertia.delete(`/admin/reservations/${reservation.id}`);
        }
      },
    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 1200px; /* Adjust as necessary */
  }
  </style>