<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import LoadingButton from '../../components/ui/loading-button.vue';

const form = useForm({
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post('/change-password', {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Ganti Password - SERU UKDW" />

    <div class="min-h-screen flex items-center justify-center bg-green-900 p-4 relative overflow-hidden">
        <!-- Dekorasi Background -->
        <div class="absolute top-[-10%] left-[-10%] w-64 h-64 rounded-full bg-green-800 opacity-50 mix-blend-multiply blur-2xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-80 h-80 rounded-full bg-yellow-900 opacity-50 mix-blend-multiply blur-3xl"></div>

        <div class="relative w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100 z-10">
            
            <div class="mb-8 text-center">
                <div class="flex justify-center mb-6">
                    <img src="/uploads/landing/1.png" alt="Pigo Security" class="w-24 h-24 object-contain filter drop-shadow-md" />
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Ubah Password Anda</h2>
                <p class="text-gray-500 mt-2 text-sm">Demi keamanan, Anda diwajibkan untuk mengubah password default sebelum melanjutkan ke sistem.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <div class="mt-1 relative">
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-4 py-2 border"
                            :class="{'border-red-500': form.errors.password}"
                            placeholder="Minimal 8 karakter"
                            required
                            autofocus
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 text-gray-500 hover:text-gray-700"
                        >
                            <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input
                        id="password_confirmation"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password_confirmation"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-4 py-2 border"
                        placeholder="Ulangi password baru"
                        required
                    />
                </div>

                <div class="pt-2">
                    <LoadingButton 
                        type="submit" 
                        class="w-full" 
                        :isLoading="form.processing"
                    >
                        Simpan Password Baru
                    </LoadingButton>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <form @submit.prevent="useForm().post('/logout')">
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-900 underline">Batal & Keluar</button>
                </form>
            </div>
        </div>
    </div>
</template>
