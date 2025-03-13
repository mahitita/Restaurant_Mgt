<template>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-2xl font-bold mb-4">Reserve Tables</h1>

      <div class="mb-6">
        <label for="reservationTime" class="block font-semibold mb-2">Select Date and Time:</label>
        <input
          type="datetime-local"
          v-model="reservationTime"
          @change="fetchAvailableTables"
          class="border p-2 rounded w-full"
          :min="minDateTime"
        />
      </div>

      <svg viewBox="0 0 1000 600" class="w-full h-auto border rounded-lg shadow-lg">
        <g v-for="table in tables" :key="table.id" @click="toggleTable(table)" style="cursor: pointer">
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
          <text
            :x="table.x_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.width / 2 : 0)"
            :y="table.y_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.height / 2 : 0)"
            text-anchor="middle"
            alignment-baseline="middle"
            fill="black"
            font-size="16"
            font-weight="bold"
          >
            {{ table.table_number }}
          </text>
          <circle
            v-for="chair in getChairs(table)"
            :key="chair.id"
            :cx="chair.x"
            :cy="chair.y"
            r="10"
            fill="#FFD700"
          />
        </g>
      </svg>

      <div v-if="selectedTables.length" class="mt-4 p-4 bg-green-100 border border-green-400 rounded-lg">
        <p class="text-lg">Selected Tables: {{ selectedTables.map(id => tables.find(t => t.id === id).table_number).join(', ') }}</p>
        <p>Total Seats: {{ totalSeats }}</p>
        <p>Deposit: ${{ selectedTables.length * 10 }} (refunded if you pay cash on-site)</p>
        <select v-model="paymentType" class="border p-2 rounded w-full mb-4">
          <option value="card">Card</option>
          <option value="bank_transfer">Bank Transfer</option>
        </select>
        <input v-model="accountNumber" placeholder="Account Number" class="border p-2 rounded w-full mb-4" />
        <button @click="reserveTables" class="bg-blue-500 text-white px-4 py-2 rounded" :disabled="isReserving">
          {{ isReserving ? 'Reserving...' : 'Reserve with Deposit' }}
        </button>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, onMounted, computed } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
  import { usePage } from '@inertiajs/vue3';
  import axios from 'axios';

  const { props } = usePage();
  const tables = ref(props.tables || []);
  const selectedTables = ref([]);
  const reservationTime = ref(props.selectedDateTime || new Date(Date.now() + 3600000).toISOString().slice(0, 16));
  const paymentType = ref('card');
  const accountNumber = ref('');
  const isReserving = ref(false);
  const minDateTime = new Date().toISOString().slice(0, 16);

  const totalSeats = computed(() =>
    selectedTables.value.reduce((sum, id) => sum + tables.value.find(t => t.id === id).seats, 0)
  );

  onMounted(() => {
    console.log("Initial Tables:", tables.value);
    fetchAvailableTables();
  });

  const fetchAvailableTables = async () => {
    const response = await axios.get(route('tables.available'), {
      params: { date_time: reservationTime.value },
    });
    tables.value = response.data;
    selectedTables.value = selectedTables.value.filter(id =>
      tables.value.find(t => t.id === id)?.available
    );
  };

  const toggleTable = (table) => {
    if (!table.available) {
      alert('This table is not available on the selected date!');
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
      alert("Please enter your account number.");
      return;
    }
    isReserving.value = true;
    Inertia.post(route('tables.store'), {
      table_ids: selectedTables.value,
      reservation_time: reservationTime.value,
      payment: { paymentType: paymentType.value, accountNumber: accountNumber.value },
    }, {
      onSuccess: () => {
        selectedTables.value = [];
        fetchAvailableTables();
      },
      onError: (errors) => alert("Reservation failed: " + JSON.stringify(errors)),
      onFinish: () => isReserving.value = false,
    });
  };

  const getTableFill = (table) => {
    if (selectedTables.value.includes(table.id)) return 'green';
    return table.available ? '#90EE90' : '#D3D3D3';
  };

  const getChairs = (table) => {
    const chairs = [];
    const chairSpacing = 30;

    if (table.type === 'rectangle' || table.type === 'square') {
      for (let i = 0; i < table.seats / 2; i++) {
        chairs.push({
          id: `chair-${table.id}-left-${i}`,
          x: table.x_coordinate - chairSpacing,
          y: table.y_coordinate + (i + 1) * (table.height / (table.seats / 2)),
        });
        chairs.push({
          id: `chair-${table.id}-right-${i}`,
          x: table.x_coordinate + table.width + chairSpacing,
          y: table.y_coordinate + (i + 1) * (table.height / (table.seats / 2)),
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
        });
      }
    }
    return chairs;
  };
  </script>

  <style scoped>
  svg { max-width: 1000px; max-height: 600px; }
  rect, circle, ellipse { transition: fill 0.3s ease; }
  rect:hover, circle:hover, ellipse:hover { fill: #87CEFA; }
  </style>
