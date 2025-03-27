<template>
    <UserLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-4">Order Confirmation</h2>
        <div v-if="success" class="bg-green-100 text-green-700 p-4 rounded mb-6">
          {{ success }}
        </div>

        <div id="receipt" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto receipt-style">
          <div class="text-center mb-6">
            <img src="/images/gursha-logo.png" alt="Gursha Logo" class="w-24 mx-auto mb-2" />
            <h1 class="text-2xl font-bold">Gursha Restaurant</h1>
            <p class="text-sm text-gray-600">123 Flavor Street, Food City</p>
            <p class="text-sm text-gray-600">Phone: (123) 456-7890</p>
          </div>

          <div class="border-t border-b py-4">
            <h3 class="text-lg font-semibold mb-2">Order Details</h3>
            <p><strong>Order ID:</strong> {{ order.id }}</p>
            <p><strong>Type:</strong> {{ order.order_type }}</p>
            <p><strong>Total:</strong> ${{ order.total_price }}</p>
            <p><strong>Estimated Wait:</strong> {{ order.estimated_wait_minutes }} minutes</p>
            <p v-if="order.table_id"><strong>Table:</strong> {{ order.table_id }}</p>
            <p v-if="order.pickup_time"><strong>Pickup Time:</strong> {{ order.pickup_time }}</p>
            <p v-if="order.delivery_address"><strong>Delivery Address:</strong> {{ order.delivery_address }}</p>
            <h4 class="font-semibold mt-4">Items Ordered:</h4>
            <ul class="list-none text-sm">
              <li v-for="item in order.order_items" :key="item.id" class="flex justify-between py-1 border-b">
                <span>Menu ID {{ item.menu_id }} (x{{ item.quantity }})</span>
                <span>${{ item.price }}</span>
              </li>
            </ul>
          </div>

          <div class="border-b py-4">
            <h3 class="text-lg font-semibold mb-2">Payment Details</h3>
            <p><strong>Method:</strong> {{ payment.payment_method }}</p>
            <p><strong>Amount:</strong> ${{ payment.amount }}</p>
            <p><strong>Deposit Paid:</strong> ${{ payment.deposit_amount }}</p>
            <p><strong>Deposit Refunded:</strong> {{ payment.deposit_refunded ? 'Yes' : 'No' }}</p>
            <p><strong>Paid At:</strong> {{ payment.paid_at }}</p>
            <p><strong>Status:</strong> {{ payment.status }}</p>
          </div>

          <div v-if="reservations && reservations.length" class="py-4">
            <h3 class="text-lg font-semibold mb-2">Reservation Details</h3>
            <p><strong>Tables:</strong> {{ reservations.map(r => r.table_id).join(', ') }}</p>
            <p><strong>Time:</strong> {{ reservations[0].reservation_time }}</p>
          </div>

          <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Thank you for choosing Gursha!</p>
            <p class="text-sm text-gray-600">Date: {{ currentDate }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-center space-x-4">
          <button @click="downloadPDF" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
            Download Receipt (PDF)
          </button>
          <button @click="printReceipt" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Print Receipt
          </button>
          <button @click="$inertia.get(route('orders.index'))" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            View All Orders
          </button>
        </div>
      </section>
    </UserLayout>
  </template>

  <script setup>
  import UserLayout from '@/Layouts/UserLayout.vue';
  import { ref, onMounted } from 'vue';
  import html2pdf from 'html2pdf.js';

  const props = defineProps({
    order: { type: Object, required: true },
    payment: { type: Object, required: true },
    reservations: { type: Array, default: () => [] },
    success: { type: String, default: null },
  });

  const currentDate = ref(new Date().toLocaleString('en-US', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit',
  }));

  onMounted(() => {
    console.log('Component mounted, receipt element:', document.getElementById('receipt'));
  });

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
  </style>