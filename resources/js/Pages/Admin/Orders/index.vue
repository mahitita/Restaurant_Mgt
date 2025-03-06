<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Orders</h1>

        <!-- Success Message -->
        <div v-if="successMessage" class="p-3 bg-green-200 text-green-800 rounded mb-4">
          {{ successMessage }}
        </div>

        <!-- Orders Table -->
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 p-2">#</th>
              <th class="border border-gray-300 p-2">Customer</th>
              <th class="border border-gray-300 p-2">Total</th>
              <th class="border border-gray-300 p-2">Status</th>
              <th class="border border-gray-300 p-2">Date</th>
              <th class="border border-gray-300 p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(order, index) in orders" :key="order.id">
              <td class="border border-gray-300 p-2">{{ index + 1 }}</td>
              <td class="border border-gray-300 p-2">{{ order.user.name }}</td>
              <td class="border border-gray-300 p-2">${{ order.total }}</td>
              <td class="border border-gray-300 p-2">
                <span class="px-2 py-1 rounded" :class="statusClass(order.status)">
                  {{ order.status }}
                </span>
              </td>
              <td class="border border-gray-300 p-2">{{ formatDate(order.created_at) }}</td>
              <td class="border border-gray-300 p-2">
                <Link
                  :href="route('admin.orders.show', order.id)"
                  class="bg-blue-500 text-white px-3 py-1 rounded mr-2"
                >
                  View
                </Link>
                <button
                  @click="deleteOrder(order.id)"
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
    props: { orders: Array },
    data() {
      return { successMessage: "" };
    },
    methods: {
      deleteOrder(id) {
        if (confirm("Are you sure?")) {
          router.delete(route("admin.orders.destroy", id), {
            onSuccess: () => (this.successMessage = "Order deleted successfully."),
          });
        }
      },
      statusClass(status) {
        return {
          pending: "bg-yellow-200 text-yellow-800",
          processing: "bg-blue-200 text-blue-800",
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
