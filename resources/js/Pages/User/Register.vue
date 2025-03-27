<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
      <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-bold text-gursha-primary mb-6 text-center">Register as Customer</h1>
  
        <div v-if="flash.error" class="bg-red-100 p-4 mb-4 rounded text-red-700">
          {{ flash.error }}
        </div>
        <div v-if="flash.success" class="bg-green-100 p-4 mb-4 rounded text-green-700">
          {{ flash.success }}
        </div>
  
        <form @submit.prevent="register">
          <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input
              v-model="name"
              type="text"
              id="name"
              class="w-full p-2 border rounded focus:ring-gursha-primary focus:outline-none"
              placeholder="Enter your name"
              style="border: 1px solid black; padding: 8px; color: black; background: white;"
              @input="logInput('name', $event.target.value)"
              required
            />
          </div>
          <div class="mb-4">
            <label for="phone" class="block text-gray-700">Phone</label>
            <input
              v-model="phone"
              type="text"
              id="phone"
              class="w-full p-2 border rounded focus:ring-gursha-primary focus:outline-none"
              placeholder="Enter your phone"
              style="border: 1px solid black; padding: 8px; color: black; background: white;"
              @input="logInput('phone', $event.target.value)"
              required
            />
          </div>
          <div class="mb-4">
            <label for="email" class="block text-gray-700">Email (Optional)</label>
            <input
              v-model="email"
              type="email"
              id="email"
              class="w-full p-2 border rounded focus:ring-gursha-primary focus:outline-none"
              placeholder="Enter your email"
              style="border: 1px solid black; padding: 8px; color: black; background: white;"
              @input="logInput('email', $event.target.value)"
            />
          </div>
          <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input
              v-model="password"
              type="password"
              id="password"
              class="w-full p-2 border rounded focus:ring-gursha-primary focus:outline-none"
              placeholder="Enter your password"
              style="border: 1px solid black; padding: 8px; color: black; background: white;"
              @input="logInput('password', $event.target.value)"
              required
            />
          </div>
          <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
            <input
              v-model="password_confirmation"
              type="password"
              id="password_confirmation"
              class="w-full p-2 border rounded focus:ring-gursha-primary focus:outline-none"
              placeholder="Confirm your password"
              style="border: 1px solid black; padding: 8px; color: black; background: white;"
              @input="logInput('password_confirmation', $event.target.value)"
              required
            />
          </div>
          <button
            type="submit"
            class="w-full bg-gursha-primary text-white py-2 rounded-full hover:bg-gursha-accent"
            :disabled="processing"
          >
            {{ processing ? 'Registering...' : 'Register' }}
          </button>
        </form>
  
        <p class="mt-4 text-center text-gray-600">
          Already have an account? <a href="/user/login" class="text-gursha-primary hover:underline">Login</a>
        </p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3';
  
  const name = ref('');
  const phone = ref('');
  const email = ref('');
  const password = ref('');
  const password_confirmation = ref('');
  const processing = ref(false);
  const flash = ref({ success: null, error: null });
  
  const logInput = (field, value) => {
    console.log(`${field} input:`, value);
  };
  
  const register = () => {
    console.log('Register form submitted:', {
      name: name.value,
      phone: phone.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });
    processing.value = true;
    router.post('/user/register', {
      name: name.value,
      phone: phone.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    }, {
      onSuccess: (page) => {
        console.log('Registration successful:', page);
        flash.value.success = page.props.flash.success;
        processing.value = false;
      },
      onError: (errors) => {
        console.error('Registration failed:', errors);
        flash.value.error = errors.message || 'Registration failed.';
        processing.value = false;
      },
    });
  };
  </script>