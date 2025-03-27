<!-- resources/js/Pages/Orders/Track.vue -->
<template>
    <UserLayout>
      <section class="container mx-auto py-12 px-4">
        <h2 class="text-4xl font-bold mb-8 text-gray-800">Track Your Order</h2>

        <!-- Success Message -->
        <div v-if="$page.props.success" class="bg-green-100 text-green-700 p-4 rounded mb-6">
          {{ $page.props.success }}
        </div>

        <!-- Receipt Container -->
        <div id="receipt" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto receipt-style">
          <!-- Header with Logo and Restaurant Name -->
          <div class="text-center mb-6">
            <img src="/image/logo.jpg" alt="Gursha Logo" class="w-24 mx-auto mb-2" />
            <h1 class="text-2xl font-bold">Gursha Restaurant</h1>
            <p class="text-sm text-gray-600">123 Flavor Street, Food City</p>
            <p class="text-sm text-gray-600">Phone: (123) 456-7890</p>
          </div>

          <!-- Order Details -->
          <div class="border-t border-b py-4">
            <h3 class="text-lg font-semibold mb-2">Order #{{ order.id }}</h3>
            <p><strong>Type:</strong> {{ order.order_type }}</p>
            <!-- <p><strong>Status:</strong> {{ order.status }}</p> -->
            <p><strong>Total:</strong> Br {{ order.total_price }}</p>
            <!-- <p><strong>Estimated Wait:</strong> {{ order.estimated_wait_minutes ?? 'Calculating...' }} minutes</p> -->
            <p><strong>Ordered At:</strong> {{ order.created_at }}</p>
            <p v-if="order.order_type === 'dine-in'"><strong>Table:</strong> {{ order.table_id }}</p>
            <p v-if="order.order_type === 'takeout'"><strong>Pickup Time:</strong> {{ order.pickup_time }}</p>
            <p v-if="order.order_type === 'delivery'"><strong>Delivery Address:</strong> {{ order.delivery_address }}</p>

            <h4 class="font-semibold mt-4">Items Ordered:</h4>
            <ul class="list-none text-sm">
              <li v-for="item in order.order_items" :key="item.id" class="flex justify-between py-1 border-b">
                <span>{{ item.name }} (x{{ item.quantity }})</span>
                <span>${{ item.price }}</span>
              </li>
            </ul>
          </div>

          <!-- Payment Details -->
          <div class="border-b py-4">
            <h3 class="text-lg font-semibold mb-2">Payment Details</h3>
            <p><strong>Method:</strong> {{ payment.payment_method }}</p>
            <p><strong>Amount:</strong> Br {{ order.total_price }}</p>
            <p v-if="payment.deposit_amount > 0"><strong>Deposit Paid:</strong> ${{ payment.deposit_amount }}</p>
            <!-- <p><strong>Deposit Refunded:</strong> {{ payment.deposit_refunded ? 'Yes' : 'No' }}</p> -->
            <p v-if="payment.paid_at"><strong>Paid At:</strong> {{ payment.paid_at }}</p>
            <!-- <p><strong>Status:</strong> {{ payment.status }}</p> -->
          </div>

          <!-- Reservation Details -->
          <div v-if="reservations && reservations.length" class="py-4">
            <h3 class="text-lg font-semibold mb-2">Related Reservations</h3>
            <p><strong>Tables:</strong> {{ reservations.map(r => r.table_id).join(', ') }}</p>
            <p><strong>Time:</strong> {{ reservations[0].reservation_time }}</p>
          </div>

          <!-- Footer -->
          <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Thank you for choosing Gursha!</p>
            <p class="text-sm text-gray-600">Date: {{ currentDate }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-4">
          <button
            @click="downloadPDF"
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
          >
            Download Receipt (PDF)
          </button>
          <button
            @click="printReceipt"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          >
            Print Receipt
          </button>
          <Link
            href="/orders/my-orders"
            class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700"
          >
            Back to My Orders
          </Link>
        </div>
      </section>
    </UserLayout>
  </template>

  <script setup>
  import UserLayout from '../../Layouts/UserLayout.vue';
  import { Link } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import html2pdf from 'html2pdf.js';

  const props = defineProps({
    order: { type: Object, required: true },
    payment: { type: Object, required: true },
    reservations: { type: Array, default: () => [] },
    success: { type: String, default: null },
  });

  const currentDate = ref(new Date().toLocaleString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }));

  const downloadPDF = () => {
    const element = document.getElementById('receipt');
    if (!element) {
      console.error('Receipt element not found');
      return;
    }
    console.log('Downloading PDF for order:', props.order.id);
    html2pdf()
      .set({
        filename: `Gursha_Receipt_Order_${props.order.id}.pdf`,
        margin: 10,
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
      })
      .from(element)
      .save()
      .catch(err => console.error('PDF generation failed:', err));
  };

  const printReceipt = () => {
    const printContent = document.getElementById('receipt');
    if (!printContent) {
      console.error('Receipt element not found for printing');
      return;
    }
    console.log('Printing receipt for order:', props.order.id);
    const printWindow = window.open('', '_blank', 'width=600,height=400');
    if (!printWindow) {
      console.error('Failed to open print window. Check browser settings.');
      return;
    }
    printWindow.document.write(`
      <html>
        <head>
          <title>Gursha Receipt - Order ${props.order.id}</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            .receipt-style { width: 100%; max-width: 400px; margin: auto; }
            h1 { font-size: 24px; }
            h3 { font-size: 18px; }
            p, li { font-size: 14px; }
            .border-t, .border-b { border-top: 1px dashed #000; border-bottom: 1px dashed #000; }
          </style>
        </head>
        <body>${printContent.outerHTML}</body>
      </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    setTimeout(() => printWindow.close(), 1000); // Close after a delay
  };
  </script>

  <style scoped>
  .receipt-style {
    font-family: 'Courier New', Courier, monospace;
    border: 1px solid #ccc;
    background: #fff;
    color: #333;
  }

  .receipt-style h1, .receipt-style h3 {
    font-family: 'Arial', sans-serif;
  }

  .receipt-style ul li {
    font-size: 14px;
  }
  </style>