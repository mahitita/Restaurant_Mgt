<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-4">Your Cart</h2>

        <div v-if="cartItems.length === 0" class="text-center text-gray-500">
          Your cart is empty.
        </div>

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
              <input
                type="number"
                min="1"
                v-model.number="item.quantity"
                @change="updateItemQuantity(item.id, item.quantity)"
                class="w-16 border p-1 text-center"
              />
              <button @click="removeFromCart(item.id)" class="bg-red-500 text-white px-3 py-1 ml-4 rounded">
                Remove
              </button>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="text-xl font-bold">Total: ${{ totalPrice }}</h3>
          </div>

          <!-- Order Type -->
          <div class="mt-6">
            <label class="block font-semibold mb-2" for="orderType">Order Type:</label>
            <select id="orderType" v-model="orderType" class="border p-2 rounded">
              <option value="dine-in">Dine-in</option>
              <option value="takeout">Takeout</option>
              <option value="delivery">Delivery</option>
            </select>
          </div>

          <!-- Dine-in Table Selection -->
          <div v-if="orderType === 'dine-in'" class="mt-6">
            <h3 class="font-semibold mb-2">Select a Table:</h3>
            <div class="grid grid-cols-3 gap-4">
              <button
                v-for="table in availableTables"
                :key="table.id"
                @click="selectTable(table.id)"
                :class="{
                  'bg-green-500 text-white': selectedTable === table.id,
                  'bg-gray-300 text-gray-700': table.status !== 'available',
                  'bg-blue-500 text-white': table.status === 'available' && selectedTable !== table.id
                }"
                class="p-4 rounded-md shadow-md text-center"
                :disabled="table.status !== 'available'"
              >
                <p class="text-lg font-semibold">Table {{ table.table_number }}</p>
                <p class="text-sm">Seats: {{ table.seats }}</p>
              </button>
            </div>
            <p v-if="orderType === 'dine-in' && !selectedTable" class="text-red-500 mt-2">Please select a table.</p>
          </div>

          <!-- Takeout Pickup Time -->
          <div v-if="orderType === 'takeout'" class="mt-4">
            <label class="block font-semibold mb-2" for="pickupTime">Pickup Time:</label>
            <input type="datetime-local" v-model="pickupTime" class="border p-2 rounded w-full" />
          </div>

          <!-- Delivery Address -->
          <div v-if="orderType === 'delivery'" class="mt-4">
            <label class="block font-semibold mb-2" for="deliveryAddress">Delivery Address:</label>
            <input type="text" v-model="deliveryAddress" class="border p-2 rounded w-full" placeholder="Enter your address" />
          </div>

          <button @click="openPaymentModal" class="bg-green-500 text-white px-4 py-2 mt-6 rounded">
            Proceed to Checkout
          </button>
        </div>
      </section>

      <!-- Payment Modal -->
      <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">

    <div class="modal-content">
      <h2>Payment</h2>
      <p>Total: ${{ totalPrice }}</p>
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
      <button @click="processPayment" class="bg-blue-500 text-white px-4 py-2 rounded">
        Pay ${{ totalPrice }}
      </button>
      <button @click="showPaymentModal = false" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">
        Cancel
      </button>
    </div>
    </div>
  </div>
    </UserLayout>
  </template>



<script>
import UserLayout from "../Layouts/UserLayout.vue";
import { useCartStore } from "../Stores/CartStore";
import { storeToRefs } from "pinia";
import { ref, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import axios from "axios";

export default {
  components: { UserLayout },
  setup() {
    const cartStore = useCartStore();
    const { cartItems, totalPrice } = storeToRefs(cartStore);

    const orderType = ref("takeout");
    const selectedTable = ref(null);
    const pickupTime = ref("");
    const deliveryAddress = ref("");
    const availableTables = ref([]);
    const showPaymentModal = ref(false);
    const payment = ref({
      paymentType: "card",
      accountNumber: "",
    });

    const fetchTables = async () => {
      try {
        const response = await axios.get("/api/tables");
        availableTables.value = response.data;
      } catch (error) {
        console.error("Error fetching tables:", error);
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
      if (orderType.value === "dine-in" && !selectedTable.value) {
        alert("Please select a table.");
        return;
      }
      showPaymentModal.value = true;
    };
    const paymentType = ref({ paymentType: 'card', accountNumber: '' });

const processPayment = () => {
  if (paymentType.value.paymentType !== 'cash' && !payment.value.accountNumber) {
    alert("Please enter your account number.");
    return;
  }

  Inertia.post(route("orders.store"), {
    cart: cartStore.cartItems,
    order_type: orderType.value,
    table_id: selectedTable.value,
    pickup_time: pickupTime.value,
    delivery_address: deliveryAddress.value,
    payment: payment.value,
  }, {
    onSuccess: () => {
      cartStore.clearCart();
      showPaymentModal.value = false;
    },
    onError: (errors) => alert("Payment failed: " + JSON.stringify(errors)),
  });
};

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
