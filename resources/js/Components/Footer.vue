<!-- resources/js/Components/Footer.vue -->
<template>
    <footer class="bg-gursha-gold text-white py-8">
      <div class="container mx-auto grid grid-cols-3 gap-8">
        <div>
          <h3 class="text-2xl font-bold mb-4">Gursha</h3>
          <p class="text-sm">A taste of Ethiopia, reimagined.</p>
        </div>
        <div class="text-center">
          <h4 class="text-lg font-semibold mb-4">Connect With Us</h4>
          <div class="flex justify-center space-x-6">
            <a href="#" class="text-2xl hover:scale-125 transition-transform duration-300">
              <i class="fab fa-instagram animate-bounce"></i>
            </a>
            <a href="#" class="text-2xl hover:scale-125 transition-transform duration-300">
              <i class="fab fa-twitter animate-bounce delay-100"></i>
            </a>
            <a href="#" class="text-2xl hover:scale-125 transition-transform duration-300">
              <i class="fab fa-facebook animate-bounce delay-200"></i>
            </a>
          </div>
        </div>
        <div>
          <h4 class="text-lg font-semibold mb-4">Find Us</h4>
          <p class="text-sm">123 Spice Lane, Seattle, WA</p>
          <div class="h-20 w-full bg-gray-300 mt-2 rounded animate-pulse"></div> <!-- Mini-map placeholder -->
        </div>
      </div>
      <p class="text-center text-sm mt-6">&copy; 2025 Gursha. All rights reserved.</p>
    </footer>
  </template>
  
  <style scoped>
  .delay-100 {
    animation-delay: 0.1s;
  }
  .delay-200 {
    animation-delay: 0.2s;
  }
  </style>