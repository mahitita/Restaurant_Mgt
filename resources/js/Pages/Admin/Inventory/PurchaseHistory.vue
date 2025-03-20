<template>
    <AdminLayout>
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Purchase History for {{ inventory.name }}</h1>

      <div v-if="flash.success" class="bg-green-100 p-2 mb-4 rounded">
        {{ flash.success }}
      </div>

      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2">Quantity</th>
            <th class="p-2">Total Cost</th>
            <th class="p-2">Supplier</th>
            <th class="p-2">Purchased At</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="purchase in purchases" :key="purchase.id">
            <td class="p-2">{{ purchase.quantity }}</td>
            <td class="p-2">${{ purchase.cost }}</td>
            <td class="p-2">{{ purchase.supplier || 'N/A' }}</td>
            <td class="p-2">{{ new Date(purchase.purchased_at).toLocaleString() }}</td>
          </tr>
        </tbody>
      </table>

      <button @click="goToInventory" class="mt-4 text-blue-500">Back to Inventory List</button>
    </div>
    </AdminLayout>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';

  export default {
    layout: AdminLayout,
    props: {
      inventory: Object,
      purchases: Array,
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
    methods: {
      goToInventory() {
        Inertia.visit('/admin/inventory');
      },
    },
  };
  </script>