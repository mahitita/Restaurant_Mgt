<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Add Menu Item</h1>

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input v-model="form.name" type="text" class="border p-2 w-full rounded" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Category</label>
            <select v-model="form.category_id" class="border p-2 w-full rounded" required>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Price</label>
            <input v-model="form.price" type="number" class="border p-2 w-full rounded" required />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Description</label>
            <textarea v-model="form.description" class="border p-2 w-full rounded"></textarea>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Image</label>
            <input type="file" @change="handleFileUpload" class="border p-2 w-full rounded" />
          </div>

          <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
          </div>
        </form>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";


  export default {
    components: { AdminLayout },
    props: { categories: Array },
    setup() {
      const form = useForm({
        name: "",
        price: "",
        category_id: "",
        description: "",
        image: null,
      });

      function handleFileUpload(event) {
        form.image = event.target.files[0];
      }

      function submit() {
        form.post(route("admin.menus.store"), { onSuccess: () => location.reload() });
      }

      return { form, submit, handleFileUpload };
    },
  };
  </script>
