<template>
    <div class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-lg font-bold mb-4">Add New Category</h2>
        <input
          type="text"
          v-model="form.name"
          placeholder="Category Name"
          class="border p-2 w-full rounded"
        />
        <div class="flex justify-end mt-4">
          <button @click="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
          <button @click="$emit('close')" class="ml-2 bg-gray-300 px-4 py-2 rounded">Cancel</button>
        </div>
      </div>
    </div>
  </template>

  <script>
  import { useForm } from "@inertiajs/vue3";

  export default {
    setup() {
      const form = useForm({ name: "" });

      function submit() {
        form.post("/admin/categories", { onSuccess: () => location.reload() });
      }

      return { form, submit };
    },
  };
  </script>
