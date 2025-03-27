<template>
    <UserLayout>
      <section class="container mx-auto py-12 px-4">
        <h2 class="text-4xl font-bold mb-8 text-gray-800">Your Cart</h2>

        <div v-if="cartItems.length === 0" class="text-center text-gray-500 text-lg">
          Your cart is empty. <Link href="/menu" class="text-orange-600 hover:underline">Explore our menu!</Link>
        </div>

        <div v-else>
          <!-- Cart items -->
          <div v-for="item in cartItems" :key="item.id" class="flex items-center justify-between py-4 border-b">
            <div class="flex items-center">
              <img :src="item.image" :alt="item.name" class="w-24 h-24 object-cover rounded-lg" />
              <div class="ml-6">
                <h3 class="text-xl font-semibold text-gray-800">{{ item.name }}</h3>
                <p class="text-gray-600">${{ item.price }} x {{ item.quantity }}</p>
              </div>
            </div>
            <div class="flex items-center">
              <input
                type="number"
                min="1"
                v-model.number="item.quantity"
                @change="updateItemQuantity(item.id, item.quantity)"
                class="w-16 border p-2 text-center rounded"
              />
              <button @click="removeFromCart(item.id)" class="ml-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Remove
              </button>
            </div>
          </div>

          <div class="mt-8">
            <h3 class="text-2xl font-bold text-gray-800">Total: ${{ totalPrice.toFixed(2) }}</h3>
          </div>

          <!-- Order Type -->
          <div class="mt-8">
            <label class="block text-lg font-semibold mb-2 text-gray-800" for="orderType">Order Type:</label>
            <select id="orderType" v-model="orderType" class="border p-2 rounded w-full md:w-1/3">
              <option value="dine-in">Dine-in</option>
              <option value="takeout">Takeout</option>
              <option value="delivery">Delivery</option>
            </select>
          </div>

          <!-- Dine-in Table Selection -->
          <div v-if="orderType === 'dine-in'" class="mt-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Select a Table:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <button
                v-for="table in availableTables"
                :key="table.id"
                @click="selectTable(table.id)"
                :class="{
                  'bg-green-500 text-white': selectedTable === table.id,
                  'bg-gray-300 text-gray-700 cursor-not-allowed': !table.available,
                  'bg-orange-600 text-white hover:bg-orange-700': table.available && selectedTable !== table.id,
                }"
                class="p-4 rounded-lg shadow-md text-center"
                :disabled="!table.available"
              >
                <p class="text-lg font-semibold">Table {{ table.table_number }}</p>
                <p class="text-sm">Seats: {{ table.seats }}</p>
              </button>
            </div>
            <p v-if="orderType === 'dine-in' && !selectedTable" class="text-red-500 mt-2">Please select a table.</p>
          </div>

          <!-- Takeout Pickup Time -->
          <div v-if="orderType === 'takeout'" class="mt-6">
            <label class="block text-lg font-semibold mb-2 text-gray-800" for="pickupTime">Pickup Time:</label>
            <input type="datetime-local" v-model="pickupTime" class="border p-2 rounded w-full md:w-1/3" />
          </div>

          <!-- Delivery Address -->
          <div v-if="orderType === 'delivery'" class="mt-6">
            <label class="block text-lg font-semibold mb-2 text-gray-800" for="deliveryAddress">Delivery Address:</label>
            <input type="text" v-model="deliveryAddress" class="border p-2 rounded w-full md:w-1/2" placeholder="Enter your address" />
          </div>

          <button @click="openPaymentModal" class="mt-8 bg-orange-600 text-white px-6 py-3 rounded-full hover:bg-orange-700">
            Proceed to Checkout
          </button>
        </div>
      </section>

      <!-- Payment Modal -->
      <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
          <h2 class="text-2xl font-bold mb-4 text-gray-800">Payment</h2>
          <p class="text-lg mb-4">Total: ${{ totalPrice.toFixed(2) }}</p>
          <select v-model="payment.paymentType" class="border p-2 rounded w-full mb-4">
            <option value="card">Card</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="cash">Cash</option>
          </select>
          <input
            v-if="payment.paymentType !== 'cash'"
            v-model="payment.accountNumber"
            placeholder="Account Number"
            class="border p-2 rounded w-full mb-4"
          />
          <div class="flex justify-end">
            <button @click="processPayment" class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700">
              Pay Now
            </button>
            <button @click="showPaymentModal = false" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</button>
          </div>
        </div>
      </div>
    </UserLayout>
  </template>

  <script>
  import UserLayout from '../Layouts/UserLayout.vue';
  import { useCartStore } from '../Stores/CartStore';
  import { storeToRefs } from 'pinia';
  import { ref, onMounted } from 'vue';
  import { router, Link } from '@inertiajs/vue3'; // Updated import
  import axios from 'axios';

  export default {
    components: { UserLayout, Link },
    setup() {
      const cartStore = useCartStore();
      const { cartItems, totalPrice } = storeToRefs(cartStore);

      const orderType = ref('takeout');
      const selectedTable = ref(null);
      const pickupTime = ref('');
      const deliveryAddress = ref('');
      const availableTables = ref([]);
      const showPaymentModal = ref(false);
      const payment = ref({
        paymentType: 'card',
        accountNumber: '',
      });

      const fetchTables = async () => {
        try {
          const response = await axios.get('/tables/available', {
            params: { date_time: new Date().toISOString() },
          });
          availableTables.value = response.data;
        } catch (error) {
          console.error('Error fetching tables:', error);
        }
      };

      onMounted(fetchTables);

      const selectTable = (tableId) => {
        selectedTable.value = tableId;
      };

      const updateItemQuantity = (menuId, quantity) => {
        cartStore.updateQuantity(menuId, quantity);
      };

      const removeFromCart = (menuId) => {
        cartStore.removeFromCart(menuId);
      };

      const openPaymentModal = () => {
        if (orderType.value === 'dine-in' && !selectedTable.value) {
          alert('Please select a table.');
          return;
        }
        if (orderType.value === 'takeout' && !pickupTime.value) {
          alert('Please select a pickup time.');
          return;
        }
        if (orderType.value === 'delivery' && !deliveryAddress.value) {
          alert('Please enter a delivery address.');
          return;
        }
        showPaymentModal.value = true;
      };

      const processPayment = () => {
        if (payment.value.paymentType !== 'cash' && !payment.value.accountNumber) {
          alert('Please enter your account number.');
          return;
        }

        console.log('Sending order:', {
          cart: cartItems.value,
          order_type: orderType.value,
          table_id: selectedTable.value,
          pickup_time: pickupTime.value,
          delivery_address: deliveryAddress.value,
          payment: payment.value,
        });

        router.post(route('orders.store'), {
          cart: cartItems.value,
          order_type: orderType.value,
          table_id: selectedTable.value,
          pickup_time: pickupTime.value,
          delivery_address: deliveryAddress.value,
          payment: payment.value,
        }, {
          preserveState: false,
          onBefore: () => {
            console.log('Before sending request, cart:', cartItems.value);
          },
          onSuccess: () => {
            console.log('Order successful, clearing cart');
            cartStore.clearCart();
            showPaymentModal.value = false;
          },
          onError: (errors) => {
            console.error('Order failed:', errors);
            alert('Payment failed: ' + JSON.stringify(errors));
          },
          onFinish: () => {
            console.log('Request finished, current cart:', cartItems.value);
          },
        });
      };

      onMounted(() => {
        if (cartItems.value.length > 0) {
          console.log('Cart loaded with items:', cartItems.value);
        }
      });

      return {
        cartItems,
        totalPrice,
        orderType,
        selectedTable,
        pickupTime,
        deliveryAddress,
        availableTables,
        showPaymentModal,
        selectTable,
        updateItemQuantity,
        removeFromCart,
        openPaymentModal,
        processPayment,
        payment,
      };
    },
  };
  </script>