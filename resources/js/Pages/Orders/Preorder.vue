<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <h1 class="text-3xl font-bold mb-6 text-gursha-primary tracking-tight">Pre-Order for Your Reservation</h1>

        <!-- Reservation Info -->
        <div class="mb-6 p-4 bg-gray-100 rounded-lg shadow-md">
          <p class="text-lg font-semibold text-gray-800">
            Table(s): {{ reservations.map(r => r.table_number).join(', ') }}
          </p>
          <p class="text-gray-600">Reservation Time: {{ formatDateTime(reservations[0].reservation_time) }}</p>
        </div>

        <!-- Menu Items -->
        <div v-if="menuItems && menuItems.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <div
            v-for="item in menuItems"
            :key="item.id"
            class="bg-white p-3 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300"
          >
            <img
              :src="item.image"
              :alt="item.name"
              class="w-full h-24 object-cover rounded-md mb-2"
              @error="handleImageError"
            />
            <h3 class="text-sm font-semibold text-gray-800 truncate">{{ item.name }}</h3>
            <p class="text-gray-600 text-xs">Br {{ item.price }}</p>
            <div class="flex items-center justify-between mt-2">
              <div class="flex items-center">
                <button
                  @click="decreaseQuantity(item.id)"
                  class="bg-gray-300 px-2 py-1 rounded-l hover:bg-gray-400 text-xs"
                  :disabled="cart[item.id] === 0"
                >
                  -
                </button>
                <span class="border-t border-b px-2 py-1 text-center w-8 text-xs">{{ cart[item.id] }}</span>
                <button
                  @click="addToCart(item)"
                  class="bg-gursha-primary text-white px-2 py-1 rounded-r hover:bg-gursha-accent text-xs"
                >
                  +
                </button>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-gray-600 text-lg bg-white p-6 rounded-lg shadow-md text-center">
          No menu items available.
        </div>

        <!-- Cart Summary -->
        <div class="mt-6 p-6 bg-green-50 border border-green-200 rounded-lg shadow-md">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Your Pre-Order</h2>
          <div v-if="cartItems.length === 0" class="text-gray-600">
            No items added yet.
          </div>
          <ul v-else class="space-y-2">
            <li v-for="item in cartItems" :key="item.id" class="flex justify-between items-center text-gray-700">
              <span class="text-sm">
                {{ item.name }} - {{ item.quantity }} x Br {{ item.price }} = Br {{ (item.quantity * item.price) }}
              </span>
              <button
                @click="removeFromCart(item.id)"
                class="text-red-500 hover:text-red-700 text-sm"
              >
                Remove
              </button>
            </li>
          </ul>
          <p class="text-lg font-semibold mt-4 text-gray-800">Total: Br {{ totalPrice }}</p>
          <button
            @click="openPaymentModal"
            class="mt-4 w-full bg-gursha-primary text-white px-6 py-2 rounded-full hover:bg-gursha-accent transition-colors duration-300"
            :disabled="cartItems.length === 0"
            :class="{ 'opacity-50 cursor-not-allowed': cartItems.length === 0 }"
          >
            Place Pre-Order
          </button>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Complete Payment</h2>
            <p class="text-gray-700 mb-4">Total: Br {{ totalPrice }}</p>
            <select
              v-model="payment.paymentType"
              class="w-full border p-2 rounded-md mb-4 focus:ring-2 focus:ring-gursha-primary"
            >
              <option value="card">Card</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="cash">Cash</option>
            </select>
            <input
              v-if="payment.paymentType !== 'cash'"
              v-model="payment.accountNumber"
              placeholder="Account Number"
              class="w-full border p-2 rounded-md mb-4 focus:ring-2 focus:ring-gursha-primary"
            />
            <div class="flex justify-end space-x-3">
              <button
                @click="showPaymentModal = false"
                class="text-gray-600 hover:text-gray-800 font-medium"
              >
                Cancel
              </button>
              <button
                @click="processPayment"
                class="bg-gursha-primary text-white px-4 py-2 rounded-md hover:bg-gursha-accent transition-colors duration-300"
              >
                Pay Now
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

  // Format date and time for display
  const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString('en-US', {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  // Handle image loading errors
  const handleImageError = (event) => {
    event.target.src = '/image/placeholder.jpg'; // Replace with a placeholder image path
    console.warn('Image failed to load, using placeholder:', event.target.alt);
  };

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