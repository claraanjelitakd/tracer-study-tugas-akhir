<!--
  Halaman Dashboard Utama Superadmin
  
  Fungsi:
  Portal kendali pusat bagi Superadmin. Menyajikan status ringkasan sistem,
  metrik instrumen kuesioner tracer study, serta akses cepat ke manajemen
  percabangan dan butir pertanyaan kuesioner.
  
  Warna:
  Hijau Resmi UKDW (#005B3C) dan Kuning Landing Page (#FACC15 / yellow-400).
-->
<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

// Properti yang dikirimkan oleh SuperAdmin\DashboardController
const props = defineProps({
    user: Object,
    stats: Object,
});

// Method untuk melakukan proses logout sesi dan kembali ke halaman beranda (Home)
const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head title="Dashboard Super Admin - Tracer Study UKDW" />

    <div class="min-h-screen bg-slate-100 text-slate-800 flex flex-col font-sans">
        
        <!-- Header / Navbar Resmi Superadmin -->
        <header class="bg-[#004D32] border-b-4 border-yellow-400 text-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    
                    <!-- Identitas Kampus & Superadmin -->
                    <div class="flex items-center space-x-4">
                        <div class="bg-white p-1.5 rounded shadow-sm flex items-center justify-center">
                            <img src="/uploads/landing/2.png" alt="Logo UKDW" class="h-10 w-10 object-contain" onerror="this.style.display='none'" />
                        </div>
                        <div class="border-l border-emerald-600/60 pl-3">
                            <div class="text-[11px] uppercase tracking-widest text-emerald-200 font-semibold leading-tight">
                                Universitas Kristen Duta Wacana
                            </div>
                            <div class="text-base sm:text-lg font-bold tracking-tight text-white leading-tight">
                                Pusat Kendali Super Admin
                            </div>
                        </div>
                    </div>

                    <!-- Navigasi Menu Atas -->
                    <div class="hidden md:flex items-center space-x-1">
                        <Link 
                            href="/superadmin/dashboard" 
                            class="px-3.5 py-2 text-xs uppercase tracking-wider font-bold rounded bg-[#003824] text-yellow-400 border-b-2 border-yellow-400 transition-all"
                        >
                            Dashboard
                        </Link>
                        <Link 
                            href="/superadmin/pertanyaan" 
                            class="px-3.5 py-2 text-xs uppercase tracking-wider font-medium text-emerald-100 hover:text-white hover:bg-[#003824] rounded transition-all"
                        >
                            Kelola Kuesioner
                        </Link>
                    </div>

                    <!-- User Info & Logout -->
                    <div class="flex items-center space-x-3">
                        <div class="hidden sm:block text-right">
                            <div class="text-xs font-bold text-white">{{ user?.name || 'Super Administrator' }}</div>
                            <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold bg-yellow-400 text-green-950 rounded uppercase tracking-wider">
                                Hak Akses Penuh
                            </span>
                        </div>
                        <button 
                            @click="logout" 
                            class="px-3.5 py-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold uppercase tracking-wider rounded border border-white/20 shadow-sm transition-colors"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subnav Mobile -->
            <div class="md:hidden bg-[#003B26] px-4 py-2 border-t border-emerald-700/50 flex space-x-3">
                <Link href="/superadmin/dashboard" class="text-xs font-bold text-yellow-400">Dashboard</Link>
                <Link href="/superadmin/pertanyaan" class="text-xs font-medium text-emerald-200">Kelola Kuesioner</Link>
            </div>
        </header>

        <!-- Main Body Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            <!-- Banner Utama / Title Card -->
            <div class="bg-white border-l-4 border-[#005B3C] border-y border-r border-slate-200 rounded shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-[#005B3C] border border-emerald-200 uppercase tracking-wider">
                            Pusat Otoritas Tertinggi
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-yellow-100 text-yellow-900 border border-yellow-200 uppercase tracking-wider">
                            Instrumen Aktif
                        </span>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">
                        Dasbor Administrasi Super Administrator
                    </h1>
                    <p class="text-sm text-slate-600 mt-1 max-w-3xl">
                        Selamat datang di portal Super Admin. Anda memiliki wewenang penuh dalam mengonfigurasi seluruh instrumen butir pertanyaan Tracer Study, alur logika percabangan (*jump logic*), serta pemantauan ekosistem sistem.
                    </p>
                </div>
                <div class="border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-6 text-right shrink-0">
                    <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Peran Akun</div>
                    <div class="text-xs font-bold text-[#005B3C] mt-0.5">Super Administrator</div>
                </div>
            </div>

            <!-- KPI Ringkasan Metrik (4 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- KPI 1: Total Pertanyaan -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-[#005B3C] p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Total Butir Pertanyaan
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats?.total_pertanyaan || 0 }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500 pt-2 border-t border-slate-100 flex justify-between">
                        <span>Format Soal:</span>
                        <span class="font-bold text-[#005B3C]">F1 s/d F22 & Jump Logic</span>
                    </div>
                </div>

                <!-- KPI 2: Total Bagian (Sections) -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-yellow-400 p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Bagian / Section Kuesioner
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats?.total_sections || 0 }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500 pt-2 border-t border-slate-100 flex justify-between">
                        <span>Pembagian:</span>
                        <span class="font-bold text-[#005B3C]">Hierarkis Terstruktur</span>
                    </div>
                </div>

                <!-- KPI 3: Basis Data Alumni -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-[#005B3C] p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Basis Data Alumni
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats?.total_alumni || 0 }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500 pt-2 border-t border-slate-100 flex justify-between">
                        <span>Terdaftar di Sistem</span>
                        <span class="font-bold text-[#005B3C]">{{ stats?.total_prodi || 0 }} Prodi</span>
                    </div>
                </div>

                <!-- KPI 4: Partisipasi Respon -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-yellow-400 p-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Partisipasi Responden
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats?.total_responden || 0 }}
                    </div>
                    <div class="mt-2 text-xs text-slate-500 pt-2 border-t border-slate-100 flex justify-between">
                        <span>Alumni Menjawab:</span>
                        <span class="font-bold text-[#005B3C]">{{ stats?.total_responden || 0 }} Orang</span>
                    </div>
                </div>

            </div>

            <!-- Gerbang Pengelolaan Modul Utama -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-[#005B3C] inline-block"></span>
                        Modul Pengelolaan Kuesioner (Superadmin)
                    </h2>
                </div>

                <div class="bg-white border border-slate-200 hover:border-[#005B3C] rounded shadow-sm p-6 transition-all duration-200 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="max-w-3xl">
                        <div class="mb-2">
                            <span class="px-2.5 py-1 bg-yellow-50 text-yellow-900 text-[11px] font-bold uppercase tracking-wider rounded border border-yellow-300">
                                INSTRUMEN KUESIONER TRACER STUDY
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 tracking-tight mb-2">
                            Konfigurasi Butir Pertanyaan, Pilihan Jawaban, & Alur Percabangan
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Kelola seluruh butir pertanyaan Tracer Study universitas. Buat dan sesuaikan pertanyaan standar Dikti, tambahkan pilihan opsi ganda/matriks, atur alur lompatan pertanyaan (*Google Forms Jump Logic*), serta targetkan instrumen khusus per program studi.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <Link 
                            href="/superadmin/pertanyaan" 
                            class="inline-flex items-center justify-center px-6 py-3.5 bg-[#005B3C] hover:bg-[#00422c] text-white text-xs uppercase tracking-wider font-bold rounded shadow hover:shadow-md transition-all gap-2"
                        >
                            <span>Kelola Kuesioner Sekarang &rarr;</span>
                        </Link>
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer Superadmin -->
        <footer class="bg-slate-900 text-slate-400 text-xs py-6 border-t-2 border-yellow-400 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-white">Tracer Study UKDW</span>
                    <span>&mdash;</span>
                    <span>Pusat Kendali Super Administrator</span>
                </div>
                <div class="text-slate-500 text-[11px]">
                    &copy; {{ new Date().getFullYear() }} Universitas Kristen Duta Wacana. Hak Cipta Dilindungi.
                </div>
            </div>
        </footer>

    </div>
</template>
