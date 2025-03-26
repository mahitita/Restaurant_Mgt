<template>
    <div class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50">
      <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Category</h2>
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label for="name" class="block text-gray-700 font-semibold mb-2">Category Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              placeholder="Enter category name"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition"
              :class="{ 'border-red-500': form.errors.name }"
            />
            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
          </div>
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="emit('close')"
              class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
              :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            >
              {{ form.processing ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>

  <script>
  import { useForm } from "@inertiajs/vue3";

  export default {
    
    emits: ['close'], // Define the 'close' event
  setup(props, { emit }) { // Access emit from the setup context
    const form = useForm({
      name: '',
    });
      function submit() {
        form.post(route("admin.categories.store"), {
          onSuccess: () => {
            form.reset();
            emit("close");
          },
        });
      }

      return { form, submit, emit };
    },
  };
  </script>