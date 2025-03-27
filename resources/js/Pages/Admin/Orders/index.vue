<template>

    <div class="container mx-auto py-8 px-4">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Orders</h1>
        </div>

        <!-- Success/Error Messages -->
        <transition name="fade">
          <div
            v-if="$page.props.flash?.success"
            class="p-4 bg-green-100 text-green-800 rounded-lg mb-6 flex justify-between items-center"
          >
            <span>{{ $page.props.flash.success }}</span>
            <button @click="$page.props.flash.success = null" class="text-green-800 hover:text-green-600">
              ✕
            </button>
          </div>
        </transition>
        <transition name="fade">
          <div
            v-if="$page.props.flash?.error"
            class="p-4 bg-red-100 text-red-800 rounded-lg mb-6 flex justify-between items-center"
          >
            <span>{{ $page.props.flash.error }}</span>
            <button @click="$page.props.flash.error = null" class="text-red-800 hover:text-red-600">
              ✕
            </button>
          </div>
        </transition>

        <!-- Filters -->
        <div class="mb-6">
          <label class="block text-gray-700 font-semibold mb-2">Filter by Status</label>
          <div class="flex space-x-4">
            <label v-for="status in statuses" :key="status" class="flex items-center">
              <input
                type="checkbox"
                :value="status"
                v-model="selectedStatuses"
                @change="applyFilters"
                class="mr-2"
              />
              <span class="capitalize">{{ status }}</span>
            </label>
          </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-50 text-gray-700">
                <th class="p-4 text-left font-semibold">#</th>
                <th class="p-4 text-left font-semibold">Customer</th>
                <th class="p-4 text-left font-semibold">Order Type</th>
                <th class="p-4 text-left font-semibold">Details</th>
                <th class="p-4 text-left font-semibold">Items</th>
                <th class="p-4 text-left font-semibold">Total Price</th>
                <th class="p-4 text-left font-semibold">Status</th>
                <th class="p-4 text-left font-semibold">Priority</th>
                <th class="p-4 text-left font-semibold">Wait Time (min)</th>
                <th class="p-4 text-left font-semibold">Ordered At</th>
                <th class="p-4 text-left font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(order, index) in orders.data"
                :key="order.id"
                class="border-b hover:bg-gray-50 transition"
                :class="{ 'bg-yellow-100': order.is_priority }"
              >
                <td class="p-4 text-gray-600">{{ index + 1 + (orders.current_page - 1) * orders.per_page }}</td>
                <td class="p-4 text-gray-800">{{ order.user_name }}</td>
                <td class="p-4 text-gray-800 capitalize">{{ order.order_type }}</td>
                <td class="p-4 text-gray-600">
                  <span v-if="order.order_type === 'dine-in'">Table: {{ order.table_id || 'N/A' }}</span>
                  <span v-else-if="order.order_type === 'takeout'">Pickup: {{ order.pickup_time || 'N/A' }}</span>
                  <span v-else-if="order.order_type === 'delivery'">Address: {{ order.delivery_address || 'N/A' }}</span>
                </td>
                <td class="p-4 text-gray-800">
                  <ul>
                    <li v-for="item in order.items" :key="item.name">
                      {{ item.name }} (x{{ item.quantity }}) - £{{ item.price }}
                    </li>
                  </ul>
                </td>
                <td class="p-4 text-gray-800">£{{ order.total_price }}</td>
                <td class="p-4">
                  <select
                    v-model="order.status"
                    @change="updateStatus(order)"
                    class="border p-2 rounded-lg"
                    :disabled="updatingStatus[order.id]"
                  >
                    <option value="pending">Pending</option>
                    <option value="received">Received</option>
                    <option value="preparing">Preparing</option>
                    <option value="ready">Ready</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </td>
                <td class="p-4">
                  <button
                    @click="togglePriority(order)"
                    class="px-4 py-2 rounded-lg"
                    :class="order.is_priority ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'"
                    :disabled="togglingPriority[order.id]"
                  >
                    {{ order.is_priority ? 'High' : 'Normal' }}
                  </button>
                </td>
                <td class="p-4 text-gray-800">
                  <input
                    type="number"
                    v-model.number="order.estimated_wait_minutes"
                    @change="updateStatus(order)"
                    class="border p-2 rounded-lg w-20"
                    :disabled="updatingStatus[order.id]"
                  />
                </td>
                <td class="p-4 text-gray-600">{{ order.ordered_at }}</td>
                <td class="p-4">
                  <!-- Add more actions if needed -->
                </td>
              </tr>
              <tr v-if="!orders.data || orders.data.length === 0">
                <td colspan="11" class="p-4 text-center text-gray-500">No orders found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-between items-center">
          <span class="text-gray-600">
            Showing {{ orders.from || 0 }} to {{ orders.to || 0 }} of {{ orders.total || 0 }} orders
          </span>
          <div class="flex space-x-2">
            <button
              v-for="link in orders.links"
              :key="link.label"
              @click="router.get(link.url)"
              v-html="link.label"
              :class="[
                'px-4 py-2 rounded-lg',
                link.active ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700',
                !link.url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-300',
              ]"
              :disabled="!link.url"
            />
          </div>
        </div>
      </div>
    </div>

</template>

<script setup>
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  orders: Object,
  filters: Object,
});

defineOptions({
  layout: AdminLayout,
});

const statuses = ['pending', 'received', 'preparing', 'ready', 'completed', 'cancelled'];
const selectedStatuses = ref(props.filters.status || ['pending', 'received', 'preparing']);
const updatingStatus = ref({});
const togglingPriority = ref({});

const applyFilters = () => {
  router.get(
    route('admin.orders.index'),
    { status: selectedStatuses.value },
    { preserveState: true, preserveScroll: true }
  );
};

const updateStatus = (order) => {
  updatingStatus.value[order.id] = true;
  router.put(
    route('admin.orders.updateStatus', order.id),
    {
      status: order.status,
      estimated_wait_minutes: order.estimated_wait_minutes,
    },
    {
      onSuccess: () => {
        updatingStatus.value[order.id] = false;
      },
      onError: () => {
        updatingStatus.value[order.id] = false;
      },
    }
  );
};

const togglePriority = (order) => {
  togglingPriority.value[order.id] = true;
  router.post(
    route('admin.orders.togglePriority', order.id),
    {},
    {
      onSuccess: () => {
        togglingPriority.value[order.id] = false;
      },
      onError: () => {
        togglingPriority.value[order.id] = false;
      },
    }
  );
};

// Watch for changes in flash messages to clear them after a timeout
watch(
  () => usePage().props.flash,
  (newFlash) => {
    if (newFlash?.success || newFlash?.error) {
      setTimeout(() => {
        usePage().props.flash.success = null;
        usePage().props.flash.error = null;
      }, 3000);
    }
  },
  { deep: true }
);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

tr:hover {
  background-color: #f9fafb;
}
</style>