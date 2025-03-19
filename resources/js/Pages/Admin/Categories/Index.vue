<template>
  <AdminLayout>
    <div class="container mx-auto py-8 px-4">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Categories</h1>
          <button
            @click="showCreateModal = true"
            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
          >
            + Add Category
          </button>
        </div>

        <transition name="fade">
          <div
            v-if="successMessage"
            class="p-4 bg-green-100 text-green-800 rounded-lg mb-6 flex justify-between items-center"
          >
            <span>{{ successMessage }}</span>
            <button @click="successMessage = ''" class="text-green-800 hover:text-green-600">
              ✕
            </button>
          </div>
        </transition>

        <div class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-50 text-gray-700">
                <th class="p-4 text-left font-semibold">#</th>
                <th class="p-4 text-left font-semibold">Name</th>
                <th class="p-4 text-left font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(category, index) in categories"
                :key="category.id"
                class="border-b hover:bg-gray-50 transition"
              >
                <td class="p-4 text-gray-600">{{ index + 1 }}</td>
                <td class="p-4 text-gray-800">{{ category.name }}</td>
                <td class="p-4">
                  <button
                    @click="editCategory(category)"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition mr-2"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteCategory(category.id)"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                  >
                    Delete
                  </button>
                </td>
              </tr>
              <tr v-if="categories.length === 0">
                <td colspan="3" class="p-4 text-center text-gray-500">No categories found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <CreateForm v-if="showCreateModal" @close="showCreateModal = false" />
        <EditForm v-if="showEditModal" :category="selectedCategory" @close="showEditModal = false" />
      </div>
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
  mounted() {
    if (this.$page.props.flash && this.$page.props.flash.success) {
      this.successMessage = this.$page.props.flash.success;
    }
  },
  methods: {
    editCategory(category) {
      this.selectedCategory = category;
      this.showEditModal = true;
    },
    deleteCategory(id) {
      if (confirm("Are you sure you want to delete this category?")) {
        router.delete(`/admin/categories/${id}`, {
          onSuccess: () => {
            this.successMessage = "Category deleted successfully.";
          },
        });
      }
    },
  },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
tr:hover {
  background-color: #f9fafb;
}
</style>