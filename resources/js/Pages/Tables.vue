<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <section class="text-center mb-8">
          <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 animate-fade-in">Reserve a Table at Gursha</h1>
          <p class="text-lg text-gray-600 mt-2">Book your spot for an unforgettable dining experience.</p>
        </section>
  
        <!-- Date/Time Picker -->
        <div class="mb-8 flex justify-center">
          <div class="bg-white p-4 rounded-lg shadow-md w-full max-w-sm">
            <label for="reservationTime" class="block text-sm font-semibold text-gray-700 mb-2">Date & Time</label>
            <input
              type="datetime-local"
              v-model="reservationTime"
              @change="fetchAvailableTables"
              class="w-full p-2 border rounded-md text-gray-700 focus:ring-orange-500 focus:border-orange-500 transition"
              :min="minDateTime"
            />
          </div>
        </div>
  
        <!-- Table Map -->
        <div class="flex justify-center mb-8">
          <svg viewBox="0 0 600 400" class="w-full max-w-2xl h-auto border rounded-lg shadow-xl bg-gray-50">
            <g v-for="table in tables" :key="table.id" @click="toggleTable(table)" class="cursor-pointer group">
              <!-- Table Shapes -->
              <rect
                v-if="table.type === 'rectangle'"
                :x="table.x_coordinate"
                :y="table.y_coordinate"
                :width="table.width"
                :height="table.height"
                :fill="getTableFill(table)"
                stroke="#333"
                rx="5"
                ry="5"
                class="transition-all duration-300 group-hover:fill-opacity-80"
              />
              <circle
                v-else-if="table.type === 'round'"
                :cx="table.x_coordinate"
                :cy="table.y_coordinate"
                :r="table.width / 2"
                :fill="getTableFill(table)"
                stroke="#333"
                class="transition-all duration-300 group-hover:fill-opacity-80"
              />
              <ellipse
                v-else-if="table.type === 'oval'"
                :cx="table.x_coordinate + table.width / 2"
                :cy="table.y_coordinate + table.height / 2"
                :rx="table.width / 2"
                :ry="table.height / 2"
                :fill="getTableFill(table)"
                stroke="#333"
                class="transition-all duration-300 group-hover:fill-opacity-80"
              />
              <rect
                v-else-if="table.type === 'square'"
                :x="table.x_coordinate"
                :y="table.y_coordinate"
                :width="table.width"
                :height="table.width"
                :fill="getTableFill(table)"
                stroke="#333"
                rx="5"
                ry="5"
                class="transition-all duration-300 group-hover:fill-opacity-80"
              />
              <!-- Table Label -->
              <text
                :x="table.x_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.width / 2 : 0)"
                :y="table.y_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.height / 2 : 0)"
                text-anchor="middle"
                alignment-baseline="middle"
                fill="#333"
                font-size="12"
                font-weight="600"
              >
                T{{ table.table_number }} ({{ table.seats }})
              </text>
              <!-- Chairs -->
              <circle
                v-for="chair in getChairs(table)"
                :key="chair.id"
                :cx="chair.x"
                :cy="chair.y"
                r="8"
                fill="#FFD700"
                stroke="#333"
                stroke-width="1"
                class="transition-all duration-300"
              />
            </g>
          </svg>
        </div>
  
        <!-- Legend -->
        <div class="flex justify-center gap-6 mb-8 text-sm text-gray-700">
          <div class="flex items-center">
            <div class="w-4 h-4 bg-green-300 mr-2 rounded"></div> Available
          </div>
          <div class="flex items-center">
            <div class="w-4 h-4 bg-gray-300 mr-2 rounded"></div> Unavailable
          </div>
          <div class="flex items-center">
            <div class="w-4 h-4 bg-green-500 mr-2 rounded"></div> Selected
          </div>
        </div>
  
        <!-- Waitlist Option -->
        <div class="mt-6 p-4 bg-orange-50 rounded-lg shadow-md max-w-md mx-auto">
          <p v-if="preferredTableId && !tables.find(t => t.id === preferredTableId)?.available" class="text-lg font-semibold text-orange-600">
            Table {{ tables.find(t => t.id === preferredTableId)?.table_number }} is unavailable
          </p>
          <p v-else-if="!tables.some(t => t.available)" class="text-lg font-semibold text-orange-600">
            No tables available
          </p>
          <p v-else class="text-gray-700">Join the waitlist for your preferred table!</p>
          <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Party Size</label>
              <input
                type="number"
                v-model="partySize"
                min="1"
                class="w-full p-2 border rounded-md focus:ring-orange-500 focus:border-orange-500"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Preferred Table</label>
              <select
                v-model="preferredTableId"
                class="w-full p-2 border rounded-md focus:ring-orange-500 focus:border-orange-500"
              >
                <option :value="null">Any</option>
                <option v-for="table in tables" :key="table.id" :value="table.id">
                  T{{ table.table_number }} ({{ table.seats }} seats)
                </option>
              </select>
            </div>
          </div>
          <button
            @click="joinWaitlist"
            class="mt-4 w-full bg-orange-600 text-white px-4 py-2 rounded-full hover:bg-orange-700 hover:shadow-lg transform hover:scale-105 transition-all duration-300"
          >
            Join Waitlist
          </button>
        </div>
  
        <!-- Selected Tables and Payment -->
        <div v-if="selectedTables.length" class="mt-6 p-4 bg-green-50 rounded-lg shadow-md max-w-md mx-auto">
          <p class="text-lg font-semibold text-gray-800">
            Selected: {{ selectedTables.map(id => `T${tables.find(t => t.id === id).table_number}`).join(', ') }}
          </p>
          <p class="text-gray-700">Seats: {{ totalSeats }}</p>
          <p class="text-gray-700">Deposit: ${{ selectedTables.length * 10 }}</p>
          <select v-model="paymentType" class="w-full p-2 border rounded-md mt-2 focus:ring-orange-500">
            <option value="card">Card</option>
            <option value="bank_transfer">Bank Transfer</option>
          </select>
          <input
            v-model="accountNumber"
            placeholder="Account Number"
            class="w-full p-2 border rounded-md mt-2 focus:ring-orange-500"
          />
          <button
            @click="reserveTables"
            class="mt-4 w-full bg-orange-600 text-white px-4 py-2 rounded-full hover:bg-orange-700 hover:shadow-lg transform hover:scale-105 transition-all duration-300"
            :disabled="isReserving || !accountNumber"
          >
            {{ isReserving ? 'Reserving...' : 'Reserve Now' }}
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
      preferredTableId.value = table.id;
      return;
    }
    const index = selectedTables.value.indexOf(table.id);
    if (index === -1) selectedTables.value.push(table.id);
    else selectedTables.value.splice(index, 1);
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
      onSuccess: () => router.visit(route('reservations.index')),
      onError: (errors) => alert('Failed to join waitlist: ' + JSON.stringify(errors)),
    });
  };
  
  const getTableFill = (table) => {
    if (selectedTables.value.includes(table.id)) return '#34D399'; // Green for selected
    return table.available ? '#90EE90' : '#D3D3D3'; // Light green or gray
  };
  
  const getChairs = (table) => {
    const chairs = [];
    const chairSpacing = 20;
  
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
  .animate-fade-in {
    animation: fadeIn 1s ease-in;
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  </style>