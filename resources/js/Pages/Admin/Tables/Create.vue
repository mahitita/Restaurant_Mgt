<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const form = ref({
  table_number: '',
  seats: 2,
  x_coordinate: 0,
  y_coordinate: 0,
  width: 100,
  height: 60,
  type: 'rectangle',
});

const submit = () => {
  router.post('/admin/tables', form.value);
};
</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Add Table</h1>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label>Table Number</label>
        <input v-model="form.table_number" class="border p-2 w-full" required />
      </div>
      <div>
        <label>Seats</label>
        <input v-model="form.seats" type="number" class="border p-2 w-full" required />
      </div>
      <div>
        <label>Table Type</label>
        <select v-model="form.type" class="border p-2 w-full">
          <option value="rectangle">Rectangle</option>
          <option value="round">Round</option>
          <option value="oval">Oval</option>
          <option value="square">Square</option>
        </select>
      </div>
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
    </form>
  </div>
</template>
