<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-4">Your Cart</h2>
        <div v-if="cartItems.length === 0" class="text-center text-gray-500">
          Your cart is empty.
        </div>
        <div v-else>
          <div v-for="item in cartItems" :key="item.id" class="flex justify-between py-2">
            <div class="flex items-center">
              <img :src="item.image" alt="item.name" class="w-20 h-20 object-cover rounded-md">
              <div class="ml-4">
                <h3 class="font-semibold">{{ item.name }}</h3>
                <p class="text-gray-600">{{ item.quantity }} x ${{ item.price }}</p>
              </div>
            </div>
            <button @click="removeFromCart(item.id)" class="text-red-500">Remove</button>
          </div>
          <div class="mt-4 text-lg font-semibold">
            Total: ${{ totalPrice }}
          </div>
        </div>
      </section>
    </UserLayout>
  </template>

  <script>
import UserLayout from '@/Layouts/UserLayout.vue';
  import { useCartStore } from '@/Stores/CartStore';
  export default {
    setup() {
      const cartStore = useCartStore();
      return {
        cartItems: cartStore.cartItems, // Make sure cartItems is reactive
        removeFromCart: cartStore.removeFromCart,
        totalPrice: cartStore.totalPrice
      };
    }
  };
  </script>
