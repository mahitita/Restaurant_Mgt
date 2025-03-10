<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  table: Object,
});

const form = ref({ ...props.table });

const submit = () => {
  router.put(`/admin/tables/${form.value.id}`, form.value);
};
</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Edit Table</h1>
    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label>Table Number</label>
        <input v-model="form.table_number" class="border p-2 w-full" required />
      </div>
      <div>
        <label>Seats</label>
        <input v-model="form.seats" type="number" class="border p-2 w-full" required />
      </div>
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
    </form>
  </div>
</template>
