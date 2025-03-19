<template>
    <AdminLayout>
      <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-bold mb-6">Edit Menu Item</h1>

        <div v-if="flash.success" class="bg-green-100 p-3 mb-4 rounded">
          {{ flash.success }}
        </div>

        <form @submit.prevent="submitForm" enctype="multipart/form-data">
          <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input v-model="form.name" type="text" class="border p-2 w-full" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Price</label>
            <input v-model="form.price" type="number" step="0.01" class="border p-2 w-full" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Category</label>
            <select v-model="form.category_id" class="border p-2 w-full" required>
              <option value="">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Description</label>
            <textarea v-model="form.description" class="border p-2 w-full"></textarea>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Prep Time (minutes)</label>
            <input v-model="form.prep_time" type="number" class="border p-2 w-full" />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Available</label>
            <input v-model="form.available" type="checkbox" class="mr-2" />
            <span>{{ form.available ? 'Yes' : 'No' }}</span>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Ingredients</label>
            <div v-for="(item, index) in form.inventory_items" :key="index" class="flex mb-2">
              <select v-model="item.id" class="border p-2 mr-2" @change="updateUnit(index)">
                <option value="">Select Inventory</option>
                <option v-for="inv in inventories" :key="inv.id" :value="inv.id">
                  {{ inv.name }}
                </option>
              </select>
              <input v-model="item.quantity" type="number" step="0.001" class="border p-2 mr-2 w-24" placeholder="Qty" />
              <input v-model="item.unit" class="border p-2 mr-2 w-24" placeholder="Unit" />
              <button type="button" @click="removeInventoryItem(index)" class="text-red-500">Remove</button>
            </div>
            <button type="button" @click="addInventoryItem" class="text-blue-500">Add Ingredient</button>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Current Image</label>
            <img v-if="form.image" :src="'/storage/' + form.image" alt="Menu Image" class="w-32 h-32 object-cover mb-2" />
            <span v-else>No Image</span>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Upload New Image</label>
            <input type="file" @change="handleFileUpload" accept="image/*" class="border p-2 w-full" />
          </div>

          <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-300">Update Menu Item</button>
          <button type="button" @click="goToIndex" class="ml-2 text-gray-500">Cancel</button>
        </form>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';
import  AdminLayout from '@/Layouts/AdminLayout.vue';
  export default {
    layout: AdminLayout,
    props: {
      menu: Object,
      categories: Array,
      inventories: Array,
    },
    data() {
      return {
        form: {
          ...this.menu,
          inventory_items: this.menu.inventories.map(inv => ({
            id: inv.id,
            quantity: inv.pivot.quantity,
            unit: inv.pivot.unit,
          })),
        },
      };
    },
    computed: {
      flash() {
        return this.$page.props.flash || {};
      },
    },
    methods: {
      handleFileUpload(event) {
        this.form.image = event.target.files[0];
      },
      addInventoryItem() {
        this.form.inventory_items.push({ id: '', quantity: '', unit: 'unit' });
      },
      goToIndex() {
        Inertia.visit('/admin/menus');
      },
      removeInventoryItem(index) {
        this.form.inventory_items.splice(index, 1);
      },
      updateUnit(index) {
        const inventory = this.inventories.find(inv => inv.id === this.form.inventory_items[index].id);
        if (inventory) {
          this.form.inventory_items[index].unit = inventory.unit;
        }
      },
      submitForm() {
        const formData = new FormData();
        for (const key in this.form) {
          if (key !== 'inventory_items' && key !== 'image' && this.form[key] !== null) {
            formData.append(key, this.form[key]);
          }
        }
        if (this.form.inventory_items.length) {
          formData.append('inventory_items', JSON.stringify(this.form.inventory_items));
        }
        if (this.form.image && typeof this.form.image !== 'string') {
          formData.append('image', this.form.image);
        }
        formData.append('_method', 'PUT');
        Inertia.post(`/admin/menus/${this.menu.id}`, formData);
      },
    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 800px; /* Adjust as needed */
  }
  </style>