<template>
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Edit Reservation</h1>

      <div v-if="flash.success" class="bg-green-100 p-2 mb-4 rounded">
        {{ flash.success }}
      </div>
      <div v-if="flash.error" class="bg-red-100 p-2 mb-4 rounded">
        {{ flash.error }}
      </div>

      <form @submit.prevent="submitForm">
        <div class="mb-4">
          <label class="block text-gray-700">Table</label>
          <select v-model="form.table_id" class="border p-2 w-full" required>
            <option v-for="table in tables" :key="table.id" :value="table.id">
              {{ table.table_number }}
            </option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">User</label>
          <input v-model="form.user_name" type="text" class="border p-2 w-full" disabled />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Reservation Time</label>
          <input v-model="form.reservation_time" type="datetime-local" class="border p-2 w-full" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Status</label>
          <select v-model="form.status" class="border p-2 w-full" required>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white p-2 rounded">Update Reservation</button>
        <button type="button" @click="goToIndex" class="ml-2 text-gray-500">Cancel</button>
      </form>
    </div>
  </template>

  <script>
  import { router } from '@inertiajs/vue3';

  export default {
    props: {
      reservation: Object,
      tables: Array,
    },
    data() {
      return {
        form: { ...this.reservation },
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
    methods: {
      submitForm() {
        Inertia.put(`/admin/reservations/${this.reservation.id}`, this.form);
      },
      goToIndex() {
        router.get('/admin/reservations');
      },
    },
  };
  </script>