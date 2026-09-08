<!--
  Halaman Dashboard Resmi Admin Biro 3
  Fungsi: Portal kendali eksekutif resmi untuk Administrator Biro 3 (Kemahasiswaan, Alumni, dan Pengembangan Karir UKDW).
  Konsep: Formal, Bersih, dan Berwibawa dengan palet warna resmi UKDW: Hijau Tua (#005B3C) dan Kuning Landing Page (#FACC15 / yellow-400).
-->
<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    stats: Object,
    prodiSummaries: Array,
    recentAlumni: Array,
});

// Method proses logout Admin Biro 3 dan kembali ke halaman beranda (Home)
const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head title="Dashboard Utama - Biro 3 UKDW" />

    <div class="min-h-screen bg-slate-100 text-slate-800 flex flex-col font-sans">
        
        <!-- Header / Navbar Institusional Formal -->
        <header class="bg-[#004D32] border-b-4 border-yellow-400 text-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    
                    <!-- Identitas Kampus & Biro -->
                    <div class="flex items-center space-x-4">
                        <div class="bg-white p-1.5 rounded shadow-sm flex items-center justify-center">
                            <img src="/uploads/landing/2.png" alt="Logo UKDW" class="h-10 w-10 object-contain" onerror="this.style.display='none'" />
                        </div>
                        <div class="border-l border-emerald-600/60 pl-3">
                            <div class="text-[11px] uppercase tracking-widest text-emerald-200 font-semibold leading-tight">
                                Universitas Kristen Duta Wacana
                            </div>
                            <div class="text-base sm:text-lg font-bold tracking-tight text-white leading-tight">
                                Biro Kemahasiswaan, Alumni & Pengembangan Karir (Biro III)
                            </div>
                        </div>
                    </div>

                    <!-- Navigasi Menu & Akses Cepat -->
                    <div class="hidden md:flex items-center space-x-1">
                        <Link 
                            href="/biro3/dashboard" 
                            class="px-3.5 py-2 text-xs uppercase tracking-wider font-bold rounded bg-[#003824] text-yellow-400 border-b-2 border-yellow-400 transition-all"
                        >
                            Dashboard
                        </Link>
                        <Link 
                            href="/biro3/alumni" 
                            class="px-3.5 py-2 text-xs uppercase tracking-wider font-medium text-emerald-100 hover:text-white hover:bg-[#003824] rounded transition-all"
                        >
                            Data Alumni
                        </Link>
                    </div>

                    <!-- Profil Administrator & Logout -->
                    <div class="flex items-center space-x-3">
                        <div class="hidden sm:block text-right">
                            <div class="text-xs font-bold text-white">{{ user?.name || 'Administrator Biro 3' }}</div>
                            <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold bg-yellow-400 text-green-950 rounded uppercase tracking-wider">
                                Hak Akses Biro III
                            </span>
                        </div>
                        <button 
                            @click="logout" 
                            class="px-3.5 py-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold uppercase tracking-wider rounded border border-white/20 shadow-sm transition-colors flex items-center gap-1.5"
                            title="Keluar dari sesi sistem"
                        >
                            <span>Keluar</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subnav Mobile -->
            <div class="md:hidden bg-[#003B26] px-4 py-2 border-t border-emerald-700/50 flex space-x-3">
                <Link href="/biro3/dashboard" class="text-xs font-bold text-yellow-400">Dashboard</Link>
                <Link href="/biro3/alumni" class="text-xs font-medium text-emerald-200">Data Alumni</Link>
            </div>
        </header>

        <!-- Main Body Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <!-- Banner Utama / Title Card Formal -->
            <div class="bg-white border-l-4 border-[#005B3C] border-y border-r border-slate-200 rounded shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-[#005B3C] border border-emerald-200 uppercase tracking-wider">
                            Pusat Kendali Administrasi
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-yellow-100 text-yellow-900 border border-yellow-200 uppercase tracking-wider">
                            Tahun Operasional Aktif
                        </span>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">
                        Dashboard Utama & Pemantauan Tracer Study
                    </h1>
                    <p class="text-sm text-slate-600 mt-1 max-w-3xl">
                        Selamat datang di panel administrasi Biro III. Gunakan portal ini untuk memantau indikator capaian pelacakan lulusan, memverifikasi basis data alumni, serta mengonfigurasi instrumen kuesioner resmi universitas.
                    </p>
                </div>
                <div class="flex md:flex-col items-end justify-between md:justify-center gap-2 border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-6 shrink-0">
                    <div class="text-right">
                        <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Status Sistem</div>
                        <div class="text-xs font-bold text-emerald-700 flex items-center gap-1.5 justify-end mt-0.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-emerald-200"></span>
                            Sinkronisasi Aktif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Otoritas</div>
                        <div class="text-xs font-bold text-slate-800">Admin Biro 3 UKDW</div>
                    </div>
                </div>
            </div>

            <!-- KPI Summary Cards Formal (4 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- KPI 1: Total Alumni -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-[#005B3C] p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Total Basis Data Alumni</span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats.total_alumni }}
                    </div>
                    <div class="mt-2 text-xs text-slate-600 flex items-center justify-between pt-2 border-t border-slate-100">
                        <span>LinkedIn Terdata:</span>
                        <span class="font-bold text-[#005B3C]">{{ stats.alumni_linkedin }} Alumni</span>
                    </div>
                </div>

                <!-- KPI 2: Responden Kuesioner -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-yellow-400 p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Partisipasi Kuesioner</span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats.total_responden }}
                    </div>
                    <div class="mt-2 text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <div class="flex justify-between font-semibold mb-1">
                            <span>Tingkat Respons:</span>
                            <span class="text-green-800 font-bold">{{ stats.persentase_respon }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-yellow-400 h-1.5 rounded-full" :style="{ width: `${Math.min(stats.persentase_respon, 100)}%` }"></div>
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Butir Pertanyaan -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-[#005B3C] p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Instrumen Pertanyaan</span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats.total_pertanyaan }}
                    </div>
                    <div class="mt-2 text-xs text-slate-600 flex items-center justify-between pt-2 border-t border-slate-100">
                        <span>Format Soal:</span>
                        <span class="font-bold text-[#005B3C]">F1 s/d F22 & Jump Logic</span>
                    </div>
                </div>

                <!-- KPI 4: Total Program Studi -->
                <div class="bg-white border border-slate-200 rounded shadow-sm border-t-4 border-t-yellow-400 p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Cakupan Program Studi</span>
                    </div>
                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                        {{ stats.total_prodi }}
                    </div>
                    <div class="mt-2 text-xs text-slate-600 flex items-center justify-between pt-2 border-t border-slate-100">
                        <span>Integrasi:</span>
                        <span class="font-bold text-[#005B3C]">Seluruh Fakultas UKDW</span>
                    </div>
                </div>
            </div>

            <!-- Dua Kartu Gerbang Navigasi Utama (Tegas & Formal) -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-black uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 bg-[#005B3C] inline-block"></span>
                        Modul Pengelolaan Utama Biro III
                    </h2>
                    <span class="text-xs text-slate-500">Pilih modul operasional di bawah untuk memulai pekerjaan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Modul 1: Kelola Data Alumni -->
                    <div class="bg-white border border-slate-200 hover:border-[#005B3C] rounded shadow-sm transition-all duration-200 flex flex-col justify-between">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <span class="px-2.5 py-1 bg-emerald-50 text-[#005B3C] text-[11px] font-bold uppercase tracking-wider rounded border border-emerald-200">
                                    MODUL 01 — ALUMNI
                                </span>
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 tracking-tight mb-2">
                                Manajemen Data Alumni & Sinkronisasi LinkedIn
                            </h3>
                            
                            <p class="text-sm text-slate-600 leading-relaxed mb-6">
                                Direktori terpusat data lulusan universitas. Dilengkapi fitur pencarian komprehensif (NIM, Nama, Prodi), verifikasi status kontak, serta modul sinkronisasi otomatis riwayat posisi kerja mutakhir melalui akun LinkedIn alumni.
                            </p>

                            <div class="bg-slate-50 border border-slate-200 rounded p-3 mb-2 space-y-1.5 text-xs text-slate-700">
                                <div class="flex items-center gap-2 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#005B3C]"></span>
                                    <span>Pencarian cepat berdasar NIM / Nama / Program Studi</span>
                                </div>
                                <div class="flex items-center gap-2 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#005B3C]"></span>
                                    <span>Sinkronisasi data karir & perusahaan via LinkedIn API/Scraper</span>
                                </div>
                                <div class="flex items-center gap-2 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#005B3C]"></span>
                                    <span>Rincian kelengkapan profil dan rekam jejak lulusan</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <Link 
                                href="/biro3/alumni" 
                                class="w-full inline-flex items-center justify-center px-5 py-3 bg-[#005B3C] hover:bg-[#00432c] text-white text-xs uppercase tracking-wider font-bold rounded shadow transition-colors"
                            >
                                <span>Buka Manajemen Alumni</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Modul 2: Pemantauan Responden & Distribusi Partisipasi -->
                    <div class="bg-white border border-slate-200 hover:border-yellow-400 rounded shadow-sm transition-all duration-200 flex flex-col justify-between">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <span class="px-2.5 py-1 bg-yellow-50 text-yellow-900 text-[11px] font-bold uppercase tracking-wider rounded border border-yellow-300">
                                    MODUL 02 — PEMANTAUAN
                                </span>
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 tracking-tight mb-2">
                                Pemantauan Partisipasi & Capaian Pelacakan Lulusan
                            </h3>
                            
                            <p class="text-sm text-slate-600 leading-relaxed mb-6">
                                Pantau distribusi pengisian kuesioner pada setiap program studi secara real-time. Ketahui persentase respon lulusan, evaluasi data serapan kerja, dan percepat capaian target keterisian tracer study universitas.
                            </p>

                            <div class="bg-slate-50 border border-slate-200 rounded p-3 mb-2 space-y-1.5 text-xs text-slate-700">
                                <div class="flex items-center gap-2 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#005B3C]"></span>
                                    <span>Pemantauan respons per prodi & fakultas secara real-time</span>
                                </div>
                                <div class="flex items-center gap-2 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#005B3C]"></span>
                                    <span>Status kelengkapan pengisian kuesioner F1 s/d F22</span>
                                </div>
                                <div class="flex items-center gap-2 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#005B3C]"></span>
                                    <span>Pengaturan butir kuesioner kini dikelola terpusat oleh Superadmin</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 pt-0">
                            <Link 
                                href="/biro3/alumni" 
                                class="w-full inline-flex items-center justify-center px-5 py-3 bg-[#005B3C] hover:bg-[#00432c] text-white text-xs uppercase tracking-wider font-bold rounded shadow transition-colors"
                            >
                                <span>Lihat Distribusi Responden</span>
                            </Link>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Tabel Pemantauan Partisipasi per Program Studi -->
            <div class="bg-white border border-slate-200 rounded shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-800">
                            Distribusi & Partisipasi Responden per Program Studi
                        </h3>
                        <p class="text-xs text-slate-500">
                            Laporan rekapitulasi data kelulusan dan respon kuesioner pada seluruh program studi aktif.
                        </p>
                    </div>
                    <Link 
                        href="/biro3/alumni" 
                        class="text-xs font-bold text-[#005B3C] hover:text-[#00432c] inline-flex items-center gap-1"
                    >
                        <span>Lihat Semua Alumni</span>
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                        <thead class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 w-20">Kode</th>
                                <th class="px-6 py-3">Nama Program Studi</th>
                                <th class="px-6 py-3 text-center">Total Alumni</th>
                                <th class="px-6 py-3 text-center">Responden Selesai</th>
                                <th class="px-6 py-3 text-left w-52">Tingkat Partisipasi</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="prodi in prodiSummaries" :key="prodi.id" class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-3.5 font-bold font-mono text-slate-800">
                                    {{ prodi.kode_prodi }}
                                </td>
                                <td class="px-6 py-3.5 font-semibold text-slate-900">
                                    {{ prodi.nama_prodi }}
                                </td>
                                <td class="px-6 py-3.5 text-center font-bold text-slate-800">
                                    {{ prodi.total_alumni }}
                                </td>
                                <td class="px-6 py-3.5 text-center font-bold text-[#005B3C]">
                                    {{ prodi.total_responden }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-200 rounded-full h-2 overflow-hidden">
                                            <div 
                                                class="h-2 rounded-full transition-all duration-500" 
                                                :class="prodi.response_rate >= 50 ? 'bg-[#005B3C]' : 'bg-yellow-400'"
                                                :style="{ width: `${Math.min(prodi.response_rate, 100)}%` }"
                                            ></div>
                                        </div>
                                        <span class="w-10 text-right font-bold text-slate-700">{{ prodi.response_rate }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span 
                                        v-if="prodi.total_alumni === 0" 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200"
                                    >
                                        Belum Ada Data
                                    </span>
                                    <span 
                                        v-else-if="prodi.response_rate >= 70" 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300"
                                    >
                                        Sangat Baik
                                    </span>
                                    <span 
                                        v-else-if="prodi.response_rate >= 30" 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-900 border border-yellow-300"
                                    >
                                        Cukup
                                    </span>
                                    <span 
                                        v-else 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300"
                                    >
                                        Perlu Tindak Lanjut
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <!-- Footer Institusional Resmi -->
        <footer class="bg-slate-900 text-slate-400 text-xs py-6 border-t-2 border-yellow-400 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-white">Tracer Study UKDW</span>
                    <span>&mdash;</span>
                    <span>Biro Kemahasiswaan, Alumni dan Pengembangan Karir (Biro III)</span>
                </div>
                <div class="text-slate-500 text-[11px]">
                    &copy; {{ new Date().getFullYear() }} Universitas Kristen Duta Wacana. Hak Cipta Dilindungi.
                </div>
            </div>
        </footer>

    </div>
</template>
