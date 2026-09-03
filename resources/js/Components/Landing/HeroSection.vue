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
            
            <div class="max-w-3xl">
                <h1 class="text-white text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-2 uppercase drop-shadow-md">
                    PORTAL TRACER STUDY
                </h1>
                <h2 class="text-yellow-400 text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight mb-6 uppercase drop-shadow-lg">
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
            
        </div>

        <!-- Overlapping 3 Cards di Bottom -->
        <div class="absolute left-0 right-0 -bottom-24 lg:-bottom-28 z-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Card 1 -->
                    <div class="bg-slate-800 bg-opacity-90 backdrop-blur-md rounded-xl p-8 border-t-4 border-yellow-400 text-white shadow-2xl hover:-translate-y-2 transition-transform duration-300 group">
                        <h3 class="text-2xl font-bold mb-4 group-hover:text-yellow-400 transition-colors">Tracer Study</h3>
                        <p class="text-gray-300 mb-6 text-sm leading-relaxed">
                            Ditujukan kepada seluruh alumni UKDW dalam rentang waktu 1-2 tahun setelah lulus guna mengevaluasi proses pendidikan.
                        </p>
                        <a href="/login" class="inline-flex items-center text-yellow-400 font-semibold hover:text-yellow-300">
                            Selengkapnya 
                            <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-slate-800 bg-opacity-90 backdrop-blur-md rounded-xl p-8 border-t-4 border-green-500 text-white shadow-2xl hover:-translate-y-2 transition-transform duration-300 group">
                        <h3 class="text-2xl font-bold mb-4 group-hover:text-green-400 transition-colors">Survey Pengguna Lulusan</h3>
                        <p class="text-gray-300 mb-6 text-sm leading-relaxed">
                            Ditujukan kepada seluruh pengguna lulusan UKDW (perusahaan/instansi) dalam mengukur kualitas lulusan dari sudut pandang pengguna.
                        </p>
                        <a href="/login" class="inline-flex items-center text-green-400 font-semibold hover:text-green-300">
                            Selengkapnya 
                            <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-slate-800 bg-opacity-90 backdrop-blur-md rounded-xl p-8 border-t-4 border-blue-500 text-white shadow-2xl hover:-translate-y-2 transition-transform duration-300 group">
                        <h3 class="text-2xl font-bold mb-4 group-hover:text-blue-400 transition-colors">Laporan Tracer Study</h3>
                        <p class="text-gray-300 mb-6 text-sm leading-relaxed">
                            Laporan hasil Tracer Study dilaporkan setiap tahun dan disosialisasikan kepada seluruh pemangku kepentingan.
                        </p>
                        <a href="#alumni" class="inline-flex items-center text-blue-400 font-semibold hover:text-blue-300">
                            Selengkapnya 
                            <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</template>
