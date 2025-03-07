<script setup>
// import { defineProps } from "vue";

defineProps({ orders: Array });
</script>

<template>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Your Orders</h2>
        <div v-if="orders.length === 0" class="text-center text-gray-500">
            No orders placed yet.
        </div>
        <div v-else>
            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Order ID</th>
                        <th class="p-2">Total</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders" :key="order.id">
                        <td class="p-2">{{ order.id }}</td>
                        <td class="p-2">${{ order.total_price }}</td>
                        <td class="p-2">{{ order.status }}</td>
                        <td class="p-2">{{ order.created_at }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
