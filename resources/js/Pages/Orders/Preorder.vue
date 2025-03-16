<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">Pre-Order for Your Reservation</h2>
        <div class="mb-6">
          <h3 class="text-xl font-bold">Your Reservation</h3>
          <p v-for="reservation in reservations" :key="reservation.id">
            Table {{ reservation.table_number }} - {{ reservation.reservation_time }}
          </p>
        </div>
        <div class="grid gap-6">
          <div v-for="item in menuItems" :key="item.id" class="bg-white p-4 rounded-lg shadow-md">
            <h4 class="font-bold">{{ item.name }}</h4>
            <p>${{ item.price }} (Prep: {{ item.prep_time }} min)</p>
            <input
              type="number"
              v-model="cart[item.id]"
              min="0"
              class="border p-2 rounded w-20 mt-2"
              placeholder="Qty"
            />
          </div>
        </div>
        <button
          @click="submitPreorder"
          class="bg-blue-500 text-white px-4 py-2 mt-6 rounded"
          :disabled="isSubmitting"
        >
          {{ isSubmitting ? 'Submitting...' : 'Place Pre-Order' }}
        </button>
      </section>
    </UserLayout>
  </template>

  <script>
  import { ref } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
import UserLayout from '@/Layouts/UserLayout.vue';
  export default {
    components: { UserLayout },
    props: {
      reservations: Array,
      menuItems: Array,
    },
    setup(props) {
      const cart = ref({});
      const isSubmitting = ref(false);

      const submitPreorder = () => {
        const cartItems = Object.entries(cart.value)
          .filter(([, qty]) => qty > 0)
          .map(([id, quantity]) => {
            const item = props.menuItems.find(i => i.id === parseInt(id));
            return { id: item.id, quantity, price: item.price };
          });

        if (cartItems.length === 0) {
          alert('Please add items to your pre-order.');
          return;
        }

        isSubmitting.value = true;
        Inertia.post(route('orders.preorder.store'), {
          reservation_ids: props.reservations.map(r => r.id),
          cart: cartItems,
        }, {
          onSuccess: () => isSubmitting.value = false,
          onError: (errors) => {
            alert("Pre-order failed: " + JSON.stringify(errors));
            isSubmitting.value = false;
          },
        });
      };

      return { cart, isSubmitting, submitPreorder };
    },
  };
  </script>
