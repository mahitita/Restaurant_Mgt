<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-4">Your Cart</h2>

        <!-- If the cart is empty -->
        <div v-if="cartItems.length === 0" class="text-center text-gray-500">
          Your cart is empty.
        </div>

        <!-- If there are items in the cart -->
        <div v-else>
          <div v-for="item in cartItems" :key="item.id" class="flex items-center justify-between py-4 border-b">
            <div class="flex items-center">
              <img :src="item.image" alt="item.name" class="w-20 h-20 object-cover rounded-md" />
              <div class="ml-4">
                <h3 class="font-semibold">{{ item.name }}</h3>
                <p class="text-gray-600">$ {{ item.price }}</p>
              </div>
            </div>
            <div class="flex items-center">
              <!-- Quantity Input -->
              <input
                type="number"
                min="1"
                v-model.number="item.quantity"
                @change="updateItemQuantity(item.id, item.quantity)"
                class="w-16 border p-1 text-center"
              />
              <button
                @click="removeFromCart(item.id)"
                class="bg-red-500 text-white px-3 py-1 ml-4 rounded"
              >
                Remove
              </button>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="text-xl font-bold">Total: ${{ totalPrice }}</h3>
          </div>

          <!-- Order Type Selection -->
          <div class="mt-6">
            <label class="block font-semibold mb-2" for="orderType">Order Type:</label>
            <select id="orderType" v-model="orderType" class="border p-2 rounded">
              <option value="dine-in">Dine-in</option>
              <option value="takeout">Takeout</option>
              <option value="delivery">Delivery</option>
            </select>
          </div>

          <button @click="checkout" class="bg-green-500 text-white px-4 py-2 mt-6 rounded">
            Proceed to Checkout
          </button>
        </div>
      </section>
    </UserLayout>
  </template>

  <script>
  import UserLayout from "../Layouts/UserLayout.vue";
  import { useCartStore } from "../Stores/CartStore";
  import { storeToRefs } from "pinia";
  import { ref } from "vue";
  import { Inertia } from "@inertiajs/inertia";

  export default {
    components: { UserLayout },
    setup() {
      const cartStore = useCartStore();
      // Use storeToRefs to maintain reactivity
      const { cartItems, totalPrice } = storeToRefs(cartStore);

      const orderType = ref("takeout"); // Default order type

      // Update quantity for a specific item
      const updateItemQuantity = (menuId, quantity) => {
        cartStore.updateQuantity(menuId, quantity);
      };

      // Remove an item from the cart
      const removeFromCart = (menuId) => {
        cartStore.removeFromCart(menuId);
      };

      // Checkout: send the cart data and order type to the backend
      const checkout = () => {
        Inertia.post(route("orders.store"), {
          cart: cartStore.cartItems,
          order_type: orderType.value,
        }, {
          onSuccess: () => {
            cartStore.clearCart();
            // Optionally, navigate to orders page:
            // Inertia.visit(route("orders.index"));
          },
          onError: (errors) => {
            console.log("Checkout errors:", errors);
          }
        });
      };

      return {
        cartItems,
        totalPrice,
        orderType,
        updateItemQuantity,
        removeFromCart,
        checkout,
      };
    },
  };
  </script>

  <style scoped>
  /* Add your styles as needed */
  </style>
