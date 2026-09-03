<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import LoadingButton from '../../components/ui/loading-button.vue';

const form = useForm({
    username: '',
    password: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login - SERU UKDW" />

    <div class="min-h-screen flex flex-col lg:flex-row bg-white">
        
        <!-- Bagian Kiri: Visual Branding (SIMASTER Style) -->
        <div class="hidden lg:flex lg:w-7/12 relative bg-gray-900 flex-col justify-center p-12 xl:p-24 text-white">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Campus Building" class="w-full h-full object-cover opacity-40 mix-blend-overlay" />
                <div class="absolute inset-0 bg-gradient-to-b from-green-900/90 to-gray-900/95"></div>
            </div>

            <!-- Content -->
            <div class="z-10 w-full max-w-2xl mt-auto mb-auto">
                <!-- Logos -->
                <div class="flex items-center gap-6 mb-8">
                    <!-- Logo Pigo diperbesar (w-48 h-48) -->
                    <img src="/uploads/landing/1.png" alt="Pigo Mascot" class="w-40 h-40 sm:w-48 sm:h-48 object-contain filter drop-shadow-2xl" />
                </div>

                <h1 class="text-5xl lg:text-6xl font-black mb-4 tracking-tight">
                    PORTAL TRACER <span class="text-yellow-400">UKDW</span>
                </h1>
                
                <p class="text-lg lg:text-xl font-light text-gray-300 max-w-xl mb-12 leading-relaxed">
                    Sistem informasi Ekosistem Rekam Jejak Alumni, untuk Mendukung Tercapainya Lulusan Unggul dan Berkarakter di Universitas Kristen Duta Wacana.
                </p>
            </div>
        </div>

        <!-- Bagian Kanan: Form Login (Clean White Panel) -->
        <div class="w-full lg:w-5/12 flex items-center justify-center p-8 sm:p-12 lg:p-16">
            <div class="w-full max-w-sm">
                
                <div class="mb-12 text-center">
                    <h2 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">SIGN IN</h2>
                    <p class="text-gray-500 font-medium">Akun Tracer Study Anda</p>
                </div>

                <!-- Notifikasi Error Global -->
                <div v-if="form.errors.username" class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600 font-medium text-center">{{ form.errors.username }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-semibold text-slate-700 mb-2">NIM atau Username</label>
                        <input
                            id="username"
                            type="text"
                            v-model="form.username"
                            class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 transition-colors"
                            placeholder="Alumni gunakan NIM, Admin gunakan username"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Kata Sandi / Tanggal Lahir</label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-3 pr-10 transition-colors"
                                placeholder="Alumni gunakan format YYYY-MM-DD"
                                required
                                autocomplete="current-password"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-gray-700 focus:outline-none"
                            >
                                <svg v-if="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <LoadingButton 
                            type="submit" 
                            class="w-full bg-[#0074b7] hover:bg-[#005f96] text-white py-3 rounded-xl shadow-md transition-all font-bold tracking-wide" 
                            :isLoading="form.processing"
                        >
                            Masuk ke Sistem
                        </LoadingButton>
                    </div>

                    <!-- Divider -->
                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white text-gray-500">Atau</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-slate-700 font-semibold rounded-xl transition-colors">
                            Lupa Kata Sandi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>
