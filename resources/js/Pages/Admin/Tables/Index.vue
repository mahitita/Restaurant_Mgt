<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
  tables: Array,
});

const deleteTable = (id) => {
  if (confirm('Are you sure you want to delete this table?')) {
    router.delete(`/admin/tables/${id}`);
  }
};
</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Manage Tables</h1>
    <router-link href="/admin/tables/create" class="bg-blue-500 text-white px-4 py-2 rounded">Add Table</router-link>
    <table class="w-full mt-4 border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-200">
          <th class="border p-2">Table #</th>
          <th class="border p-2">Seats</th>
          <th class="border p-2">Status</th>
          <th class="border p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="table in tables" :key="table.id">
          <td class="border p-2">{{ table.table_number }}</td>
          <td class="border p-2">{{ table.seats }}</td>
          <td class="border p-2">{{ table.status }}</td>
          <td class="border p-2">
            <router-link :href="`/admin/tables/${table.id}/edit`" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</router-link>
            <button @click="deleteTable(table.id)" class="bg-red-500 text-white px-2 py-1 ml-2 rounded">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
