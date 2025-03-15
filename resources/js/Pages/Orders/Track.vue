<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">Track Your Order</h2>
        <div class="bg-white p-6 rounded-lg shadow-md">
          <p><strong>Order ID:</strong> {{ order.id }}</p>
          <p><strong>Total:</strong> ${{ order.total_price }}</p>
          <p><strong>Status:</strong> {{ order.status }}</p>
          <p><strong>Estimated Wait:</strong> {{ order.estimated_wait_minutes }} minutes</p>
          <h4 class="font-semibold mt-4">Items:</h4>
          <ul class="list-disc ml-6">
            <li v-for="item in order.items" :key="item.name">
              {{ item.name }} - Quantity: {{ item.quantity }}
            </li>
          </ul>
        </div>
      </section>
    </UserLayout>
  </template>

  <script>
  import { ref, onMounted, onUnmounted } from 'vue';
  import UserLayout from '../Layouts/UserLayout.vue';

  export default {
    components: { UserLayout },
    props: {
      order: Object,
    },
    setup(props) {
      const order = ref(props.order);

      onMounted(() => {
        window.Echo.channel(`orders.${props.order.id}`)
          .listen('OrderUpdated', (e) => {
            order.value.status = e.order.status;
            order.value.estimated_wait_minutes = e.order.estimated_wait_minutes;
          });
      });

      onUnmounted(() => {
        window.Echo.leave(`orders.${props.order.id}`);
      });

      return { order };
    },
  };
  </script>
