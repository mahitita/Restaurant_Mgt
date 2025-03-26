<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">Join the Waitlist</h2>
        <div v-if="waitlist" class="bg-white p-6 rounded-lg shadow-md">
          <p><strong>Party Size:</strong> {{ waitlist.party_size }}</p>
          <p><strong>Estimated Wait:</strong> {{ waitlist.estimated_wait_minutes }} minutes</p>
          <p>We’ll notify you when a table is ready!</p>
        </div>
        <div v-else>
          <form @submit.prevent="joinWaitlist" class="bg-white p-6 rounded-lg shadow-md">
            <label class="block font-semibold mb-2">Party Size:</label>
            <input
              type="number"
              v-model="partySize"
              min="1"
              class="border p-2 rounded w-full mb-4"
            />
            <button
              type="submit"
              class="bg-blue-500 text-white px-4 py-2 rounded"
              :disabled="isSubmitting"
            >
              {{ isSubmitting ? 'Joining...' : 'Join Waitlist' }}
            </button>
          </form>
          <p class="mt-4">Available Tables: {{ availableTables }}</p>
          <p>Average Turnover: {{ averageTurnover }} minutes</p>
        </div>
      </section>
    </UserLayout>
  </template>

  <script>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
  export default {
    components: { UserLayout },
    props: {
      waitlist: Object,
      availableTables: Number,
      averageTurnover: Number,
    },
    setup(props) {
      const partySize = ref(1);
      const isSubmitting = ref(false);

      const joinWaitlist = () => {
        isSubmitting.value = true;
        router.post(route('waitlist.store'), { party_size: partySize.value }, {
          onSuccess: () => isSubmitting.value = false,
          onError: (errors) => {
            alert("Failed to join waitlist: " + JSON.stringify(errors));
            isSubmitting.value = false;
          },
        });
      };

      return { partySize, isSubmitting, joinWaitlist };
    },
  };
  </script>
