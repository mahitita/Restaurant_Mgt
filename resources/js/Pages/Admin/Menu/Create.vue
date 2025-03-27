<template>

      <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-bold mb-6">Create New Menu Item</h1>

        <div v-if="$page.props.flash?.success" class="bg-green-100 p-3 mb-4 rounded">
          {{ $page.props.flash.success }}
        </div>


        <form @submit.prevent="submit" enctype="multipart/form-data">
          <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input
              v-model="form.name"
              type="text"
              class="border p-2 w-full"
              required
              :class="{ 'border-red-500': form.errors.name }"
            />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Price</label>
            <input
              v-model.number="form.price"
              type="number"
              step="0.01"
              class="border p-2 w-full"
              required
              :class="{ 'border-red-500': form.errors.price }"
            />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Category</label>
            <select
              v-model="form.category_id"
              class="border p-2 w-full"
              required
              :class="{ 'border-red-500': form.errors.category_id }"
            >
              <option value="">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Description</label>
            <textarea
              v-model="form.description"
              class="border p-2 w-full"
              :class="{ 'border-red-500': form.errors.description }"
            ></textarea>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Prep Time (minutes)</label>
            <input
              v-model.number="form.prep_time"
              type="number"
              class="border p-2 w-full"
              :class="{ 'border-red-500': form.errors.prep_time }"
            />
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Available</label>
            <input
              v-model="form.available"
              type="checkbox"
              class="mr-2"
              :class="{ 'border-red-500': form.errors.available }"
            />
            <span>{{ form.available ? 'Yes' : 'No' }}</span>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Ingredients</label>
            <div v-for="(item, index) in form.inventory_items" :key="index" class="flex mb-2">
              <select
                v-model="item.id"
                class="border p-2 mr-2"
                @change="updateUnit(index)"
                :class="{ 'border-red-500': form.errors[`inventory_items.${index}.id`] }"
              >
                <option value="">Select Ingredients</option>
                <option v-for="inv in inventories" :key="inv.id" :value="inv.id">
                  {{ inv.name }}
                </option>
              </select>
              <input
                v-model.number="item.quantity"
                type="number"
                step="0.001"
                class="border p-2 mr-2 w-24"
                placeholder="Qty"
                :class="{ 'border-red-500': form.errors[`inventory_items.${index}.quantity`] }"
              />
              <input
                v-model="item.unit"
                class="border p-2 mr-2 w-24"
                placeholder="Unit"
                :class="{ 'border-red-500': form.errors[`inventory_items.${index}.unit`] }"
              />
              <button type="button" @click="removeInventoryItem(index)" class="text-red-500">Remove</button>
            </div>
            <button type="button" @click="addInventoryItem" class="text-blue-500">Add Ingredient</button>
          </div>

          <div class="mb-4">
            <label class="block text-gray-700">Image</label>
            <input
              type="file"
              @change="handleFileUpload"
              accept="image/*"
              class="border p-2 w-full"
              :class="{ 'border-red-500': form.errors.image }"
            />
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition duration-300"
            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
          >
            {{ form.processing ? 'Creating...' : 'Create Menu Item' }}
          </button>
          <button
            type="button"
            @click="router.get('/admin/menus')"
            class="ml-2 text-gray-500"
            :disabled="form.processing"
          >
            Cancel
          </button>
        </form>
      </div>

  </template>

  <script setup>
  import { useForm, router } from '@inertiajs/vue3';
  import AdminLayout from '@/Layouts/AdminLayout.vue';

  const props = defineProps({
    categories: Array,
    inventories: Array,
  });

  defineOptions({
    layout: AdminLayout,
  });

  const form = useForm({
    name: '',
    price: '',
    category_id: '',
    description: '',
    prep_time: 15,
    available: true,
    inventory_items: [],
    image: null,
  });

  const handleFileUpload = (event) => {
    form.image = event.target.files[0];
  };

  const addInventoryItem = () => {
    form.inventory_items.push({ id: '', quantity: '', unit: 'unit' });
  };

  const removeInventoryItem = (index) => {
    form.inventory_items.splice(index, 1);
  };

  const updateUnit = (index) => {
    const inventory = props.inventories.find((inv) => inv.id === Number(form.inventory_items[index].id));
    if (inventory) {
      form.inventory_items[index].unit = inventory.unit;
    }
  };

  const submit = () => {
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('price', form.price.toString());
    formData.append('category_id', form.category_id.toString());
    formData.append('description', form.description || '');
    formData.append('prep_time', form.prep_time.toString());
    formData.append('available', form.available ? '1' : '0');
    formData.append('inventory_items', JSON.stringify(form.inventory_items));
    if (form.image) {
      formData.append('image', form.image);
    }

    form.post('/admin/menus', {
      data: formData,
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        form.reset();
        router.get('/admin/menus');
      },
    });
  };
  </script>

  <style scoped>
  .container {
    max-width: 800px;
  }
  </style>