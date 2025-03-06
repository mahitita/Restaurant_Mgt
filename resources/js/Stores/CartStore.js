import { defineStore } from "pinia";

export const useCartStore = defineStore("cart", {
  state: () => ({
    cartItems: [],
  }),

  actions: {
    addToCart(menuItem) {
      const existingItem = this.cartItems.find(item => item.id === menuItem.id);
      if (existingItem) {
        existingItem.quantity += 1; // Update the quantity if item already exists
      } else {
        this.cartItems.push({ ...menuItem, quantity: 1 }); // Add new item to the cart
      }
      console.log("Cart after adding:", this.cartItems); // Log cart items after adding
    },

    removeFromCart(menuId) {
      this.cartItems = this.cartItems.filter(item => item.id !== menuId);
      console.log("Cart after removal:", this.cartItems); // Log cart after removal
    },

    clearCart() {
      this.cartItems = [];
      console.log("Cart cleared:", this.cartItems); // Log cart after clearing
    },
  },

  getters: {
    totalItems(state) {
      return state.cartItems.reduce((total, item) => total + item.quantity, 0);
    },
    totalPrice(state) {
      return state.cartItems.reduce((total, item) => total + item.price * item.quantity, 0);
    },
  },
});
