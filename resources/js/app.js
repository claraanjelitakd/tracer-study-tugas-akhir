import { createApp, h, ref } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PigoLoader from './components/ui/pigo-loader.vue';

// Global loading state (dibuat true agar muncul saat first load)
const isNavigating = ref(true);

// Matikan loader saat aplikasi pertama kali termuat
setTimeout(() => {
    isNavigating.value = false;
}, 1500); // 1.5 detik loading awal

// Listen to Inertia router events
router.on('start', () => isNavigating.value = true);
router.on('finish', () => {
    setTimeout(() => {
        isNavigating.value = false;
    }, 500);
});

createInertiaApp({
    title: (title) => `${title} - Tracer Study`,
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.vue');
        // Cari file vue yang cocok (mengabaikan besar-kecil huruf/case-insensitive)
        const path = Object.keys(pages).find(
            (key) => key.toLowerCase() === `./pages/${name}.vue`.toLowerCase()
        );
        if (!path) {
            throw new Error(`Page not found: ./pages/${name}.vue`);
        }
        return typeof pages[path] === 'function' ? pages[path]() : pages[path];
    },
    setup({ el, App, props, plugin }) {
        const AppWrapper = {
            setup() {
                return () => h('div', [
                    h(PigoLoader, { isLoading: isNavigating.value }),
                    h(App, props)
                ]);
            }
        };

        return createApp(AppWrapper)
            .use(plugin)
            .mount(el);
    },
});
