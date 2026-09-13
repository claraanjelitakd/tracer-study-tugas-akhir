<!--
  Halaman Direktori Mahasiswa & Alumni Program Studi
  File: resources/js/Pages/AdminProdi/Alumni/Index.vue
  
  Warna Resmi Solid UKDW (Mirip dengan Super Admin):
  - Hijau: #0D542B (Solid, tanpa gradasi berlebih)
  - Kuning: #FDC700 (Kuning UKDW murni)
  - Putih: #FFFFFF
  Desain profesional, bersih, elegan, bebas border berlebih dan bebas efek hover border.
-->
<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Navbar from '../Components/Navbar.vue';

const props = defineProps({
    user: Object,
    prodi: Object,
    alumnis: {
        type: Array,
        default: () => [],
    },
    daftarTahun: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    stats: {
        type: Object,
        default: () => ({
            total_alumni: 0,
            total_filtered: 0,
            univ_selesai: 0,
            univ_belum: 0,
            prodi_selesai: 0,
            prodi_belum: 0,
        }),
    },
});

// State Filter
const search = ref(props.filters.search || '');
const tahun = ref(props.filters.tahun || 'all');
const semester = ref(props.filters.semester || 'all');
const statusUniv = ref(props.filters.status_univ || 'all');
const statusProdi = ref(props.filters.status_prodi || 'all');

// Pagination lokal ala DataTables
const perPage = ref(10);
const currentPage = ref(1);

const totalPages = computed(() => {
    return Math.ceil(props.alumnis.length / perPage.value) || 1;
});

const paginatedAlumnis = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    return props.alumnis.slice(start, start + perPage.value);
});

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

// Hitung persentase keterisian prodi
const persentaseProdiSelesai = computed(() => {
    if (!props.stats.total_alumni) return 0;
    return Math.round((props.stats.prodi_selesai / props.stats.total_alumni) * 100);
});

