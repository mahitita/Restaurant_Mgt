<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Order Details</h1>

        <div class="mb-4">
          <strong>Customer:</strong> {{ order.user.name }}
        </div>
        <div class="mb-4">
          <strong>Status:</strong>
          <span class="px-2 py-1 rounded" :class="statusClass(order.status)">
            {{ order.status }}
          </span>
        </div>

        <!-- Order Items -->
        <h2 class="text-xl font-semibold mb-2">Items</h2>
        <ul>
          <li v-for="item in order.items" :key="item.id" class="mb-2">
            {{ item.menu.name }} - ${{ item.price }} x {{ item.quantity }}
          </li>
        </ul>

        <!-- Update Status -->
        <div class="mt-4">
          <select v-model="order.status" class="border p-2 rounded">
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <button @click="updateOrder" class="bg-blue-500 text-white px-3 py-1 rounded ml-2">
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
    props: { order: Object },
    methods: {
      updateOrder() {
        router.put(route("admin.orders.update", this.order.id), {
          status: this.order.status,
        });
      },
      statusClass(status) {
        return {
          pending: "bg-yellow-200 text-yellow-800",
          processing: "bg-blue-200 text-blue-800",
          completed: "bg-green-200 text-green-800",
          cancelled: "bg-red-200 text-red-800",
        }[status];
      },
    },
  };
  </script>
