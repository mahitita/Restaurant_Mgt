<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({ menuItems: Array });
const orderType = ref("");
const selectedItems = ref([]);
const errors = ref({});

const addItem = (item) => {
    const existingItem = selectedItems.value.find((i) => i.id === item.id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        selectedItems.value.push({ id: item.id, name: item.name, price: item.price, quantity: 1 });
    }
};

const removeItem = (id) => {
    selectedItems.value = selectedItems.value.filter((item) => item.id !== id);
};

const submitOrder = () => {
    router.post("/admin/orders", { order_type: orderType.value, menu_items: selectedItems.value }, {
        onError: (err) => (errors.value = err),
    });
};
</script>

<template>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold mb-4 text-gray-700">Create New Order</h2>
        <label class="block text-sm font-medium text-gray-700">Order Type:</label>
        <select v-model="orderType" class="w-full border rounded-lg p-2">
            <option value="Dine-in">Dine-in</option>
            <option value="Takeout">Takeout</option>
            <option value="Delivery">Delivery</option>
        </select>

        <h3 class="text-lg font-semibold mt-4">Menu Items:</h3>
        <div v-for="item in menuItems" :key="item.id" class="flex justify-between p-2 border-b">
            <span>{{ item.name }} - ${{ item.price }}</span>
            <button @click="addItem(item)" class="bg-green-500 text-white px-2 py-1 rounded-lg">
                Add
            </button>
        </div>

        <h3 class="text-lg font-semibold mt-4">Selected Items:</h3>
        <div v-for="item in selectedItems" :key="item.id" class="flex justify-between p-2 border-b">
            <span>{{ item.name }} ({{ item.quantity }}) - ${{ item.price * item.quantity }}</span>
            <button @click="removeItem(item.id)" class="bg-red-500 text-white px-2 py-1 rounded-lg">
                Remove
            </button>
        </div>

        <button @click="submitOrder" class="mt-4 w-full bg-blue-500 text-white py-2 rounded-lg">
            Submit Order
        </button>
    </div>
</template>
