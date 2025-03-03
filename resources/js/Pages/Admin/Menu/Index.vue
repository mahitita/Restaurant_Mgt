<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

defineProps({ orders: Array });

const updateStatus = (order, newStatus) => {
    router.patch(`/admin/orders/${order.id}/status`, { status: newStatus }, {
        preserveScroll: true,
        onSuccess: () => alert("Order status updated!"),
    });
};
</script>

<template>
    <div class="max-w-6xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold mb-4 text-gray-700">Order Management</h2>

        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">Order ID</th>
                    <th class="border p-2">Customer</th>
                    <th class="border p-2">Order Type</th>
                    <th class="border p-2">Total Price</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Items</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="order in orders" :key="order.id" class="border text-center">
                    <td class="p-2">{{ order.id }}</td>
                    <td class="p-2">{{ order.user ? order.user.name : "Guest" }}</td>
                    <td class="p-2">{{ order.order_type }}</td>
                    <td class="p-2">${{ order.total_price }}</td>
                    <td class="p-2">
                        <span :class="{
                            'text-yellow-500': order.status === 'pending',
                            'text-blue-500': order.status === 'preparing',
                            'text-green-500': order.status === 'ready',
                            'text-purple-500': order.status === 'completed',
                            'text-red-500': order.status === 'cancelled'
                        }">
                            {{ order.status }}
                        </span>
                    </td>
                    <td class="p-2">
                        <ul>
                            <li v-for="item in order.order_items" :key="item.id">
                                {{ item.menu.name }} x {{ item.quantity }} (${{ item.price }})
                            </li>
                        </ul>
                    </td>
                    <td class="p-2">
                        <select @change="updateStatus(order, $event.target.value)" class="border p-1 rounded">
                            <option disabled selected>Update Status</option>
                            <option value="pending">Pending</option>
                            <option value="preparing">Preparing</option>
                            <option value="ready">Ready</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