// Terapkan Filter ke URL
let searchTimeout = null;
const applyFilters = () => {
    currentPage.value = 1;
    router.get('/prodi/alumni', {
        search: search.value || undefined,
        tahun: tahun.value !== 'all' ? tahun.value : undefined,
        semester: semester.value !== 'all' ? semester.value : undefined,
        status_univ: statusUniv.value !== 'all' ? statusUniv.value : undefined,
        status_prodi: statusProdi.value !== 'all' ? statusProdi.value : undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const handleSearchInput = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 400);
};

const resetFilters = () => {
    search.value = '';
    tahun.value = 'all';
    semester.value = 'all';
    statusUniv.value = 'all';
    statusProdi.value = 'all';
    currentPage.value = 1;
    router.get('/prodi/alumni', {}, { preserveState: true });
};
</script>

<template>
    <Head :title="`Direktori Alumni - ${prodi?.nama_prodi || 'Program Studi'}`" />

    <div class="min-h-screen bg-[#f8fafc] text-gray-800 font-sans pb-24">
        <!-- Navbar Terpadu Admin Prodi -->
        <Navbar :user="user" :prodi="prodi" />

        <!-- Header Solid Hijau Resmi UKDW #0D542B -->
        <header class="bg-[#0D542B] text-white pt-10 pb-20">
            <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 text-xs text-white/80 font-medium mb-2">
                        <Link href="/prodi/dashboard" class="hover:underline">Dashboard</Link>
                        <span>/</span>
                        <span class="text-white font-bold">Data Alumni</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Daftar Mahasiswa & Hasil Tracer
                    </h1>
                    <p class="text-white/90 text-sm sm:text-base font-normal mt-1 max-w-2xl leading-relaxed">
                        Direktori data alumni {{ prodi?.nama_prodi }} dan audit kelengkapan kuesioner tracer study universitas serta program studi.
                    </p>
                </div>

                <!-- Ringkasan Cepat di Header -->
                <div class="bg-black/15 border border-white/20 px-6 py-4 rounded-2xl text-left md:text-right text-white">
                    <span class="text-xs text-white/80 font-bold uppercase tracking-wider block">Partisipasi Kuesioner Prodi</span>
                    <span class="text-3xl font-black text-[#FDC700] block">{{ persentaseProdiSelesai }}%</span>
                    <span class="text-xs text-white/90 font-medium">{{ stats.prodi_selesai }} dari {{ stats.total_alumni }} Alumni Selesai</span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="w-full max-w-[1400px] mx-auto -mt-10 px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- 4 Kartu Statistik Ringkas & Profesional -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Mahasiswa</p>
                    <p class="text-3xl font-extrabold text-gray-900 tracking-tight mt-2">{{ stats.total_alumni }}</p>
                    <p class="text-xs text-gray-400 mt-1">Alumni prodi {{ prodi?.kode_prodi }}</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-xs font-bold text-[#0D542B] uppercase tracking-wider">Kuesioner Univ Selesai</p>
                    <p class="text-3xl font-extrabold text-[#0D542B] tracking-tight mt-2">{{ stats.univ_selesai }}</p>
                    <p class="text-xs text-gray-400 mt-1">Tracer universitas lengkap</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Kuesioner Prodi Selesai</p>
                    <p class="text-3xl font-extrabold text-blue-700 tracking-tight mt-2">{{ stats.prodi_selesai }}</p>
                    <p class="text-xs text-gray-400 mt-1">Instrumen prodi lengkap</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-xs font-bold text-gray-700 uppercase tracking-wider">Belum Mengisi Prodi</p>
                    <p class="text-3xl font-extrabold text-gray-900 tracking-tight mt-2">{{ stats.prodi_belum }}</p>
                    <p class="text-xs text-gray-400 mt-1">Perlu dijangkau kembali</p>
                </div>
            </div>

            <!-- Panel Filter Komprehensif -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                    <h2 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider">
                        Filter & Pencarian Mahasiswa
                    </h2>
                    <button 
                        @click="resetFilters" 
                        class="text-xs text-[#0D542B] hover:underline font-bold transition-colors cursor-pointer"
                    >
                        Reset Filter
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Pencarian Nama / NIM -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Cari Nama / NIM</label>
                        <input 
                            type="text" 
                            v-model="search" 
                            @input="handleSearchInput" 
                            placeholder="Ketik nama atau NIM..." 
                            class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 py-2.5 px-3.5 font-medium text-gray-900 focus:bg-white focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        />
                    </div>

                    <!-- Filter Tahun Kelulusan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Tahun Kelulusan</label>
                        <select 
                            v-model="tahun" 
                            @change="applyFilters" 
                            class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 py-2.5 px-3.5 font-medium text-gray-900 focus:bg-white focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                            <option value="all">Semua Tahun (All)</option>
                            <option v-for="t in daftarTahun" :key="t" :value="t">
                                {{ t }}
                            </option>
                        </select>
                    </div>

                    <!-- Filter Semester Kelulusan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Semester Kelulusan</label>
                        <select 
                            v-model="semester" 
                            @change="applyFilters" 
                            class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 py-2.5 px-3.5 font-medium text-gray-900 focus:bg-white focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                            <option value="all">Semua Semester (All)</option>
                            <option value="Gasal">Gasal</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>

                    <!-- Filter Status Kuesioner Universitas -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Status Kuesioner Univ</label>
                        <select 
                            v-model="statusUniv" 
                            @change="applyFilters" 
                            class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 py-2.5 px-3.5 font-medium text-gray-900 focus:bg-white focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                            <option value="all">Semua Status (All)</option>
                            <option value="selesai">Selesai</option>
                            <option value="belum_selesai">Belum Selesai</option>
                        </select>
                    </div>

                    <!-- Filter Status Kuesioner Prodi -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Status Kuesioner Prodi</label>
                        <select 
                            v-model="statusProdi" 
                            @change="applyFilters" 
                            class="w-full text-xs rounded-xl border border-gray-200 bg-gray-50 py-2.5 px-3.5 font-medium text-gray-900 focus:bg-white focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                            <option value="all">Semua Status (All)</option>
                            <option value="selesai">Selesai</option>
                            <option value="belum_selesai">Belum Selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Mahasiswa ala DataTables -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <!-- DataTables Top Bar -->
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-600">
                        <span>Tampilkan</span>
                        <select v-model="perPage" class="text-xs rounded-lg border border-gray-200 bg-white py-1.5 px-2.5 font-bold text-gray-800 outline-none">
                            <option :value="5">5</option>
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                        </select>
                        <span>data per halaman</span>
                    </div>

                    <div class="text-xs text-gray-500 font-medium">
                        Menampilkan <span class="font-bold text-gray-900">{{ (currentPage - 1) * perPage + 1 }}</span> &ndash; 
                        <span class="font-bold text-gray-900">{{ Math.min(currentPage * perPage, alumnis.length) }}</span> dari 
                        <span class="font-bold text-gray-900">{{ alumnis.length }}</span> total mahasiswa
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-4 px-5">No</th>
                                <th class="py-4 px-5">Mahasiswa / Alumni</th>
                                <th class="py-4 px-5">Tahun & Semester</th>
                                <th class="py-4 px-5 text-center">Kuesioner Univ</th>
                                <th class="py-4 px-5 text-center">Kuesioner Prodi</th>
                                <th class="py-4 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="paginatedAlumnis.length === 0">
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    Tidak ada data mahasiswa atau alumni yang cocok dengan filter yang dipilih.
                                </td>
                            </tr>
                            <tr 
                                v-for="(alumni, idx) in paginatedAlumnis" 
                                :key="alumni.id" 
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="py-4 px-5 font-mono font-medium text-gray-400">
                                    {{ (currentPage - 1) * perPage + idx + 1 }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-extrabold text-gray-900 text-sm">{{ alumni.nama }}</div>
                                    <div class="font-mono text-gray-400 text-xs mt-0.5">{{ alumni.nim }}</div>
                                </td>
                                <td class="py-4 px-5 text-gray-600 font-medium">
                                    {{ alumni.tahun_lulus }}
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span 
                                        v-if="alumni.is_univ_complete"
                                        class="inline-block px-3 py-1 bg-[#0D542B] text-white font-bold text-xs rounded-full"
                                    >
                                        Selesai
                                    </span>
                                    <span 
                                        v-else
                                        class="inline-block px-3 py-1 bg-gray-100 text-gray-600 font-medium text-xs rounded-full"
                                    >
                                        Belum Lengkap
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span 
                                        v-if="alumni.is_prodi_complete"
                                        class="inline-block px-3 py-1 bg-[#0D542B] text-white font-bold text-xs rounded-full"
                                    >
                                        Selesai
                                    </span>
                                    <span 
                                        v-else
                                        class="inline-block px-3 py-1 bg-[#FDC700] text-black font-bold text-xs rounded-full"
                                    >
                                        {{ alumni.prodi_answers_count }}/{{ alumni.total_prodi_questions }} Terjawab
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <Link 
                                        :href="`/prodi/alumni/${alumni.id}`"
                                        class="inline-flex items-center px-4 py-2 bg-[#0D542B] hover:bg-[#093c1f] text-white text-xs font-bold rounded-xl transition-colors shadow-xs cursor-pointer"
                                    >
                                        Detail &rarr;
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- DataTables Pagination Controls -->
                <div class="p-5 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                    <span class="text-xs text-gray-500 font-medium">
                        Halaman <span class="font-bold text-gray-900">{{ currentPage }}</span> dari <span class="font-bold text-gray-900">{{ totalPages }}</span>
                    </span>

                    <div class="flex items-center gap-1.5">
                        <button 
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer"
                        >
                            Sebelumnya
                        </button>

                        <button 
                            v-for="page in totalPages" 
                            :key="page"
                            @click="goToPage(page)"
                            v-show="page === 1 || page === totalPages || Math.abs(page - currentPage) <= 1"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg transition-colors cursor-pointer"
                            :class="currentPage === page ? 'bg-[#0D542B] text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-100'"
                        >
                            {{ page }}
                        </button>

                        <button 
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors cursor-pointer"
                        >
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>
</template>
