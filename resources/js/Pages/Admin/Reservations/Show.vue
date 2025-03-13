<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Reservation Details</h1>

        <div class="mb-4">
          <strong>Customer:</strong> {{ reservation.user.name }}
        </div>
        <div class="mb-4">
          <strong>Status:</strong>
          <span class="px-2 py-1 rounded" :class="statusClass(reservation.status)">
            {{ reservation.status }}
          </span>
        </div>

        <!-- Update Status -->
        <div class="mt-4">
          <select v-model="reservation.status" class="border p-2 rounded">
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <button @click="updateReservation" class="bg-blue-500 text-white px-3 py-1 rounded ml-2">
            Update Status
          </button>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script>
  import AdminLayout from "@/Layouts/AdminLayout.vue";
  import { router } from "@inertiajs/vue3";

  export default {
    components: { AdminLayout },
    props: { reservation: Object },
    methods: {
      updateReservation() {
        router.put(route("admin.reservations.update", this.reservation.id), {
          status: this.reservation.status,
        });
      },
      statusClass(status) {
        return {
          pending: "bg-yellow-200 text-yellow-800",
          confirmed: "bg-blue-200 text-blue-800",
          completed: "bg-green-200 text-green-800",
          cancelled: "bg-red-200 text-red-800",
        }[status];
      },
    },
  };
  </script>
