<template>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-4xl font-bold mb-8 text-gursha-primary">Waitlist Management</h1>
  
      <div v-if="flash.success" class="bg-green-100 p-4 mb-6 rounded-lg shadow-md text-green-800">
        {{ flash.success }}
      </div>
  
      <div v-if="waitlists.length" class="space-y-6">
        <div v-for="waitlist in waitlists" :key="waitlist.id" class="bg-white p-6 rounded-lg shadow-md">
          <p class="text-lg font-semibold text-gray-800">Customer: {{ waitlist.user_name }}</p>
          <p class="text-gray-600">Party Size: {{ waitlist.party_size }}</p>
          <p class="text-gray-600">Preferred Table: {{ waitlist.preferred_table }}</p>
          <p class="text-gray-600">Added: {{ waitlist.added_at }}</p>
          <p class="text-gray-600" v-if="waitlist.notified_at">Notified: {{ waitlist.notified_at }}</p>
          <div class="mt-4 flex items-center space-x-4">
            <label class="text-gray-700 font-medium">Status:</label>
            <select
              v-model="waitlist.status"
              @change="updateStatus(waitlist)"
              class="border p-2 rounded shadow-sm focus:ring-gursha-primary focus:border-gursha-primary"
            >
              <option value="waiting">Waiting</option>
              <option value="seated">Seated</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
      </div>
      <div v-else class="text-gray-600 text-lg">No customers on the waitlist.</div>
    </div>
  </template>
  
  <script>
  import { Inertia } from '@inertiajs/inertia';
  
  export default {
    props: {
      waitlists: Array,
      tables: Array,
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
    methods: {
      updateStatus(waitlist) {
        Inertia.put(route('admin.waitlists.update', waitlist.id), { status: waitlist.status }, {
          preserveState: true,
          onSuccess: () => {
            console.log(`Status updated for ${waitlist.user_name} to ${waitlist.status}`);
          },
          onError: (errors) => {
            console.error('Status update failed:', errors);
            // Revert status on error
            waitlist.status = this.waitlists.find(w => w.id === waitlist.id).status;
          },
        });
      },
    },
  };
  </script>