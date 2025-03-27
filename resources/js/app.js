import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import piniaPersist from 'pinia-plugin-persistedstate';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import './bootstrap';
import '../css/app.css';

const pinia = createPinia();
pinia.use(piniaPersist);

// Debug available pages
const pages = import.meta.glob('./Pages/**/*.vue');
// console.log('Available pages:', Object.keys(pages));

createInertiaApp({
  title: (title) => `${title} - Laravel`,
  resolve: async (name) => {
    console.log("Resolving component:", name);
    try {
      const page = await resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob('./Pages/**/*.vue')
      );
      console.log(`Successfully resolved ${name}:`, page);
      return page;
    } catch (err) {
      console.error(`Failed to resolve ${name}:`, err);
      // Fallback to Error.vue
      const errorPage = await resolvePageComponent(
        `./Pages/Error.vue`,
        import.meta.glob('./Pages/**/*.vue')
      );
      console.log("Falling back to Error.vue");
      return errorPage;
    }
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Toast, {
        position: 'top-right',
        timeout: 3000,
      })
      .use(ZiggyVue)
      .use(pinia);
    app.mount(el);
    return app;
  },
  progress: {
    color: '#4B5563',
  },
});