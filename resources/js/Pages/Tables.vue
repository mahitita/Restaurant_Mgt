<template>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-2xl font-bold mb-4">Select Your Table</h1>
      <svg viewBox="0 0 800 600" class="w-full h-auto border rounded-lg shadow-lg">
        <!-- Render each table as an SVG element -->
        <g v-for="table in tables" :key="table.id" @click="selectTable(table)" style="cursor: pointer">
          <!-- Render rectangle tables -->
          <template v-if="table.type === 'rectangle'">
            <rect
              :x="table.x_coordinate"
              :y="table.y_coordinate"
              :width="table.width"
              :height="table.height"
              :fill="selectedTable === table.id ? 'green' : table.status === 'available' ? '#90EE90' : '#D3D3D3'"
              stroke="black"
              rx="10"
              ry="10"
            />
          </template>
          <!-- Render round tables -->
          <template v-else-if="table.type === 'round'">
            <circle
              :cx="table.x_coordinate"
              :cy="table.y_coordinate"
              :r="(table.width || 60) / 2"
              :fill="selectedTable === table.id ? 'green' : table.status === 'available' ? '#90EE90' : '#D3D3D3'"
              stroke="black"
            />
          </template>
          <!-- Render oval tables -->
          <template v-else-if="table.type === 'oval'">
            <ellipse
              :cx="table.x_coordinate + table.width / 2"
              :cy="table.y_coordinate + table.height / 2"
              :rx="table.width / 2"
              :ry="table.height / 2"
              :fill="selectedTable === table.id ? 'green' : table.status === 'available' ? '#90EE90' : '#D3D3D3'"
              stroke="black"
            />
          </template>
          <!-- Render square tables -->
          <template v-else-if="table.type === 'square'">
            <rect
              :x="table.x_coordinate"
              :y="table.y_coordinate"
              :width="table.width"
              :height="table.width"
              :fill="selectedTable === table.id ? 'green' : table.status === 'available' ? '#90EE90' : '#D3D3D3'"
              stroke="black"
              rx="10"
              ry="10"
            />
          </template>
          <!-- Table Number -->
          <text
            :x="table.x_coordinate + (table.type === 'rectangle' ? table.width / 2 : 0)"
            :y="table.y_coordinate + (table.type === 'rectangle' ? table.height / 2 : 0)"
            text-anchor="middle"
            alignment-baseline="middle"
            fill="black"
            font-size="16"
            font-weight="bold"
          >
            {{ table.table_number }}
          </text>
          <!-- Chairs -->
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
      <div v-if="selectedTable" class="mt-4 p-4 bg-green-100 border border-green-400 rounded-lg">
        <p class="text-lg">You have selected Table {{ selectedTable }}</p>
        <button @click="reserveTable" class="bg-blue-500 text-white px-4 py-2 rounded">Reserve Table</button>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
  const tables = ref([]);
  const selectedTable = ref(null);

  const { props } = usePage();
  tables.value = props.tables;

  const selectTable = (table) => {
    if (table.status !== 'available') {
      alert('This table is not available!');
      return;
    }
    selectedTable.value = table.id;
  };

  const reserveTable = () => {
    Inertia.post('/tables', { table_id: selectedTable.value });
  };

  const getChairs = (table) => {
    const chairs = [];
    const chairSpacing = 20;

    // Calculate chair positions based on table dimensions and number of seats
    if (table.type === 'rectangle' || table.type === 'square') {
      for (let i = 0; i < table.seats; i++) {
        let x, y;
        if (i < table.seats / 2) {
          x = table.x_coordinate - chairSpacing;
          y = table.y_coordinate + (i * (table.height / (table.seats / 2))) + (table.height / (table.seats / 4));
        } else {
          x = table.x_coordinate + table.width + chairSpacing;
          y = table.y_coordinate + ((i - table.seats / 2) * (table.height / (table.seats / 2))) + (table.height / (table.seats / 4));
        }
        chairs.push({ id: `chair-${table.id}-${i}`, x, y });
      }
    } else if (table.type === 'round' || table.type === 'oval') {
      const radius = (table.width || 60) / 2 + chairSpacing;
      for (let i = 0; i < table.seats; i++) {
        const angle = (i * 360 / table.seats) * (Math.PI / 180);
        const x = table.x_coordinate + radius * Math.cos(angle);
        const y = table.y_coordinate + radius * Math.sin(angle);
        chairs.push({ id: `chair-${table.id}-${i}`, x, y });
      }
    }

    return chairs;
  };
  </script>

  <style scoped>
  /* Custom styles for better UI */
  svg {
    max-width: 800px;
    max-height: 600px;
  }

  rect {
    transition: fill 0.3s ease;
  }

  rect:hover,
  circle:hover,
  ellipse:hover {
    fill: #87CEFA; /* Light sky blue */
  }
  </style>
