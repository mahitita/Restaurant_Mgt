<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-8 text-gursha-primary">Confirm Your Table</h1>
  
        <div class="p-6 bg-green-100 border border-green-400 rounded-lg shadow-md">
          <p class="text-xl font-semibold text-gray-800">
            Selected Table: {{ table.table_number }}
          </p>
          <p class="text-gray-700">Seats: {{ table.seats }}</p>
          <p class="text-gray-700">Reservation Time: {{ waitlist.reservation_time }}</p>
          <p class="text-gray-700">Party Size: {{ waitlist.party_size }}</p>
          <p class="text-gray-700">Deposit: $10 (refunded if you pay cash on-site)</p>
  
          <select
            v-model="paymentType"
            class="border p-2 rounded w-full mt-4 shadow-sm focus:ring-gursha-primary"
          >
            <option value="card">Card</option>
            <option value="bank_transfer">Bank Transfer</option>
          </select>
          <input
            v-model="accountNumber"
            placeholder="Account Number"
            class="border p-2 rounded w-full mt-4 shadow-sm focus:ring-gursha-primary"
          />
          <button
            @click.prevent="reserveTable"
            class="mt-4 bg-gursha-primary text-white px-6 py-3 rounded-full hover:bg-gursha-accent hover:shadow-lg transform hover:scale-105 transition-all duration-300"
            :disabled="isReserving || !accountNumber"
          >
            {{ isReserving ? 'Reserving...' : 'Reserve with Deposit' }}
          </button>
        </div>
      </div>
    </UserLayout>
  </template>
  
  <script setup>
  import UserLayout from '../../Layouts/UserLayout.vue';
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  
  defineProps({
    waitlist: {
      type: Object,
      required: true,
    },
    table: {
      type: Object,
      required: true,
    },
  });
  
  const paymentType = ref('card');
  const accountNumber = ref('');
  const isReserving = ref(false);
  
  const reserveTable = () => {
    if (!accountNumber.value) {
      alert('Please enter your account number.');
      return;
    }
    isReserving.value = true;
    router.post(route('reservations.store-from-waitlist', waitlist.id), { // Remove props.
      table_id: table.id, // Remove props.
      payment: {
        paymentType: paymentType.value,
        accountNumber: accountNumber.value,
      },
    }, {
      preserveState: false,
      onSuccess: () => {
        console.log('STEP 4: Store POST successful, redirecting to /reservations');
        isReserving.value = false;
        router.visit(route('reservations.index'));
      },
      onError: (errors) => {
        console.error('STEP 4: Store POST failed:', errors);
        isReserving.value = false;
        alert('Reservation failed: ' + JSON.stringify(errors));
      },
      onFinish: () => {
        console.log('STEP 4: Store request finished');
        isReserving.value = false;
      },
    });
  };
  </script>