<!-- resources/js/Pages/User/Login.vue -->
<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
      <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-bold text-gursha-primary mb-6 text-center">Customer Login</h1>

        <div v-if="flash.error" class="bg-red-100 p-4 mb-4 rounded text-red-700">
          {{ flash.error }}
        </div>
        <div v-if="flash.success" class="bg-green-100 p-4 mb-4 rounded text-green-700">
          {{ flash.success }}
        </div>

        <form @submit.prevent="login">
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
          <div class="mb-6">
            <label for="password" class="block text-gray-700">Password</label>
            <input
              v-model="password"
              type="password"
              id="password"
              class="w-full p-2 border rounded focus:ring-gursha-primary focus:outline-none"
              style="border: 1px solid black; padding: 8px; color: black; background: white;"
              placeholder="Enter your password"
              @input="logInput('password', $event.target.value)"
              required
            />
          </div>
          <button
            type="submit"
            class="w-full bg-gursha-primary text-white py-2 rounded-full hover:bg-gursha-accent"
            :disabled="processing"
          >
            {{ processing ? 'Logging in...' : 'Login' }}
          </button>
        </form>

        <p class="mt-4 text-center text-gray-600">
          Don’t have an account? <a href="/user/register" class="text-gursha-primary hover:underline">Register</a>
        </p>
      </div>
    </div>
  </template>

  <script setup>
  import { ref, watch } from 'vue';
  import { router, usePage } from '@inertiajs/vue3';

  const props = defineProps({
    return_to: String,
  });

  const phone = ref('');
  const password = ref('');
  const processing = ref(false);
  const flash = ref({ success: null, error: null });

  console.log('Login component mounted, return_to:', props.return_to);

  const logInput = (field, value) => {
    console.log(`${field} input:`, value);
  };

  const login = () => {
    console.log('Login form submitted:', { phone: phone.value, password: password.value, return_to: props.return_to });
    processing.value = true;
    router.post(route('user.login.store'), {
      phone: phone.value,
      password: password.value,
      return_to: props.return_to,
    }, {
      preserveState: true,
      onSuccess: (page) => {
        console.log('Login successful, page:', page);
        flash.value.success = page.props.flash?.success;
        processing.value = false;

        // Manually navigate to the return_to URL if the redirect doesn't happen automatically
        const redirectTo = props.return_to || route('home');
        console.log('Navigating to:', redirectTo);
        router.visit(redirectTo, {
          onSuccess: () => {
            console.log('Navigation successful');
          },
          onError: (errors) => {
            console.error('Navigation failed:', errors);
          },
        });
      },
      onError: (errors) => {
        console.error('Login failed:', errors);
        flash.value.error = errors.phone || errors.message || 'Login failed.';
        processing.value = false;
      },
      onFinish: () => {
        console.log('Request finished');
        processing.value = false;
      },
    });
  };

  // Watch for flash messages to update the UI
  watch(
    () => usePage().props.flash,
    (newFlash) => {
      if (newFlash?.success) {
        flash.value.success = newFlash.success;
      }
      if (newFlash?.error) {
        flash.value.error = newFlash.error;
      }
    },
    { deep: true }
  );

  // Watch for auth changes to ensure the user is recognized as logged in
  watch(
    () => usePage().props.auth,
    (newAuth) => {
      console.log('Auth state changed:', newAuth);
      if (newAuth?.user) {
        const redirectTo = props.return_to || route('home');
        console.log('User logged in, navigating to:', redirectTo);
        router.visit(redirectTo);
      }
    },
    { deep: true }
  );
  </script>