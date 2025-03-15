<template>
    <AdminLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">Restaurant Dashboard</h2>
        <div class="mb-6">
          <label class="block font-semibold mb-2">Select Date:</label>
          <input
            type="date"
            v-model="selectedDate"
            @change="fetchDashboard"
            class="border p-2 rounded w-full max-w-xs"
          />
        </div>

        <!-- Suggestions -->
        <div v-if="suggestions.length" class="bg-yellow-100 p-4 rounded-lg mb-6">
          <h3 class="text-xl font-bold mb-2">Suggestions</h3>
          <ul class="list-disc ml-6">
            <li v-for="suggestion in suggestions" :key="suggestion">{{ suggestion }}</li>
          </ul>
        </div>

        <!-- Revenue -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
          <h3 class="text-xl font-bold mb-4">Revenue & Profit</h3>
          <p><strong>Total Revenue:</strong> ${{ revenue.total }}</p>
          <p><strong>Profit:</strong> ${{ revenue.profit }}</p>
          <p><strong>Hourly Breakdown:</strong></p>
          <ul class="list-disc ml-6">
            <li v-for="(rev, hour) in revenue.hourly" :key="hour">{{ hour }}:00 - ${{ rev }}</li>
          </ul>
          <p v-if="revenue.slow_hours.length" class="mt-4 text-red-500">
            <strong>Slow Hours:</strong> {{ revenue.slow_hours.join(', ') }}:00
          </p>
          <p><strong>Upsell Suggestions:</strong></p>
          <ul class="list-disc ml-6">
            <li v-for="item in revenue.upsell_suggestions" :key="item.name">{{ item.name }} - ${{ item.price }}</li>
          </ul>
        </div>

        <!-- Operations -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
          <h3 class="text-xl font-bold mb-4">Operations</h3>
          <p><strong>Active Orders:</strong> {{ operations.active_orders }}</p>
          <p><strong>Avg Table Turnover:</strong> {{ operations.table_turnover }} min</p>
          <p><strong>Staff Scheduled:</strong> {{ operations.staff_scheduled }}</p>
          <p><strong>Suggested Staff:</strong> {{ operations.suggested_staff }}</p>
          <p><strong>Kitchen Load:</strong> {{ operations.kitchen_load }} min/order</p>
        </div>

        <!-- Inventory -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
          <h3 class="text-xl font-bold mb-4">Inventory</h3>
          <p><strong>Low Stock:</strong></p>
          <ul class="list-disc ml-6">
            <li v-for="item in inventory.low_stock" :key="item.id">
              {{ item.name }} - {{ item.stock }} left
              <form @submit.prevent="updateStock(item)" class="inline">
                <input v-model="item.newStock" type="number" min="0" class="border p-1 w-16 mx-2" />
                <button type="submit" class="text-blue-500">Update</button>
              </form>
            </li>
          </ul>
          <p><strong>Daily Usage:</strong></p>
          <ul class="list-disc ml-6">
            <li v-for="(qty, id) in inventory.daily_usage" :key="id">
              {{ menuItems.find(m => m.id === parseInt(id))?.name }} - {{ qty }}
            </li>
          </ul>
          <p><strong>Waste:</strong> {{ inventory.waste }} units</p>
          <form @submit.prevent="logWaste" class="mt-4">
            <select v-model="waste.menu_id" class="border p-2 rounded mr-2">
              <option v-for="item in menuItems" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select>
            <input v-model="waste.quantity_used" type="number" min="0" placeholder="Used" class="border p-2 w-20 mr-2" />
            <input v-model="waste.quantity_wasted" type="number" min="0" placeholder="Wasted" class="border p-2 w-20 mr-2" />
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Log Waste</button>
          </form>
        </div>

        <!-- Customers -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-xl font-bold mb-4">Customers</h3>
          <p><strong>Top Items:</strong></p>
          <ul class="list-disc ml-6">
            <li v-for="item in customers.top_items" :key="item.name">
              {{ item.name }} - {{ item.quantity }} ordered
            </li>
          </ul>
          <p><strong>Repeat Customers:</strong> {{ customers.repeat_customers }}</p>
          <p><strong>Average Rating:</strong> {{ customers.average_rating }}/5</p>
        </div>
      </section>
    </AdminLayout>
  </template>

  <script>
  import { ref } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
  import AdminLayout from '../../Layouts/AdminLayout.vue';

  export default {
    components: { AdminLayout },
    props: {
      date: String,
      revenue: Object,
      operations: Object,
      inventory: Object,
      customers: Object,
      suggestions: Array,
    },
    setup(props) {
      const selectedDate = ref(props.date);
      const inventory = ref(props.inventory);
      const menuItems = ref([]); // Populate via API or props if needed
      const waste = ref({ menu_id: '', quantity_used: 0, quantity_wasted: 0 });

      const fetchDashboard = () => {
        Inertia.get(route('admin.dashboard'), { date: selectedDate.value }, {
          preserveState: true,
          onSuccess: (page) => {
            Object.assign(props, page.props);
          },
        });
      };

      const updateStock = (item) => {
        Inertia.post(route('admin.dashboard.stock'), {
          menu_id: item.id,
          quantity: item.newStock,
        });
      };

      const logWaste = () => {
        Inertia.post(route('admin.dashboard.waste'), waste.value, {
          onSuccess: () => {
            waste.value = { menu_id: '', quantity_used: 0, quantity_wasted: 0 };
          },
        });
      };

      return { selectedDate, inventory, menuItems, waste, fetchDashboard, updateStock, logWaste };
    },
  };
  </script>
