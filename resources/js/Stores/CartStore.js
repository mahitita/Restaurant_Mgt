import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
  state: () => ({
    cartItems: [],
  }),
  actions: {
    addToCart(menuItem) {
      const existingItem = this.cartItems.find(item => item.id === menuItem.id);
      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        this.cartItems.push({ ...menuItem, quantity: 1 });
      }
      console.log('Cart after adding:', this.cartItems);
    },
    removeFromCart(menuId) {
      this.cartItems = this.cartItems.filter(item => item.id !== menuId);
    },
    updateQuantity(menuId, quantity) {
      const item = this.cartItems.find(item => item.id === menuId);
      if (item) {
        item.quantity = quantity;
      }
    },
    clearCart() {
      this.cartItems = [];
      console.log('Cart cleared:', this.cartItems); // Debug log
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
  persist: true, // Persist the store state
});