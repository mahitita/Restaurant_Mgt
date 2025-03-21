<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-8 text-gursha-primary">Reserve a Table at Gursha</h1>
  
        <!-- Date/Time Picker -->
        <div class="mb-8">
          <label for="reservationTime" class="block font-semibold mb-2 text-gray-800 text-lg">Select Date and Time:</label>
          <input
            type="datetime-local"
            v-model="reservationTime"
            @change="fetchAvailableTables"
            class="border p-3 rounded-lg w-full md:w-1/3 shadow-md focus:ring-gursha-primary focus:border-gursha-primary text-gray-700"
            :min="minDateTime"
          />
        </div>
  
        <!-- Table Map -->
        <svg viewBox="0 0 1000 600" class="w-full h-auto border rounded-lg shadow-xl bg-gray-50 mb-8">
          <g v-for="table in tables" :key="table.id" @click="toggleTable(table)" style="cursor: pointer">
            <!-- Table Shapes -->
            <rect
              v-if="table.type === 'rectangle'"
              :x="table.x_coordinate"
              :y="table.y_coordinate"
              :width="table.width"
              :height="table.height"
              :fill="getTableFill(table)"
              stroke="black"
              rx="10"
              ry="10"
            />
            <circle
              v-else-if="table.type === 'round'"
              :cx="table.x_coordinate"
              :cy="table.y_coordinate"
              :r="table.width / 2"
              :fill="getTableFill(table)"
              stroke="black"
            />
            <ellipse
              v-else-if="table.type === 'oval'"
              :cx="table.x_coordinate + table.width / 2"
              :cy="table.y_coordinate + table.height / 2"
              :rx="table.width / 2"
              :ry="table.height / 2"
              :fill="getTableFill(table)"
              stroke="black"
            />
            <rect
              v-else-if="table.type === 'square'"
              :x="table.x_coordinate"
              :y="table.y_coordinate"
              :width="table.width"
              :height="table.width"
              :fill="getTableFill(table)"
              stroke="black"
              rx="10"
              ry="10"
            />
            <!-- Table Label -->
            <text
              :x="table.x_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.width / 2 : 0)"
              :y="table.y_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.height / 2 : 0)"
              text-anchor="middle"
              alignment-baseline="middle"
              fill="black"
              font-size="16"
              font-weight="bold"
            >
              {{ table.table_number }} ({{ table.seats }} seats)
            </text>
            <!-- Chairs -->
            <circle
              v-for="chair in getChairs(table)"
              :key="chair.id"
              :cx="chair.x"
              :cy="chair.y"
              r="12"
              fill="#FFD700"
              stroke="black"
              stroke-width="1"
            />
            <text
              v-for="chair in getChairs(table)"
              :key="chair.id + '-label'"
              :x="chair.x"
              :y="chair.y + 4"
              text-anchor="middle"
              fill="black"
              font-size="10"
            >
              {{ chair.seatNumber }}
            </text>
          </g>
        </svg>
  
        <!-- Waitlist Option -->
        <div class="mt-6 p-6 bg-gursha-light border border-gursha-accent rounded-lg shadow-md">
          <p v-if="preferredTableId && !tables.find(t => t.id === preferredTableId)?.available" class="text-xl font-semibold text-gursha-secondary">
            Table {{ tables.find(t => t.id === preferredTableId)?.table_number }} is not available
          </p>
          <p v-else-if="!tables.some(t => t.available)" class="text-xl font-semibold text-gursha-secondary">
            No tables available for {{ reservationTime }}
          </p>
          <p v-else class="text-lg text-gray-700">Want a specific table? Join the waitlist!</p>
          <div class="mt-4">
            <label class="block text-gray-700 font-medium mb-2">Party Size:</label>
            <input
              type="number"
              v-model="partySize"
              min="1"
              class="border p-2 rounded w-24 shadow-sm focus:ring-gursha-primary focus:border-gursha-primary"
            />
          </div>
          <div class="mt-4">
            <label class="block text-gray-700 font-medium mb-2">Preferred Table (Optional):</label>
            <select
              v-model="preferredTableId"
              class="border p-2 rounded w-full md:w-1/3 shadow-sm focus:ring-gursha-primary focus:border-gursha-primary"
            >
              <option :value="null">Any Table</option>
              <option v-for="table in tables" :key="table.id" :value="table.id">
                {{ table.table_number }} ({{ table.seats }} seats)
              </option>
            </select>
          </div>
          <button
            @click="joinWaitlist"
            class="mt-6 bg-gursha-primary text-white px-6 py-3 rounded-full hover:bg-gursha-accent hover:shadow-lg transform hover:scale-105 transition-all duration-300"
          >
            Join Waitlist
          </button>
        </div>
  
        <!-- Selected Tables and Payment -->
        <div v-if="selectedTables.length" class="mt-6 p-6 bg-green-100 border border-green-400 rounded-lg shadow-md">
          <p class="text-xl font-semibold text-gray-800">
            Selected Tables: {{ selectedTables.map(id => tables.find(t => t.id === id).table_number).join(', ') }}
          </p>
          <p class="text-gray-700">Total Seats: {{ totalSeats }}</p>
          <p class="text-gray-700">Deposit: ${{ selectedTables.length * 10 }} (refunded if you pay cash on-site)</p>
          <select v-model="paymentType" class="border p-2 rounded w-full mt-4 shadow-sm focus:ring-gursha-primary">
            <option value="card">Card</option>
            <option value="bank_transfer">Bank Transfer</option>
          </select>
          <input
            v-model="accountNumber"
            placeholder="Account Number"
            class="border p-2 rounded w-full mt-4 shadow-sm focus:ring-gursha-primary"
          />
          <button
            @click="reserveTables"
            class="mt-4 bg-gursha-primary text-white px-6 py-3 rounded-full hover:bg-gursha-accent hover:shadow-lg transform hover:scale-105 transition-all duration-300"
            :disabled="isReserving || !accountNumber"
          >
            {{ isReserving ? 'Reserving...' : 'Reserve with Deposit' }}
          </button>
        </div>
      </div>
    </UserLayout>
  </template>
  
  <script setup>
  import UserLayout from '../Layouts/UserLayout.vue';
  import { ref, onMounted, computed } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';
  import axios from 'axios';
  
  const { props } = usePage();
  const tables = ref(props.tables || []);
  const selectedTables = ref([]);
  const reservationTime = ref(props.selectedDateTime || new Date(Date.now() + 3600000).toISOString().slice(0, 16));
  const paymentType = ref('card');
  const accountNumber = ref('');
  const isReserving = ref(false);
  const minDateTime = new Date().toISOString().slice(0, 16);
  const partySize = ref(1);
  const preferredTableId = ref(null);
  
  const totalSeats = computed(() =>
    selectedTables.value.reduce((sum, id) => sum + tables.value.find(t => t.id === id).seats, 0)
  );
  
  onMounted(() => {
    fetchAvailableTables();
  });
  
  const fetchAvailableTables = async () => {
    const response = await axios.get(route('tables.available'), {
      params: { date_time: reservationTime.value },
    });
    tables.value = response.data;
    selectedTables.value = selectedTables.value.filter(id => tables.value.find(t => t.id === id)?.available);
  };
  
  const toggleTable = (table) => {
    if (!table.available) {
      preferredTableId.value = table.id; // Set as preferred if unavailable
      return;
    }
    const index = selectedTables.value.indexOf(table.id);
    if (index === -1) {
      selectedTables.value.push(table.id);
    } else {
      selectedTables.value.splice(index, 1);
    }
  };
  
  const reserveTables = () => {
    if (!accountNumber.value) {
      alert('Please enter your account number.');
      return;
    }
    isReserving.value = true;
    router.post(route('tables.store'), {
      table_ids: selectedTables.value,
      reservation_time: reservationTime.value,
      payment: { paymentType: paymentType.value, accountNumber: accountNumber.value },
    }, {
      preserveState: false,
      onSuccess: () => {
        selectedTables.value = [];
        router.visit(route('reservations.index'));
      },
      onError: (errors) => alert('Reservation failed: ' + JSON.stringify(errors)),
      onFinish: () => (isReserving.value = false),
    });
  };
  
  const joinWaitlist = () => {
    if (partySize.value < 1) {
      alert('Party size must be at least 1.');
      return;
    }
    router.post(route('waitlists.store'), {
      party_size: partySize.value,
      reservation_time: reservationTime.value,
      preferred_table_id: preferredTableId.value,
    }, {
      onSuccess: () => {
        router.visit(route('reservations.index'));
      },
      onError: (errors) => alert('Failed to join waitlist: ' + JSON.stringify(errors)),
    });
  };
  
  const getTableFill = (table) => {
    if (selectedTables.value.includes(table.id)) return '#34D399'; // Green for selected
    return table.available ? '#90EE90' : '#D3D3D3'; // Light green or gray
  };
  
  const getChairs = (table) => {
    const chairs = [];
    const chairSpacing = 30;
  
    if (table.type === 'rectangle' || table.type === 'square') {
      const seatsPerSide = Math.floor(table.seats / 2);
      for (let i = 0; i < seatsPerSide; i++) {
        chairs.push({
          id: `chair-${table.id}-left-${i}`,
          x: table.x_coordinate - chairSpacing,
          y: table.y_coordinate + (i + 1) * (table.height / (seatsPerSide + 1)),
          seatNumber: i + 1,
        });
        chairs.push({
          id: `chair-${table.id}-right-${i}`,
          x: table.x_coordinate + table.width + chairSpacing,
          y: table.y_coordinate + (i + 1) * (table.height / (seatsPerSide + 1)),
          seatNumber: seatsPerSide + i + 1,
        });
      }
    } else if (table.type === 'round' || table.type === 'oval') {
      const radius = (table.width / 2) + chairSpacing;
      for (let i = 0; i < table.seats; i++) {
        const angle = (i * 360 / table.seats) * (Math.PI / 180);
        chairs.push({
          id: `chair-${table.id}-${i}`,
          x: table.x_coordinate + radius * Math.cos(angle),
          y: table.y_coordinate + radius * Math.sin(angle),
          seatNumber: i + 1,
        });
      }
    }
    return chairs;
  };
  </script>
  
  <style scoped>
  svg {
    max-width: 1000px;
    max-height: 600px;
  }
  rect, circle, ellipse {
    transition: fill 0.3s ease;
  }
  rect:hover, circle:hover, ellipse:hover {
    fill: #87CEFA;
  }
  </style>