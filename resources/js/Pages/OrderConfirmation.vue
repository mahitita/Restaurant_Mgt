<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-4">Order Confirmation</h2>
        <div v-if="success" class="bg-green-100 text-green-700 p-4 rounded mb-6">
          {{ success }}
        </div>

        <!-- Order Details -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-xl font-bold mb-4">Order Details</h3>
          <p><strong>Order ID:</strong> {{ order.id }}</p>
          <p><strong>Type:</strong> {{ order.order_type }}</p>
          <p><strong>Total:</strong> ${{ order.total_price }}</p>
          <p v-if="order.table_id"><strong>Table:</strong> {{ order.table_id }}</p>
          <p v-if="order.pickup_time"><strong>Pickup Time:</strong> {{ order.pickup_time }}</p>
          <p v-if="order.delivery_address"><strong>Delivery Address:</strong> {{ order.delivery_address }}</p>
          <h4 class="font-semibold mt-4">Items:</h4>
          <ul class="list-disc ml-6">
            <li v-for="item in order.order_items" :key="item.id">
              Menu ID {{ item.menu_id }} - Quantity: {{ item.quantity }} - Price: ${{ item.price }}
            </li>
          </ul>
        </div>

        <!-- Payment Details -->
        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
          <h3 class="text-xl font-bold mb-4">Payment Details</h3>
          <p><strong>Method:</strong> {{ payment.payment_method }}</p>
          <p><strong>Amount:</strong> ${{ payment.amount }}</p>
          <p><strong>Deposit Paid:</strong> ${{ payment.deposit_amount }}</p>
          <p><strong>Deposit Refunded:</strong> {{ payment.deposit_refunded ? 'Yes' : 'No' }}</p>
          <p><strong>Paid At:</strong> {{ payment.paid_at }}</p>
          <p><strong>Status:</strong> {{ payment.status }}</p>
        </div>

        <!-- Reservation Details -->
        <div v-if="reservations && reservations.length" class="bg-white p-6 rounded-lg shadow-md mt-6">
          <h3 class="text-xl font-bold mb-4">Reservation Details</h3>
          <p><strong>Tables:</strong> {{ reservations.map(r => r.table_id).join(', ') }}</p>
          <p><strong>Time:</strong> {{ reservations[0].reservation_time }}</p>
        </div>

        <button
          @click="$inertia.get(route('orders.index'))"
          class="bg-blue-500 text-white px-4 py-2 mt-6 rounded hover:bg-blue-600"
        >
          View All Orders
        </button>
      </section>
    </UserLayout>
  </template>

  <script>
  import UserLayout from '../Layouts/UserLayout.vue';

  export default {
    components: { UserLayout },
    props: {
      order: {
        type: Object,
        required: true,
      },
      payment: {
        type: Object,
        required: true,
      },
      reservations: {
        type: Array,
        default: () => [], // Default to empty array if undefined
      },
      success: {
        type: String,
        default: null,
      },
    },
  };
  </script>