<!--
  Halaman Login Otentikasi (Desain ala SIMASTER UGM)
  Fungsi: Menampilkan antarmuka untuk masuk ke dalam sistem Tracer Study.
-->
<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';

// Form state untuk menangani login (username untuk nim alumni)
const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk - Tracer Study" />

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

        <!-- Konten Kanan (Card Form Login) -->
        <div class="w-full max-w-md px-6 z-10 lg:mr-16 xl:mr-32">
            
            <!-- Card Putih Melayang -->
            <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] p-8 sm:p-10 shadow-2xl border border-white/50 relative overflow-hidden">
                
                <!-- Ornamen atas form -->
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-[#005B3C] to-yellow-400"></div>

                <div class="text-center mb-8 pt-2">
                    <!-- Logo mobile saja -->
                    <img src="/uploads/logo/logo-ukdw.png" alt="Logo UKDW" class="w-16 h-16 mx-auto mb-4 lg:hidden" onerror="this.src='https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png'" />
                    <h2 class="text-3xl font-black text-[#001D13] tracking-tight">SIGN IN</h2>
                    <p class="text-gray-500 font-medium mt-1">Akun Tracer Study Anda</p>
                </div>

                <!-- Pesan Error Global -->
                <div v-if="form.errors.username" class="mb-6 bg-red-50 text-red-600 text-sm p-4 rounded-2xl border border-red-100 flex items-start gap-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ form.errors.username }}</span>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    
                    <div>
                        <label for="username" class="block text-sm font-bold text-gray-700 mb-1.5 ml-1">Username / NIM Alumni</label>
                        <div class="relative">
                            <input
                                id="username"
                                type="text"
                                v-model="form.username"
                                required
                                autofocus
                                class="block w-full pl-4 pr-4 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl text-gray-900 font-medium focus:ring-0 focus:border-[#005B3C] focus:bg-white transition-colors"
                                placeholder="Masukkan NIM atau Username"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5 ml-1">Kata Sandi</label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                class="block w-full pl-4 pr-4 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl text-gray-900 font-medium focus:ring-0 focus:border-[#005B3C] focus:bg-white transition-colors"
                                placeholder="••••••••"
                            />
                        </div>
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ form.errors.password }}</div>
                    </div>

                    <div class="flex items-center justify-between pt-1 pb-2">
                        <label class="flex items-center cursor-pointer group">
                            <input 
                                type="checkbox" 
                                v-model="form.remember" 
                                class="w-5 h-5 text-[#005B3C] border-2 border-gray-200 rounded-lg focus:ring-[#005B3C] focus:ring-offset-0 transition-colors cursor-pointer group-hover:border-[#005B3C]" 
                            />
                            <span class="ml-2.5 text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Ingat Saya</span>
                        </label>
                    </div>

                    <div>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-sm text-base font-bold text-white bg-[#0077b5] hover:bg-[#005c8a] focus:outline-none focus:ring-4 focus:ring-[#0077b5]/30 transition-all hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Masuk dengan Akun Sistem
                            </span>
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="relative mt-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white text-gray-500 font-medium">Atau</span>
                        </div>
                    </div>

                    <!-- Link Lupa Sandi -->
                    <div class="mt-6 text-center">
                        <Link href="#" class="inline-block w-full py-3.5 px-4 bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold rounded-2xl transition-colors border border-gray-200">
                            Lupa Kata Sandi
                        </Link>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
<style scoped>
/* Animasi ringan untuk popup */
.animate-bounce-short {
    animation: bounce-short 0.25s ease-out 1;
}
@keyframes bounce-short {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
