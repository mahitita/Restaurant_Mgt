<template>
    <AdminLayout>
      <div class="p-6 bg-white shadow rounded-lg">
        <h1 class="text-2xl font-semibold mb-4">Categories</h1>

        <!-- Add Category Button -->
        <button
          @click="showCreateModal = true"
          class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
        >
          + Add Category
        </button>

        <!-- Success Message -->
        <div v-if="successMessage" class="p-3 bg-green-200 text-green-800 rounded mb-4">
          {{ successMessage }}
        </div>

        <!-- Category Table -->
        <table class="w-full border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-100">
              <th class="border border-gray-300 p-2">#</th>
              <th class="border border-gray-300 p-2">Name</th>
              <th class="border border-gray-300 p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(category, index) in categories" :key="category.id">
              <td class="border border-gray-300 p-2">{{ index + 1 }}</td>
              <td class="border border-gray-300 p-2">{{ category.name }}</td>
              <td class="border border-gray-300 p-2">
                <button
                  @click="editCategory(category)"
                  class="bg-yellow-500 text-white px-3 py-1 rounded mr-2"
                >
                  Edit
                </button>
                <button
                  @click="deleteCategory(category.id)"
                  class="bg-red-500 text-white px-3 py-1 rounded"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Create Category Modal -->
        <CreateForm v-if="showCreateModal" @close="showCreateModal = false" />

        <!-- Edit Category Modal -->
        <EditForm v-if="showEditModal" :category="selectedCategory" @close="showEditModal = false" />
      </div>
    </AdminLayout>
  </template>

  <script>
  import AdminLayout from "@/Layouts/AdminLayout.vue";
  import CreateForm from "./Create.vue";
  import EditForm from "./Edit.vue";
  import { router } from "@inertiajs/vue3";

  export default {
    components: { AdminLayout, CreateForm, EditForm },
    props: { categories: Array },
    data() {
      return {
        showCreateModal: false,
        showEditModal: false,
        selectedCategory: null,
        successMessage: "",
      };
    },
    methods: {
      editCategory(category) {
        this.selectedCategory = category;
        this.showEditModal = true;
      },
      deleteCategory(id) {
        if (confirm("Are you sure?")) {
          router.delete(`/admin/categories/${id}`, {
            onSuccess: () => (this.successMessage = "Category deleted successfully."),
          });
        }
      },
    },
  };
  </script>
