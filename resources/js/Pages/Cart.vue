<template>
    <UserLayout>
      <section class="container mx-auto py-12 px-4 md:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-10">
          <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Your Cart</h2>
          <Link href="/menu" class="text-orange-500 hover:text-orange-600 font-medium text-lg transition-colors">
            Back to Menu
          </Link>
        </div>

        <!-- Empty Cart State -->
        <div v-if="cartItems.length === 0" class="bg-white rounded-lg shadow-md p-8 text-center">
          <p class="text-gray-600 text-xl">Your cart is empty.</p>
          <Link href="/menu" class="mt-4 inline-block text-orange-600 hover:text-orange-700 font-semibold text-lg">
            Explore Our Menu
          </Link>
        </div>

        <!-- Cart Content -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Cart Items (Left/Top) -->
          <div class="lg:col-span-2 space-y-6">
            <div v-for="item in cartItems" :key="item.id" class="bg-white rounded-xl shadow-md p-4 flex items-center justify-between hover:shadow-lg transition-shadow">
              <div class="flex items-center space-x-4">
                <img :src="item.image" :alt="item.name" class="w-20 h-20 object-cover rounded-md" />
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ item.name }}</h3>
                  <p class="text-gray-700">Br {{ item.price}} x {{ item.quantity }}</p>
                </div>
              </div>
              <div class="flex items-center space-x-3">
                <input
                  type="number"
                  min="1"
                  v-model.number="item.quantity"
                  @change="updateItemQuantity(item.id, item.quantity)"
                  class="w-16 p-2 border rounded-md text-center focus:ring-2 focus:ring-orange-500"
                />
                <button @click="removeFromCart(item.id)" class="text-red-600 hover:text-red-700 font-medium">
                  Remove
                </button>
              </div>
            </div>
          </div>

          <!-- Order Summary & Actions (Right/Bottom) -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h3>

              <!-- Order Type -->
              <div class="mb-6">
                <label class="block text-gray-800 font-medium mb-2">Order Type</label>
                <select v-model="orderType" class="w-full border p-2 rounded-md focus:ring-2 focus:ring-orange-500">
                  <option value="dine-in">Dine-in</option>
                  <option value="takeout">Takeout</option>
                  <option value="delivery">Delivery</option>
                </select>
              </div>

              <!-- Dine-in Table Selection -->
              <div v-if="orderType === 'dine-in'" class="mb-6">
                <h4 class="text-lg font-medium text-gray-800 mb-3">Select a Table</h4>
                <div class="grid grid-cols-2 gap-3">
                  <button
                    v-for="table in availableTables"
                    :key="table.id"
                    @click="selectTable(table.id)"
                    :class="{
                      'bg-green-600 text-white': selectedTable === table.id,
                      'bg-gray-200 text-gray-600 cursor-not-allowed': !table.available,
                      'bg-orange-600 text-white hover:bg-orange-700': table.available && selectedTable !== table.id,
                    }"
                    class="p-3 rounded-md text-sm font-medium transition-colors"
                    :disabled="!table.available"
                  >
                    Table {{ table.table_number }} ({{ table.seats }})
                  </button>
                </div>
                <p v-if="!selectedTable" class="text-red-500 text-sm mt-2">Please select a table.</p>
              </div>

              <!-- Takeout Pickup Time -->
              <div v-if="orderType === 'takeout'" class="mb-6">
                <label class="block text-gray-800 font-medium mb-2">Pickup Time</label>
                <input type="datetime-local" v-model="pickupTime" class="w-full border p-2 rounded-md focus:ring-2 focus:ring-orange-500" />
              </div>

              <!-- Delivery Address -->
              <div v-if="orderType === 'delivery'" class="mb-6">
                <label class="block text-gray-800 font-medium mb-2">Delivery Address</label>
                <input type="text" v-model="deliveryAddress" class="w-full border p-2 rounded-md focus:ring-2 focus:ring-orange-500" placeholder="Enter your address" />
              </div>

              <!-- Total Price -->
              <div class="border-t pt-4 mb-6">
                <p class="text-xl font-semibold text-gray-900">Total: <span class="text-orange-600">Br {{ totalPrice }}</span></p>
              </div>

              <!-- Actions: Join Waitlist & Payment -->
              <div class="flex flex-col space-y-4">
                <!-- Show "Proceed to Checkout" for all order types -->
                <button
                  @click="openPaymentModal"
                  :disabled="orderType === 'dine-in' && !selectedTable"
                  :class="{
                    'bg-orange-600 text-white hover:bg-orange-700': !(orderType === 'dine-in' && !selectedTable),
                    'bg-gray-400 text-gray-200 cursor-not-allowed': orderType === 'dine-in' && !selectedTable,
                  }"
                  class="w-full px-6 py-2 rounded-md transition-colors"
                >
                  Proceed to Checkout
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
          <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-sm">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Complete Payment</h2>
            <p class="text-gray-700 mb-4">Total: <span class="font-semibold text-orange-600">Br {{ totalPrice }}</span></p>
            <select v-model="payment.paymentType" class="w-full border p-2 rounded-md mb-4 focus:ring-2 focus:ring-orange-500">
              <option value="card">Card</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="cash">Cash</option>
            </select>
            <input
              v-if="payment.paymentType !== 'cash'"
              v-model="payment.accountNumber"
              placeholder="Account Number"
              class="w-full border p-2 rounded-md mb-4 focus:ring-2 focus:ring-orange-500"
            />
            <div class="flex justify-end space-x-3">
              <button @click="showPaymentModal = false" class="text-gray-600 hover:text-gray-800 font-medium">Cancel</button>
              <button @click="processPayment" class="bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors">
                Pay Now
              </button>
            </div>
          </div>
        </div>
      </section>
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
      // No client-side redirect needed; server handles it
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