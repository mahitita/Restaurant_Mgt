<template>
    <AdminLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">Kitchen Orders</h2>
        <div class="grid gap-6">
          <div v-for="order in orders" :key="order.id" class="bg-white p-6 rounded-lg shadow-md" :class="{ 'border-2 border-red-500': order.is_priority }">
            <h3 class="text-xl font-bold mb-2">Order #{{ order.id }} {{ order.is_priority ? '(Priority)' : '' }}</h3>
            <p><strong>User:</strong> {{ order.user_name }}</p>
            <p><strong>Total:</strong> ${{ order.total_price }}</p>
            <p><strong>Estimated Wait:</strong> {{ order.estimated_wait_minutes }} minutes</p>
            <div class="flex items-center mt-2">
              <label class="mr-2 font-semibold">Status:</label>
              <select v-model="order.status" @change="updateStatus(order)" class="border p-2 rounded">
                <option value="received">Received</option>
                <option value="preparing">Preparing</option>
                <option value="ready">Ready</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <button @click="togglePriority(order)" class="mt-2 text-blue-500">
              {{ order.is_priority ? 'Remove Priority' : 'Set Priority' }}
            </button>
            <h4 class="font-semibold mt-4">Items:</h4>
            <ul class="list-disc ml-6">
              <li v-for="item in order.items" :key="item.name">
                {{ item.name }} - Quantity: {{ item.quantity }}
              </li>
            </ul>
          </div>
        </div>
      </section>
    </AdminLayout>
  </template>

  <script>
  import { ref } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    components: { AdminLayout },
    props: {
      orders: Array,
    },
    setup(props) {
      const orders = ref(props.orders);

      const updateStatus = (order) => {
        Inertia.put(route('admin.orders.status', order.id), { status: order.status }, {
          onSuccess: () => console.log(`Order ${order.id} updated to ${order.status}`),
          onError: (errors) => alert("Status update failed: " + JSON.stringify(errors)),
        });
      };

      const togglePriority = (order) => {
        Inertia.post(route('admin.orders.priority', order.id), {}, {
          onSuccess: () => order.is_priority = !order.is_priority,
        });
      };

      return { orders, updateStatus, togglePriority };
    },
  };
  </script>
