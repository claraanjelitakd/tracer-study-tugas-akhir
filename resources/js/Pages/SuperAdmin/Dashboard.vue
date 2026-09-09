<!--
  Halaman Dashboard Utama Superadmin
  
  Fungsi:
  Menampilkan ringkasan status kuesioner tracer study dan metrik sistem
  dengan desain yang bersih, minimalis, dan profesional (mengikuti estetika modul Alumni).
-->
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Pages/SuperAdmin/Components/Navbar.vue';

// Properti yang dikirimkan oleh SuperAdmin\DashboardController
const props = defineProps({
    user: Object,
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard Super Admin - Tracer Study UKDW" />

    <div class="min-h-screen bg-[#f8fafc] text-gray-800 flex flex-col font-sans pb-24">
        
        <!-- Header / Navbar Terpadu -->
        <Navbar :user="user" />

        <!-- Header Profil Style (Elegan & Muted) -->
        <header class="bg-gradient-to-r from-[#005B3C] to-[#007b55] pt-12 pb-24 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Dashboard Super Admin
                </h1>
                <p class="text-green-100 font-medium mt-2 max-w-2xl text-sm sm:text-base leading-relaxed">
                    Ringkasan instrumen kuesioner, bagian pertanyaan, serta pemantauan data alumni Tracer Study UKDW.
                </p>
            </div>
        </header>

        <!-- Main Body Area -->
        <main class="w-full max-w-[1400px] mx-auto -mt-16 px-4 sm:px-6 lg:px-8 relative z-20 space-y-8">
            
            <!-- Grid 4 Kartu KPI Metrik -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- KPI 1: Total Pertanyaan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Total Pertanyaan
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-[#005B3C] flex items-center justify-center font-bold text-xs">
                            #
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 tracking-tight mt-3">
                        {{ stats?.total_pertanyaan || 0 }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Butir instrumen aktif
                    </p>
                </div>

                <!-- KPI 2: Total Bagian (Sections) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Bagian (Section)
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">
                            §
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 tracking-tight mt-3">
                        {{ stats?.total_sections || 0 }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Tahapan kuesioner
                    </p>
                </div>

                <!-- KPI 3: Basis Data Alumni -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Alumni Terdaftar
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-xs">
                            👤
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 tracking-tight mt-3">
                        {{ stats?.total_alumni || 0 }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Dari {{ stats?.total_prodi || 0 }} Program Studi
                    </p>
                </div>

                <!-- KPI 4: Partisipasi Respon -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Responden Alumni
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs">
                            ✓
                        </div>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 tracking-tight mt-3">
                        {{ stats?.total_responden || 0 }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Mengisi kuesioner
                    </p>
                </div>

            </div>

            <!-- Modul Pengelolaan Kuesioner (Gaya Card Modern Alumni) -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-md transition-all">
                <div class="max-w-2xl space-y-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-[#005B3C] border border-emerald-200/80 uppercase tracking-wider">
                        Instrumen Kuesioner
                    </span>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                        Pengelolaan Pertanyaan & Alur Percabangan
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Kelola seluruh butir pertanyaan Tracer Study, susun pilihan opsi jawaban, atur alur lompatan pertanyaan (*jump logic*), serta tentukan instrumen khusus per program studi.
                    </p>
                </div>

                <div class="shrink-0">
                    <Link 
                        href="/superadmin/pertanyaan" 
                        class="inline-flex items-center justify-center px-6 py-3.5 bg-gray-900 hover:bg-[#005B3C] text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition-all gap-2 cursor-pointer active:scale-98"
                    >
                        <span>Kelola Kuesioner</span>
                        <span>&rarr;</span>
                    </Link>
                </div>
            </div>

        </main>

        <!-- Footer Bersih & Minimal -->
        <footer class="text-gray-400 text-xs text-center mt-12 py-6 border-t border-gray-100">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                &copy; {{ new Date().getFullYear() }} Universitas Kristen Duta Wacana. Hak Cipta Dilindungi.
            </div>
        </footer>

    </div>
</template>
