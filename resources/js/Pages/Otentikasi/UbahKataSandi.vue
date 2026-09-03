<!--
  Halaman Ubah Kata Sandi Wajib
  Fungsi: Menampilkan antarmuka untuk mengganti kata sandi bawaan sistem.
-->
<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/change-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Ubah Kata Sandi - Tracer Study" />

    <div class="relative min-h-screen flex items-center justify-center lg:justify-end font-sans overflow-hidden">
        
        <!-- Gambar Background Full Screen dengan Overlay Hijau -->
        <div class="absolute inset-0 z-0">
            <img src="/uploads/landing/1.png" alt="Background Kampus UKDW" class="w-full h-full object-cover" onerror="this.style.display='none'">
            <div class="absolute inset-0 bg-gradient-to-r from-[#005B3C]/95 via-[#005B3C]/80 to-[#003B26]/90 mix-blend-multiply"></div>
        </div>

        <!-- Konten Kiri (Branding & Teks) - Tampil di layar besar -->
        <div class="hidden lg:flex absolute left-0 top-0 bottom-0 w-1/2 z-10 flex-col justify-center px-16 xl:px-24 text-white">
            <div class="flex items-center gap-6 mb-8">
                <!-- Logo UKDW dengan efek glow halus -->
                <div class="bg-white p-3 rounded-2xl shadow-[0_0_30px_rgba(255,255,255,0.3)]">
                    <img src="/uploads/logo/logo-ukdw.png" alt="Logo UKDW" class="w-20 h-20 object-contain" onerror="this.src='https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png'" />
                </div>
            </div>
            
            <h1 class="text-5xl xl:text-6xl font-black mb-4 tracking-tight drop-shadow-lg">
                TRACER STUDY <span class="text-yellow-400">UKDW</span>
            </h1>
            
            <p class="text-green-50 text-lg leading-relaxed max-w-lg mb-12 drop-shadow-md">
                Sistem Informasi <span class="font-bold text-yellow-300">Penelusuran Alumni</span> untuk Mendukung Tercapainya Universitas Berkelas yang Berakar Kuat dan Menjulang Tinggi.
            </p>

        </div>

        <!-- Konten Kanan (Card Form Ubah Password) -->
        <div class="w-full max-w-md px-6 z-10 lg:mr-16 xl:mr-32">
            
            <!-- Card Putih Melayang -->
            <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] p-8 sm:p-10 shadow-2xl border border-white/50 relative overflow-hidden">
                
                <!-- Ornamen atas form -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#005B3C] to-yellow-400"></div>

                <div class="text-center mb-6 pt-2">
                    <h2 class="text-2xl font-black text-[#001D13] tracking-tight">UBAH KATA SANDI</h2>
                    <p class="text-gray-500 font-medium mt-2 text-sm leading-snug">Demi keamanan, silakan ubah kata sandi bawaan Anda sebelum melanjutkan.</p>
                </div>

                <!-- Pesan Error Global -->
                <div v-if="form.errors.password" class="mb-6 bg-red-50 text-red-600 text-sm p-4 rounded-2xl border border-red-100 flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ form.errors.password }}</span>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5 ml-1">Kata Sandi Baru</label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                class="block w-full pl-4 pr-4 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl text-gray-900 font-medium focus:ring-0 focus:border-[#005B3C] focus:bg-white transition-colors"
                                v-model="form.password"
                                required
                                autofocus
                                placeholder="Minimal 8 karakter"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5 ml-1">Konfirmasi Kata Sandi Baru</label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                type="password"
                                class="block w-full pl-4 pr-4 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl text-gray-900 font-medium focus:ring-0 focus:border-[#005B3C] focus:bg-white transition-colors"
                                v-model="form.password_confirmation"
                                required
                                placeholder="Ulangi kata sandi baru"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full mt-6 flex justify-center py-3.5 px-4 border border-transparent rounded-2xl shadow-lg text-sm font-bold text-[#001D13] bg-yellow-400 hover:bg-yellow-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition-all duration-300 hover:-translate-y-0.5"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    >
                        SIMPAN KATA SANDI
                    </button>
                </form>
            </div>
        </div>

    </div>
</template>
