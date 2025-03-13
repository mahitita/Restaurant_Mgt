<template>
    <AdminLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">Manage Reservations</h2>
        <div class="mb-6">
          <label for="dateFilter" class="block font-semibold mb-2">Filter by Date:</label>
          <input
            type="date"
            v-model="selectedDate"
            @change="filterReservations"
            class="border p-2 rounded w-full max-w-xs"
          />
        </div>
        <div v-if="filteredReservations.length === 0" class="text-gray-500">
          No reservations found for this date.
        </div>
        <div v-else class="grid gap-6">
          <div
            v-for="reservation in filteredReservations"
            :key="reservation.id"
            class="bg-white p-6 rounded-lg shadow-md"
          >
            <h3 class="text-xl font-bold mb-2">Reservation #{{ reservation.id }}</h3>
            <p><strong>Table:</strong> {{ reservation.table_number }}</p>
            <p><strong>User:</strong> {{ reservation.user_name }}</p>
            <p><strong>Time:</strong> {{ reservation.reservation_time }}</p>
            <p><strong>Deposit:</strong> ${{ reservation.deposit_amount }}</p>
            <div class="flex items-center mt-2">
              <label class="mr-2 font-semibold">Status:</label>
              <select
                v-model="reservation.status"
                @change="updateStatus(reservation)"
                class="border p-2 rounded"
              >
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
        </div>
      </section>
    </AdminLayout>
  </template>

  <script>
  import { ref, computed } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    components: { AdminLayout },
    props: {
      reservations: Array,
      selectedDate: String,
    },
    setup(props) {
      const reservations = ref(props.reservations);
      const selectedDate = ref(props.selectedDate);

      const filteredReservations = computed(() => {
        return reservations.value.filter(reservation =>
          new Date(reservation.reservation_time).toDateString() === new Date(selectedDate.value).toDateString()
        );
      });

      const updateStatus = (reservation) => {
        Inertia.put(route('admin.reservations.status', reservation.id), { status: reservation.status }, {
          onSuccess: () => {
            console.log(`Reservation ${reservation.id} status updated to ${reservation.status}`);
          },
          onError: (errors) => alert("Status update failed: " + JSON.stringify(errors)),
        });
      };

      const filterReservations = () => {
        Inertia.get(route('admin.reservations.index'), { date: selectedDate.value }, {
          preserveState: true,
          onSuccess: (page) => {
            reservations.value = page.props.reservations;
          },
        });
      };

      return { reservations, selectedDate, filteredReservations, updateStatus, filterReservations };
    },
  };
  </script>
