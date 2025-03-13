<template>
    <div>
      <h1>Manage Tables</h1>
      <div v-for="table in tables" :key="table.id">
        <p>Table {{ table.table_number }} - Status: {{ table.status }}</p>
        <select v-model="table.status" @change="updateStatus(table)">
          <option value="available">Available</option>
          <option value="reserved">Reserved</option>
          <option value="occupied">Occupied</option>
        </select>
      </div>
    </div>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { Inertia } from '@inertiajs/inertia';

  const props = defineProps(['tables']);
  const tables = ref(props.tables);

  const updateStatus = (table) => {
    Inertia.put(route('admin.tables.status', table.id), { status: table.status });
  };
  </script>
