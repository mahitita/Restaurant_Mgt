<template>
    <UserLayout>
      <div class="container mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <!-- Header -->
        <section class="text-center mb-10">
          <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">Reserve Your Table</h1>
          <p class="text-lg text-gray-600 mt-3">Secure your spot at Gursha for a delightful dining experience.</p>
        </section>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
          <!-- Left: Date/Time Picker & Waitlist -->
          <div class="lg:col-span-1 space-y-6">
            <!-- Date/Time Picker -->
            <div class="bg-white p-6 rounded-xl shadow-md">
              <label for="reservationTime" class="block text-sm font-medium text-gray-700 mb-2">Pick Date & Time</label>
              <input
                type="datetime-local"
                v-model="reservationTime"
                @change="fetchAvailableTables"
                class="w-full p-3 border rounded-lg text-gray-700 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                :min="minDateTime"
              />
            </div>

            <!-- Join Waitlist -->
            <div class="bg-orange-50 p-6 rounded-xl shadow-md">
              <h3 class="text-lg font-semibold text-orange-700 mb-3">Join the Waitlist</h3>
              <p v-if="preferredTableId && !tables.find(t => t.id === preferredTableId)?.available" class="text-sm text-orange-600 mb-2">
                Table {{ tables.find(t => t.id === preferredTableId)?.table_number }} is unavailable
              </p>
              <p v-else-if="!tables.some(t => t.available)" class="text-sm text-orange-600 mb-2">No tables available</p>
              <p v-else class="text-sm text-gray-600 mb-2">Reserve a spot if your table isn’t free.</p>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Party Size</label>
                  <input
                    type="number"
                    v-model="partySize"
                    min="1"
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Preferred Table</label>
                  <select
                    v-model="preferredTableId"
                    class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                  >
                    <option :value="null">Any</option>
                    <option v-for="table in tables" :key="table.id" :value="table.id">
                      T{{ table.table_number }} ({{ table.seats }} seats)
                    </option>
                  </select>
                </div>
                <button
                  @click="joinWaitlist"
                  class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 hover:shadow-md transition-all duration-200"
                >
                  Join Waitlist
                </button>
              </div>
            </div>
          </div>

          <!-- Center: Table Map & Legend -->
          <div class="lg:col-span-3">
            <!-- Table Map -->
            <div class="bg-white p-4 rounded-xl shadow-md">
              <svg viewBox="0 0 600 400" class="w-full h-auto border rounded-lg bg-gray-50">
                <g v-for="table in tables" :key="table.id" @click="toggleTable(table)" class="cursor-pointer group">
                  <!-- Table Shapes -->
                  <rect
                    v-if="table.type === 'rectangle'"
                    :x="table.x_coordinate"
                    :y="table.y_coordinate"
                    :width="table.width"
                    :height="table.height"
                    :fill="getTableFill(table)"
                    stroke="#4B5563"
                    rx="6"
                    ry="6"
                    class="transition-all duration-200 group-hover:fill-opacity-90"
                  />
                  <circle
                    v-else-if="table.type === 'round'"
                    :cx="table.x_coordinate"
                    :cy="table.y_coordinate"
                    :r="table.width / 2"
                    :fill="getTableFill(table)"
                    stroke="#4B5563"
                    class="transition-all duration-200 group-hover:fill-opacity-90"
                  />
                  <ellipse
                    v-else-if="table.type === 'oval'"
                    :cx="table.x_coordinate + table.width / 2"
                    :cy="table.y_coordinate + table.height / 2"
                    :rx="table.width / 2"
                    :ry="table.height / 2"
                    :fill="getTableFill(table)"
                    stroke="#4B5563"
                    class="transition-all duration-200 group-hover:fill-opacity-90"
                  />
                  <rect
                    v-else-if="table.type === 'square'"
                    :x="table.x_coordinate"
                    :y="table.y_coordinate"
                    :width="table.width"
                    :height="table.width"
                    :fill="getTableFill(table)"
                    stroke="#4B5563"
                    rx="6"
                    ry="6"
                    class="transition-all duration-200 group-hover:fill-opacity-90"
                  />
                  <!-- Table Label -->
                  <text
                    :x="table.x_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.width / 2 : 0)"
                    :y="table.y_coordinate + (table.type === 'rectangle' || table.type === 'square' ? table.height / 2 : 0)"
                    text-anchor="middle"
                    alignment-baseline="middle"
                    fill="#1F2937"
                    font-size="14"
                    font-weight="500"
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
                    fill="#FBBF24"
                    stroke="#4B5563"
                    stroke-width="1"
                    class="transition-all duration-200"
                  />
                </g>
              </svg>
            </div>

            <!-- Legend & Reserve Button -->
            <div class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
              <div class="flex gap-6 text-sm text-gray-600">
                <div class="flex items-center">
                  <div class="w-4 h-4 bg-green-300 mr-2 rounded-full"></div> Available
                </div>
                <div class="flex items-center">
                  <div class="w-4 h-4 bg-gray-300 mr-2 rounded-full"></div> Unavailable
                </div>
                <div class="flex items-center">
                  <div class="w-4 h-4 bg-green-500 mr-2 rounded-full"></div> Selected
                </div>
              </div>
              <button
                v-if="selectedTables.length"
                @click="showPaymentModal = true"
                class="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 hover:shadow-md transition-all duration-200"
              >
                Reserve Selected ({{ selectedTables.length }})
              </button>
            </div>
          </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
          <div class="bg-white rounded-xl p-6 shadow-xl w-full max-w-md">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Complete Your Reservation</h3>
            <p class="text-sm text-gray-700 mb-2">
              Selected: {{ selectedTables.map(id => `T${tables.find(t => t.id === id).table_number}`).join(', ') }}
            </p>
            <p class="text-sm text-gray-700 mb-2">Seats: {{ totalSeats }}</p>
            <p class="text-sm text-gray-700 mb-4">Deposit: ${{ selectedTables.length * 10 }}</p>
            <select v-model="paymentType" class="w-full p-2 border rounded-lg mb-4 focus:ring-2 focus:ring-orange-500">
              <option value="card">Card</option>
              <option value="bank_transfer">Bank Transfer</option>
            </select>
            <input
              v-model="accountNumber"
              placeholder="Account Number"
              class="w-full p-2 border rounded-lg mb-4 focus:ring-2 focus:ring-orange-500"
            />
            <div class="flex justify-end space-x-3">
              <button @click="showPaymentModal = false" class="text-gray-600 hover:text-gray-800 font-medium">Cancel</button>
              <button
                @click="reserveTables"
                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-all duration-200"
                :disabled="isReserving || !accountNumber"
              >
                {{ isReserving ? 'Reserving...' : 'Reserve Now' }}
              </button>
            </div>
          </div>
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
  const showPaymentModal = ref(false);
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
        showPaymentModal.value = false;
        router.visit(route('reservations.index'));
      },
      onError: (errors) => {
        alert('Reservation failed: ' + JSON.stringify(errors));
        isReserving.value = false;
      },
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
    if (selectedTables.value.includes(table.id)) return '#10B981'; // Green-500
    return table.available ? '#6EE7B7' : '#D1D5DB'; // Light green or gray
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
    animation: fadeIn 0.8s ease-in;
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  </style>