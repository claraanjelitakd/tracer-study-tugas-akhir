<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const searchQuery = ref('');

const handleSearch = () => {
    // Fungsi search dummy untuk saat ini
    console.log('Mencari:', searchQuery.value);
};
</script>

<template>
    <section class="relative bg-gray-900 pt-24 pb-32 lg:pt-32 lg:pb-48 overflow-visible">
        
        <!-- Video Background Overlay -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <!-- Gunakan URL video placeholder -->
            <video 
                autoplay 
                loop 
                muted 
                playsinline
                class="absolute min-w-full min-h-full object-cover opacity-40 mix-blend-overlay"
            >
                <source src="https://cdn.pixabay.com/video/2020/05/11/38605-419082710_large.mp4" type="video/mp4" />
            </video>
            <!-- Overlay Hijau Gelap -->
            <div class="absolute inset-0 bg-green-900 bg-opacity-70"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 h-full flex flex-col justify-center">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Bagian Teks (Kiri) -->
                <div class="max-w-3xl">
                    <h1 class="hero-title text-white text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 uppercase drop-shadow-md">
                        PORTAL TRACER STUDY
                    </h1>
                    <h2 class="hero-subtitle text-yellow-400 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mb-6 uppercase drop-shadow-lg">
                        UNIVERSITAS KRISTEN DUTA WACANA
                    </h2>
                    
                    <p class="text-green-50 text-lg sm:text-xl font-medium mb-8">
                        Website ini berisi informasi pelaksanaan Tracer Study di Universitas Kristen Duta Wacana
                    </p>

                    <!-- Search Bar -->
                    <form @submit.prevent="handleSearch" class="flex w-full max-w-xl shadow-lg rounded-lg overflow-hidden transition-transform focus-within:scale-105 duration-300">
                        <input 
                            type="text" 
                            v-model="searchQuery"
                            placeholder="Cari apapun disini..." 
                            class="w-full px-6 py-4 text-gray-900 bg-white focus:outline-none placeholder-gray-400 font-medium"
                        />
                        <button 
                            type="submit" 
                            class="px-8 bg-yellow-400 hover:bg-yellow-500 text-green-900 transition-colors flex items-center justify-center focus:outline-none"
                        >
                            <svg class="w-6 h-6 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <!-- Bagian Gambar Pigo (Kanan) -->
                <div class="relative hidden lg:flex justify-center items-center h-full min-h-[400px]">
                    <!-- Gambar 1 (Default) -->
                    <img 
                        src="/uploads/landing/1.png" 
                        alt="Pigo 1" 
                        class="pigo-img-1 absolute w-full max-w-[350px] object-contain filter drop-shadow-2xl" 
                    />
                    <!-- Gambar 2 (Muncul saat scroll) -->
                    <img 
                        src="/uploads/landing/2.png" 
                        alt="Pigo 2" 
                        class="pigo-img-2 absolute w-full max-w-[350px] object-contain filter drop-shadow-2xl opacity-0" 
                    />
                </div>
            </div>
            
        </div>

        <!-- Overlapping 3 Cards di Bottom -->
        <div class="absolute left-0 right-0 -bottom-24 lg:-bottom-28 z-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Card 1 -->
                    <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 text-white shadow-[0_8px_32px_0_rgba(31,38,135,0.37)] hover:shadow-[0_8px_32px_0_rgba(250,204,21,0.4)] hover:-translate-y-3 transition-all duration-500 group overflow-hidden">
                        <!-- Decorative gradient blob -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-400/30 rounded-full blur-3xl group-hover:bg-yellow-400/50 transition-colors duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-2xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 to-yellow-500">Tracer Study</h3>
                            <p class="text-gray-200 mb-8 text-sm leading-relaxed">
                                Ditujukan kepada seluruh alumni UKDW dalam rentang waktu 1-2 tahun setelah lulus guna mengevaluasi proses pendidikan.
                            </p>
                            <!-- bukannya harusnya ke login ya? terus kalau href ke alumni kuesioner gimana alumni yang belum login -->
                            
                            <a href="/alumni/kuesioner" class="inline-flex items-center justify-center w-full py-3 px-4 rounded-xl bg-yellow-400/10 text-yellow-400 font-semibold border border-yellow-400/30 hover:bg-yellow-400 hover:text-gray-900 transition-all duration-300"> 
                                Selengkapnya 
                                <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 text-white shadow-[0_8px_32px_0_rgba(31,38,135,0.37)] hover:shadow-[0_8px_32px_0_rgba(34,197,94,0.4)] hover:-translate-y-3 transition-all duration-500 group overflow-hidden">
                        <!-- Decorative gradient blob -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-green-500/30 rounded-full blur-3xl group-hover:bg-green-500/50 transition-colors duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-2xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-green-300 to-green-500">Survey Pengguna Lulusan</h3>
                            <p class="text-gray-200 mb-8 text-sm leading-relaxed">
                                Ditujukan kepada seluruh pengguna lulusan UKDW (perusahaan/instansi) dalam mengukur kualitas lulusan dari sudut pandang pengguna.
                            </p>
                            <a href="/login" class="inline-flex items-center justify-center w-full py-3 px-4 rounded-xl bg-green-500/10 text-green-400 font-semibold border border-green-500/30 hover:bg-green-500 hover:text-white transition-all duration-300">
                                Selengkapnya 
                                <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="relative bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 text-white shadow-[0_8px_32px_0_rgba(31,38,135,0.37)] hover:shadow-[0_8px_32px_0_rgba(59,130,246,0.4)] hover:-translate-y-3 transition-all duration-500 group overflow-hidden">
                        <!-- Decorative gradient blob -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/30 rounded-full blur-3xl group-hover:bg-blue-500/50 transition-colors duration-500"></div>
                        <div class="relative z-10">
                            <h3 class="text-2xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-300 to-blue-500">Laporan Tracer Study</h3>
                            <p class="text-gray-200 mb-8 text-sm leading-relaxed">
                                Laporan hasil Tracer Study dilaporkan setiap tahun dan disosialisasikan kepada seluruh pemangku kepentingan.
                            </p>
                            <a href="#alumni" class="inline-flex items-center justify-center w-full py-3 px-4 rounded-xl bg-blue-500/10 text-blue-400 font-semibold border border-blue-500/30 hover:bg-blue-500 hover:text-white transition-all duration-300">
                                Selengkapnya 
                                <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</template>
