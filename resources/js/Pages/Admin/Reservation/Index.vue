<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Reservations</h1>

        <!-- Success Message -->
        <div v-if="successMessage" class="p-3 bg-green-200 text-green-800 rounded mb-4">
          {{ successMessage }}
        </div>

        <!-- Reservations Table -->
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 p-2">#</th>
              <th class="border border-gray-300 p-2">Customer</th>
              <th class="border border-gray-300 p-2">Date</th>
              <th class="border border-gray-300 p-2">Status</th>
              <th class="border border-gray-300 p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(reservation, index) in reservations" :key="reservation.id">
              <td class="border border-gray-300 p-2">{{ index + 1 }}</td>
              <td class="border border-gray-300 p-2">{{ reservation.user.name }}</td>
              <td class="border border-gray-300 p-2">{{ formatDate(reservation.date) }}</td>
              <td class="border border-gray-300 p-2">
                <span class="px-2 py-1 rounded" :class="statusClass(reservation.status)">
                  {{ reservation.status }}
                </span>
              </td>
              <td class="border border-gray-300 p-2">
                <Link
                  :href="route('admin.reservations.show', reservation.id)"
                  class="bg-blue-500 text-white px-3 py-1 rounded mr-2"
                >
                  View
                </Link>
                <button
                  @click="deleteReservation(reservation.id)"
                  class="bg-red-500 text-white px-3 py-1 rounded"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </AdminLayout>
  </template>

  <script>
  import AdminLayout from "@/Layouts/AdminLayout.vue";
  import { router, Link } from "@inertiajs/vue3";

  export default {
    components: { AdminLayout, Link },
    props: { reservations: Array },
    data() {
      return { successMessage: "" };
    },
    methods: {
      deleteReservation(id) {
        if (confirm("Are you sure?")) {
          router.delete(route("admin.reservations.destroy", id), {
            onSuccess: () => (this.successMessage = "Reservation deleted successfully."),
          });
        }
      },
      statusClass(status) {
        return {
          pending: "bg-yellow-200 text-yellow-800",
          confirmed: "bg-blue-200 text-blue-800",
          completed: "bg-green-200 text-green-800",
          cancelled: "bg-red-200 text-red-800",
        }[status];
      },
      formatDate(date) {
        return new Date(date).toLocaleDateString();
      },
    },
  };
  </script>
