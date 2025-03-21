<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Pre-Order for Your Reservation</h1>
  
        <!-- Reservation Info -->
        <div class="mb-6 p-4 bg-gray-100 rounded-lg shadow-md">
          <p class="text-lg font-semibold text-gray-800">
            Table(s): {{ reservations.map(r => r.table_number).join(', ') }}
          </p>
          <p class="text-gray-600">Reservation Time: {{ reservations[0].reservation_time }}</p>
        </div>
  
        <!-- Menu Items -->
        <div v-if="menuItems && menuItems.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="item in menuItems" :key="item.id" class="bg-white p-4 rounded-lg shadow-md">
            <img :src="item.image" :alt="item.name" class="w-full h-40 object-cover rounded-t-lg" />
            <h3 class="text-lg font-semibold text-gray-800 mt-2">{{ item.name }}</h3>
            <p class="text-gray-600">${{ item.price }}</p>
            <div class="flex items-center mt-2">
              <button
                @click="decreaseQuantity(item.id)"
                class="bg-gray-300 px-3 py-1 rounded-l hover:bg-gray-400"
                :disabled="cart[item.id] === 0"
              >
                -
              </button>
              <span class="border-t border-b px-4 py-1 text-center w-12">{{ cart[item.id] }}</span>
              <button
                @click="addToCart(item)"
                class="bg-orange-600 text-white px-3 py-1 rounded-r hover:bg-orange-700"
              >
                +
              </button>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-600">No menu items available.</div>
  
        <!-- Cart Summary -->
        <div class="mt-6 p-6 bg-green-100 border border-green-400 rounded-lg shadow-md">
          <h2 class="text-xl font-bold text-gray-800">Your Pre-Order</h2>
          <div v-if="cartItems.length === 0" class="text-gray-600 mt-2">
            No items added yet.
          </div>
          <ul v-else class="list-disc ml-6 mt-2">
            <li v-for="item in cartItems" :key="item.id" class="text-gray-700">
              {{ item.name }} - {{ item.quantity }} x ${{ item.price }} = ${{ (item.quantity * item.price).toFixed(2) }}
              <button
                @click="removeFromCart(item.id)"
                class="ml-2 text-red-500 hover:text-red-700"
              >
                Remove
              </button>
            </li>
          </ul>
          <p class="text-lg font-semibold mt-4 text-gray-800">Total: ${{ totalPrice.toFixed(2) }}</p>
          <button
            @click="openPaymentModal"
            class="mt-4 bg-orange-600 text-white px-6 py-2 rounded-full hover:bg-orange-700"
            :disabled="cartItems.length === 0"
          >
            Place Pre-Order
          </button>
        </div>
  
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
              <button
                @click="processPayment"
                class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700"
              >
                Pay Now
              </button>
              <button
                @click="showPaymentModal = false"
                class="ml-4 text-gray-600 hover:text-gray-800"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </UserLayout>
  </template>
  
  <script setup>
  import UserLayout from '../../Layouts/UserLayout.vue';
  import { ref, computed, onMounted } from 'vue';
  import { router } from '@inertiajs/vue3';
  import { useToast } from 'vue-toastification';
  
  const props = defineProps({
    reservations: {
      type: Array,
      required: true,
    },
    menuItems: {
      type: Array,
      required: true,
      default: () => [],
    },
  });
  
  onMounted(() => {
    console.log('Props received:', {
      reservations: props.reservations,
      menuItems: props.menuItems,
    });
    if (props.menuItems && props.menuItems.length) {
      props.menuItems.forEach(item => {
        cart.value[item.id] = 0;
      });
      console.log('Cart initialized:', cart.value);
    } else {
      console.warn('No menu items to initialize cart.');
    }
  });
  
  const cart = ref({});
  const toast = useToast();
  
  const cartItems = computed(() => {
    if (!props.menuItems || !props.menuItems.length) return [];
    return Object.entries(cart.value)
      .filter(([_, qty]) => qty > 0)
      .map(([id, quantity]) => {
        const item = props.menuItems.find(m => m.id === Number(id));
        return item ? { ...item, quantity } : null;
      })
      .filter(Boolean);
  });
  
  const totalPrice = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
  });
  
  const addToCart = (item) => {
    console.log('Add button clicked for:', item.name);
    cart.value[item.id] += 1;
    toast.success(`${item.name} added to your pre-order!`);
    console.log('Updated cart:', cart.value);
  };
  
  const decreaseQuantity = (id) => {
    if (cart.value[id] > 0) {
      cart.value[id] -= 1;
      toast.info(`Removed one item from your pre-order.`);
    }
  };
  
  const removeFromCart = (id) => {
    cart.value[id] = 0;
    toast.info(`Item removed from your pre-order.`);
  };
  
  const showPaymentModal = ref(false);
  const payment = ref({
    paymentType: 'card',
    accountNumber: '',
  });
  
  const openPaymentModal = () => {
    if (cartItems.value.length === 0) {
      toast.error('Please add items to your pre-order first.');
      return;
    }
    showPaymentModal.value = true;
  };
  
  const processPayment = () => {
    if (payment.value.paymentType !== 'cash' && !payment.value.accountNumber) {
      toast.error('Please enter your account number.');
      return;
    }
  
    const cartData = cartItems.value.map(item => ({
      id: item.id,
      quantity: item.quantity,
      price: item.price,
    }));
  
    router.post(route('orders.storePreorder'), {
      reservation_ids: props.reservations.map(r => r.id),
      cart: cartData,
      payment: payment.value,
    }, {
      onSuccess: () => {
        cart.value = {};
        if (props.menuItems) {
          props.menuItems.forEach(item => (cart.value[item.id] = 0));
        }
        showPaymentModal.value = false;
        toast.success('Pre-order placed successfully!');
      },
      onError: (errors) => {
        console.error('Pre-order failed:', errors);
        toast.error('Failed to place pre-order: ' + JSON.stringify(errors));
      },
    });
  };
  </script>