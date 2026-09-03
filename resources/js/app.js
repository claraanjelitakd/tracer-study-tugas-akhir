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
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
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
