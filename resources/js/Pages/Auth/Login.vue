<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import LoadingButton from '../../Components/UI/LoadingButton.vue';

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

    <div class="min-h-screen flex flex-col sm:flex-row bg-gray-50">
        <!-- Bagian Kiri: Visual Branding -->
        <div class="hidden sm:flex sm:w-1/2 bg-green-700 items-center justify-center p-12 text-white flex-col relative overflow-hidden">
            <!-- Dekorasi Sederhana -->
            <div class="absolute top-[-10%] left-[-10%] w-64 h-64 rounded-full bg-green-600 opacity-50 mix-blend-multiply blur-2xl"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-80 h-80 rounded-full bg-yellow-400 opacity-20 mix-blend-multiply blur-3xl"></div>
            
            <div class="z-10 text-center">
                <h1 class="text-5xl font-bold mb-4 tracking-tight">SERU</h1>
                <p class="text-xl font-light text-green-100 max-w-md mx-auto">Sistem Ekosistem Rekam Jejak Alumni Universitas Kristen Duta Wacana</p>
            </div>
        </div>

        <!-- Bagian Kanan: Form Login -->
        <div class="flex-1 flex items-center justify-center p-8 sm:p-12">
            <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                
                <div class="mb-8 text-center sm:text-left">
                    <h2 class="text-2xl font-bold text-gray-900">Selamat Datang di SERU</h2>
                    <p class="text-gray-500 mt-1">Silakan masuk menggunakan akun Anda</p>
                </div>

                <!-- Notifikasi Error Global (Misal: kredensial salah) -->
                <div v-if="form.errors.username" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600 font-medium">{{ form.errors.username }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username atau NIM</label>
                        <input
                            id="username"
                            type="text"
                            v-model="form.username"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-4 py-2 border"
                            placeholder="Masukkan username atau NIM"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-4 py-2 border pr-10"
                                placeholder="Masukkan password"
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

                    <div>
                        <LoadingButton 
                            type="submit" 
                            class="w-full" 
                            :isLoading="form.processing"
                        >
                            Masuk
                        </LoadingButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
