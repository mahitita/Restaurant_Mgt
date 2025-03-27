<template>
    <div class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-60 z-50 transition-opacity duration-300">
      <div
        class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-100"
        :class="{ 'scale-95 opacity-0': !isVisible }"
        @keydown.esc="emit('close')"
        tabindex="0"
        ref="modal"
      >
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Category</h2>
        <form @submit.prevent="submit">
          <!-- Category Name -->
          <div class="mb-5">
            <label for="name" class="block text-gray-700 font-semibold mb-2">Category Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              placeholder="Enter category name"
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-gursha-primary transition-colors duration-200"
              :class="{ 'border-red-500': form.errors.name }"
              @input="form.errors.name = ''"
              required
              autofocus
            />
            <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end space-x-3">
            <button
              type="button"
              @click="emit('close')"
              class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2 bg-gursha-primary text-white rounded-lg hover:bg-gursha-accent transition-colors duration-200 flex items-center"
              :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            >
              <span v-if="form.processing" class="inline-flex items-center">
                <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
              </span>
              <span v-else>Update</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  <script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close']);

// Form setup
const form = useForm({
  name: props.category.name,
});

// Animation state
const isVisible = ref(false);

// Modal ref for focusing
const modal = ref(null);

onMounted(() => {
  isVisible.value = true; // Trigger the entrance animation
  modal.value.focus(); // Focus the modal for accessibility
});

function submit() {
  form.put(route('admin.categories.update', props.category.id), {
    onSuccess: () => {
      form.reset();
      emit('close');
    },
  });
}
</script>