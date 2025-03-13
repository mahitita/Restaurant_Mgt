<template>
    <AdminLayout>
      <section class="container mx-auto py-8 px-4">
        <h2 class="text-3xl font-semibold mb-6">Manage Tables</h2>
        <div class="grid gap-6">
          <div v-for="table in tables" :key="table.id" class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-bold mb-2">Table {{ table.table_number }}</h3>
            <p><strong>Seats:</strong> {{ table.seats }}</p>
            <div class="flex items-center mb-4">
              <label class="mr-2 font-semibold">Status:</label>
              <select
                v-model="table.status"
                @change="updateStatus(table)"
                class="border p-2 rounded"
              >
                <option value="available">Available</option>
                <option value="reserved">Reserved</option>
                <option value="occupied">Occupied</option>
              </select>
            </div>
          </div>
        </div>
      </section>
    </AdminLayout>
  </template>

  <script>
  import { ref } from 'vue';
  import { Inertia } from '@inertiajs/inertia';
import AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    components: { AdminLayout },
    props: {
      tables: Array,
    },
    setup(props) {
      const tables = ref(props.tables);

      const updateStatus = (table) => {
        Inertia.put(route('admin.tables.status', table.id), { status: table.status }, {
          onSuccess: () => {
            console.log(`Table ${table.table_number} status updated to ${table.status}`);
          },
          onError: (errors) => alert("Status update failed: " + JSON.stringify(errors)),
        });
      };

      return { tables, updateStatus };
    },
  };
  </script>
