<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full bg-white rounded-lg shadow-xl p-8 animate-fade-in">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-extrabold text-gray-800">Admin Login</h1>
          <p class="mt-2 text-sm text-gray-600">Sign in to manage Gursha</p>
        </div>

        <!-- Error Message -->
        <div v-if="form.error" class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
          {{ form.error }}
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Email Field -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input
              id="email"
              type="email"
              v-model="form.email"
              required
              class="mt-1 w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300"
              placeholder="admin@example.com"
            />
          </div>

          <!-- Password Field -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
              id="password"
              type="password"
              v-model="form.password"
              required
              class="mt-1 w-full p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300"
              placeholder="••••••••"
            />
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-orange-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-700 hover:shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center justify-center"
          >
            <span v-if="form.processing" class="flex items-center">
              <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              </svg>
              Signing In...
            </span>
            <span v-else>Sign In</span>
          </button>
        </form>

      
      </div>
    </div>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { router, Link, useForm } from '@inertiajs/vue3';

  const form = useForm({
    email: '',
    password: '',
    error: null,
  });

  const submit = () => {
    form.post(route('admin.login'), {
      onSuccess: () => {
        router.visit(route('admin.dashboard')); // Redirect to admin dashboard
      },
      onError: (errors) => {
        form.error = errors.email || errors.password || 'Invalid credentials. Please try again.';
      },
    });
  };
  </script>

  <style scoped>
  .animate-fade-in {
    animation: fadeIn 1s ease-in;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  </style>