<template>
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Inventory Management</h1>

      <!-- Success Message -->
      <div v-if="flash.success" class="bg-green-100 p-2 mb-4 rounded">
      {{ flash.success }}
    </div>

      <!-- Inventory Table -->
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gray-200">
            <th class="p-2">Name</th>
            <th class="p-2">Quantity</th>
            <th class="p-2">Threshold</th>
            <th class="p-2">Expiry Date</th>
            <th class="p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in inventory" :key="item.id" :class="{ 'bg-red-100': item.quantity <= item.threshold }">
            <td class="p-2">{{ item.name }}</td>
            <td class="p-2">{{ item.quantity }}</td>
            <td class="p-2">{{ item.threshold }}</td>
            <td class="p-2">{{ item.expiry_date || 'N/A' }}</td>
            <td class="p-2">
              <button @click="editItem(item)" class="text-blue-500 mr-2">Edit</button>
              <button @click="deleteItem(item)" class="text-red-500">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Add/Edit Form -->
      <form @submit.prevent="submitForm" class="mt-4">
        <input v-model="form.name" placeholder="Item Name" class="border p-2 mr-2" />
        <input v-model="form.quantity" type="number" placeholder="Quantity" class="border p-2 mr-2" />
        <input v-model="form.threshold" type="number" placeholder="Threshold" class="border p-2 mr-2" />
        <input v-model="form.expiry_date" type="date" class="border p-2 mr-2" />
        <button type="submit" class="bg-blue-500 text-white p-2 rounded">
          {{ editing ? 'Update' : 'Add' }}
        </button>
        <button v-if="editing" @click="cancelEdit" class="ml-2 text-gray-500">Cancel</button>
      </form>
    </div>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';

  export default {
    props: {
      inventory: Array,
    },
    data() {
      return {
        form: {
          name: '',
          quantity: '',
          threshold: 5,
          expiry_date: '',
        },
        editing: false,
        editingId: null,
      };
    },
    computed: {
    flash() {
      return this.$page.props.flash || {};
    },
  },
    methods: {
      submitForm() {
        if (this.editing) {
          Inertia.put(`/admin/inventory/${this.editingId}`, this.form);
        } else {
          Inertia.post('/admin/inventory', this.form);
        }
        this.resetForm();
      },
      resetForm() {
      this.form = { name: '', quantity: '', threshold: 5, expiry_date: '' };
      this.editing = false;
      this.editingId = null;
    },
      editItem(item) {
        this.form = { ...item };
        this.editing = true;
        this.editingId = item.id;
      },
      deleteItem(item) {
        if (confirm('Are you sure?')) {
          Inertia.delete(`/admin/inventory/${item.id}`);
        }
      },
      cancelEdit() {
        this.resetForm();
      },
      resetForm() {
        this.form = { name: '', quantity: '', threshold: 5, expiry_date: '' };
        this.editing = false;
        this.editingId = null;
      },
    },
  };
  </script>