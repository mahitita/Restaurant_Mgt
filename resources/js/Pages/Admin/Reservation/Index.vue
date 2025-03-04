<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

defineProps({ reservations: Array });

const updateStatus = (reservation, newStatus) => {
    router.patch(`/admin/reservations/${reservation.id}/status`, { status: newStatus }, {
        preserveScroll: true,
        onSuccess: () => alert("Reservation status updated!"),
    });
};
</script>

<template>
    <div class="max-w-6xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold mb-4 text-gray-700">Table Reservations</h2>

        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">Reservation ID</th>
                    <th class="border p-2">Customer</th>
                    <th class="border p-2">Table</th>
                    <th class="border p-2">Reservation Time</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="reservation in reservations" :key="reservation.id" class="border text-center">
                    <td class="p-2">{{ reservation.id }}</td>
                    <td class="p-2">{{ reservation.user ? reservation.user.name : "Guest" }}</td>
                    <td class="p-2">{{ reservation.table.name }}</td>
                    <td class="p-2">{{ new Date(reservation.reservation_time).toLocaleString() }}</td>
                    <td class="p-2">
                        <span :class="{
                            'text-yellow-500': reservation.status === 'pending',
                            'text-blue-500': reservation.status === 'confirmed',
                            'text-red-500': reservation.status === 'cancelled'
                        }">
                            {{ reservation.status }}
                        </span>
                    </td>
                    <td class="p-2">
                        <select @change="updateStatus(reservation, $event.target.value)" class="border p-1 rounded">
                            <option disabled selected>Update Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
