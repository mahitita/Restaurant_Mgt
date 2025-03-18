<template>
    <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Edit Menu Item</h1>

      <div v-if="flash.success" class="bg-green-100 p-2 mb-4 rounded">
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
          <label class="block text-gray-700">Current Image</label>
          <img v-if="form.image" :src="'/storage/' + form.image" alt="Menu Image" class="w-32 h-32 object-cover mb-2" />
          <span v-else>No Image</span>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700">Upload New Image</label>
          <input type="file" @change="handleFileUpload" accept="image/*" class="border p-2 w-full" />
        </div>

        <button type="submit" class="bg-blue-500 text-white p-2 rounded">Update Menu Item</button>
        <router-link to="/admin/menus" class="ml-2 text-gray-500">Cancel</router-link>
      </form>
    </div>
  </template>

  <script>
  import { Inertia } from '@inertiajs/inertia';

  export default {
    props: {
      menu: Object,
      categories: Array,
    },
    data() {
      return {
        form: { ...this.menu }, // Clone menu object to avoid mutating prop directly
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
      submitForm() {
        const formData = new FormData();
        formData.append('name', this.form.name);
        formData.append('price', this.form.price);
        formData.append('category_id', this.form.category_id);
        formData.append('description', this.form.description || '');
        if (this.form.image && typeof this.form.image !== 'string') {
          formData.append('image', this.form.image);
        }
        formData.append('_method', 'PUT'); // Spoof PUT request for Laravel

        Inertia.post(`/admin/menus/${this.menu.id}`, formData, {
          onSuccess: () => {
            console.log('Menu updated');
          },
        });
      },
    },
  };
  </script>