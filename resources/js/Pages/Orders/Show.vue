<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-16 text-center">
        <h1 class="text-4xl font-bold text-gursha-primary mb-8">Order Confirmed!</h1>
        <div v-if="flash.success" class="bg-green-100 p-4 mb-8 rounded-lg text-green-700">
          {{ flash.success }}
        </div>
        <p class="text-xl mb-4">Your order has been placed successfully.</p>
        <p class="text-2xl font-semibold text-gursha-accent">Estimated Wait Time: {{ order.estimated_wait_time }} minutes</p>
        <div class="mt-8">
          <h3 class="text-xl font-semibold mb-4">Order Details</h3>
          <div v-for="item in order.items" :key="item.id" class="flex justify-between max-w-md mx-auto mb-2">
            <span>{{ item.name }} (x{{ item.quantity }})</span>
            <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
          </div>
          <p class="text-xl font-bold mt-4">Total: ${{ order.total.toFixed(2) }}</p>
        </div>
        <Link href="/" class="mt-8 inline-block bg-gursha-primary text-white px-6 py-3 rounded-full hover:bg-opacity-90 transition-all duration-300">
          Back to Home
        </Link>
      </div>
    </UserLayout>
  </template>
  
  <script>
  import UserLayout from '../../Layouts/UserLayout.vue';
  import { Link } from '@inertiajs/inertia-vue3';
  
  export default {
    components: { UserLayout, Link },
    props: {
      order: Object,
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
  };
  </script>