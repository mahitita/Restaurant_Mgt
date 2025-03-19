import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import piniaPersist from 'pinia-plugin-persistedstate';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import './bootstrap';
import '../css/app.css';
// Explicit import for testing
import OrderConfirmation from './Pages/OrderConfirmation.vue';

const pinia = createPinia();
pinia.use(piniaPersist);

createInertiaApp({
  title: (title) => `${title} - Laravel`,
  resolve: (name) => {
    console.log("Resolving component:", name);
    if (name === 'OrderConfirmation') {
      return Promise.resolve(OrderConfirmation); // Force direct import
    }
    return resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ).catch((err) => {
      console.error(`Failed to resolve ${name}:`, err);
      throw new Error(`Component ${name} not found`);
    });
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(pinia);
    app.mount(el);
    return app;
  },
  progress: {
    color: '#4B5563',
  },
});
