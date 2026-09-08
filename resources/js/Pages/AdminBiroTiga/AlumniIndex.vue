<!--
  Halaman Direktori Alumni Lulus Yudisium (Biro 3)
  Fungsi: Menampilkan data alumni yang telah lulus yudisium per tahun / semester kelulusan.
  Interaksi: Pemilihan tahun/periode kelulusan menggunakan DROPDOWN (bukan tab navigasi),
             sehingga data yang disajikan terisolasi per periode yang dipilih saja.
  Desain: Mengikuti identitas visual formal Biro 3 (Hijau UKDW #005B3C, Kuning Landing Page #FACC15, Putih)
-->
<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    alumnis: Array,
    daftarSemester: Array,
    semesterCounts: Object,
    semesterAktif: String,
    prodis: Array,
    totalLulusPeriode: Number,
    filters: Object,
});

// State reaktif filter
const search = ref(props.filters.search || '');
const prodiId = ref(props.filters.prodi_id || '');
const selectedSemester = ref(props.semesterAktif || (props.daftarSemester[0] || ''));

// Debounce timer untuk input pencarian
let searchTimeout = null;

// Fungsi untuk mengirim permintaan filter ke server
const applyFilters = () => {
    router.get('/biro3/alumni', {
        search: search.value,
        prodi_id: prodiId.value,
        semester: selectedSemester.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

// Pantau perubahan pada dropdown tahun yudisium
const onSemesterChange = () => {
    applyFilters();
};

// Pantau perubahan pada dropdown prodi
const onProdiChange = () => {
    applyFilters();
};

// Pantau input pencarian dengan debounce 350ms
const onSearchInput = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 350);
};

// Reset seluruh filter ke default
const resetFilter = () => {
    search.value = '';
    prodiId.value = '';
    selectedSemester.value = props.daftarSemester[0] || '';
    applyFilters();
};

// Logout handler untuk keluar kembali ke beranda (Home)
const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head title="Direktori Data Alumni - Biro 3" />

    <div class="min-h-screen bg-[#F8FAF9] text-gray-800 font-sans pb-16">
        <!-- ========================================== -->
        <!-- NAVBAR ATAS RESMI & SERAGAM BIRO 3         -->
        <!-- ========================================== -->
        <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <!-- Identitas & Logo Institusi -->
                    <div class="flex items-center gap-3">
                        <img src="/uploads/landing/2.png" alt="Logo UKDW" class="h-10 w-auto object-contain" />
                        <div>
                            <span class="text-xs font-bold text-[#005B3C] tracking-wider uppercase block">Biro 3 Kemahasiswaan & Alumni</span>
                            <span class="text-sm font-extrabold text-gray-900 block leading-tight">Universitas Kristen Duta Wacana</span>
                        </div>
                    </div>

                    <!-- Navigasi Menu Seragam Biro 3 -->
                    <div class="hidden md:flex items-center space-x-6">
                        <Link 
                            href="/biro3/dashboard" 
                            class="text-sm font-semibold text-gray-600 hover:text-[#005B3C] transition-colors"
                        >
                            Dashboard
                        </Link>
                        <Link 
                            href="/biro3/alumni" 
                            class="text-sm font-bold text-[#005B3C] border-b-2 border-[#005B3C] pb-1"
                        >
                            Data Alumni
                        </Link>
                        <Link 
                            href="/biro3/pertanyaan" 
                            class="text-sm font-semibold text-gray-600 hover:text-[#005B3C] transition-colors"
                        >
                            Kelola Pertanyaan
                        </Link>
                    </div>

                    <!-- User & Tombol Keluar Bersih -->
                    <div class="flex items-center space-x-4">
                        <span class="text-xs font-semibold text-gray-600 hidden sm:inline-block">Admin Biro 3</span>
                        <button 
                            @click="logout" 
                            class="px-4 py-2 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subnav Mobile -->
            <div class="md:hidden px-4 py-2 bg-gray-50 border-t border-gray-100 flex justify-around text-xs font-semibold">
                <Link href="/biro3/dashboard" class="text-gray-600">Dashboard</Link>
                <Link href="/biro3/alumni" class="text-[#005B3C] font-bold">Data Alumni</Link>
                <Link href="/biro3/pertanyaan" class="text-gray-600">Kelola Pertanyaan</Link>
            </div>
        </nav>

        <!-- ========================================== -->
        <!-- HEADER BANNER HIJAU RESMI & KUNING LANDING -->
        <!-- ========================================== -->
        <header class="bg-gradient-to-r from-[#005B3C] to-[#007b55] pt-10 pb-20 relative overflow-hidden text-white">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 text-white rounded-full text-xs font-bold tracking-wide backdrop-blur-sm mb-2">
                        Status Kelulusan: Yudisium Lulus
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Direktori Alumni Lulus Yudisium</h1>
                    <p class="text-green-100 text-sm mt-1 max-w-2xl">
                        Daftar lulusan yang telah terverifikasi yudisium, disajikan secara spesifik per tahun dan semester kelulusan akademik.
                    </p>
                </div>

                <div class="shrink-0 text-left md:text-right bg-white/10 backdrop-blur-md px-5 py-3 rounded-xl border border-white/20">
                    <div class="text-[11px] uppercase tracking-wider text-green-200 font-semibold">Wisudawan Periode Terpilih</div>
                    <div class="text-2xl md:text-3xl font-black text-yellow-400 mt-0.5">{{ totalLulusPeriode }} Alumni</div>
                    <div class="text-[11px] text-white/80 mt-0.5 font-medium">{{ selectedSemester || 'Belum Ada Periode' }}</div>
                </div>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- KONTEN UTAMA DIREKTORI                     -->
        <!-- ========================================== -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
            
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                <!-- ========================================== -->
                <!-- PANEL FILTER: DROPDOWN TAHUN YUDISIUM DLL  -->
                <!-- ========================================== -->
                <div class="p-6 bg-slate-50/70 border-b border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        
                        <!-- 1. Dropdown Tahun / Periode Yudisium Kelulusan (Utama) -->
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span>Tahun / Semester Yudisium</span>
                                <span class="text-[10px] text-[#005B3C] font-semibold bg-green-100/70 px-2 py-0.5 rounded">Filter Wajib</span>
                            </label>
                            <div class="relative">
                                <select 
                                    v-model="selectedSemester" 
                                    @change="onSemesterChange"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-xs font-bold text-gray-800 focus:ring-2 focus:ring-[#005B3C] focus:border-[#005B3C] shadow-sm appearance-none cursor-pointer"
                                >
                                    <option v-for="sem in daftarSemester" :key="sem" :value="sem">
                                        {{ sem }} ({{ semesterCounts[sem] || 0 }} Alumni)
                                    </option>
                                    <option v-if="daftarSemester.length === 0" value="">
                                        Tidak ada data periode kelulusan
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Dropdown Filter Program Studi -->
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Program Studi
                            </label>
                            <div class="relative">
                                <select 
                                    v-model="prodiId" 
                                    @change="onProdiChange"
                                    class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-700 focus:ring-2 focus:ring-[#005B3C] focus:border-[#005B3C] shadow-sm appearance-none cursor-pointer"
                                >
                                    <option value="">Semua Program Studi</option>
                                    <option v-for="prodi in prodis" :key="prodi.id" :value="prodi.id">
                                        {{ prodi.nama_prodi }}
                                    </option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Pencarian Nama / NIM -->
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Cari Nama / NIM
                            </label>
                            <input 
                                type="text" 
                                v-model="search" 
                                @input="onSearchInput"
                                placeholder="Ketik nama atau NIM..." 
                                class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-xs text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-[#005B3C] focus:border-[#005B3C] shadow-sm"
                            />
                        </div>

                        <!-- 4. Tombol Reset -->
                        <div class="md:col-span-1">
                            <button 
                                type="button" 
                                @click="resetFilter"
                                title="Kembalikan Filter"
                                class="w-full py-2.5 px-3 bg-white border border-gray-200 hover:bg-gray-100 text-gray-600 rounded-xl text-xs font-bold transition-colors shadow-sm flex items-center justify-center"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BARIS INFORMASI PERIODE AKTIF              -->
                <!-- ========================================== -->
                <div class="px-6 py-4 bg-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium">Periode Kelulusan:</span>
                        <span class="px-2.5 py-1 bg-green-50 text-[#005B3C] font-extrabold text-xs rounded-lg border border-green-200/60">
                            {{ selectedSemester || 'Belum dipilih' }}
                        </span>
                        <span class="text-xs text-gray-400">&bull;</span>
                        <span class="text-xs font-semibold text-gray-600">
                            Menampilkan <strong class="text-gray-900">{{ alumnis.length }}</strong> alumni
                        </span>
                    </div>

                    <div class="text-xs text-gray-400 italic">
                        *Data hanya memuat lulusan berstatus yudisium Lulus
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TABEL DATA ALUMNI (HANYA PERIODE TERPILIH) -->
                <!-- ========================================== -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-600 font-bold uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="py-3.5 px-4 w-12 text-center">No</th>
                                <th scope="col" class="py-3.5 px-4">NIM & Nama Alumni</th>
                                <th scope="col" class="py-3.5 px-4">Program Studi</th>
                                <th scope="col" class="py-3.5 px-4 text-center">IPK</th>
                                <th scope="col" class="py-3.5 px-4">Judul Tugas Akhir / Skripsi</th>
                                <th scope="col" class="py-3.5 px-4 text-center">Status Yudisium</th>
                                <th scope="col" class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr 
                                v-for="(alumni, idx) in alumnis" 
                                :key="alumni.id"
                                class="hover:bg-green-50/30 transition-colors"
                            >
                                <td class="py-3.5 px-4 text-center text-gray-400 font-mono">
                                    {{ idx + 1 }}
                                </td>
                                
                                <td class="py-3.5 px-4">
                                    <div class="font-extrabold text-gray-900 text-sm">
                                        {{ alumni.user?.name || alumni.data_akademik?.nama || '-' }}
                                    </div>
                                    <div class="text-[11px] font-mono text-[#005B3C] font-semibold mt-0.5">
                                        NIM: {{ alumni.nim || alumni.user?.username || '-' }}
                                    </div>
                                </td>

                                <td class="py-3.5 px-4 font-medium text-gray-700">
                                    {{ alumni.prodi?.nama_prodi || '-' }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-800">
                                        {{ alumni.data_akademik?.ipk ? Number(alumni.data_akademik.ipk).toFixed(2) : '-' }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate" :title="alumni.data_akademik?.judul_ta || ''">
                                    {{ alumni.data_akademik?.judul_ta || '-' }}
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-green-100 text-green-800 border border-green-200">
                                        Lulus
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <Link 
                                        :href="`/biro3/alumni/${alumni.id}`"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-[#005B3C] bg-green-50 hover:bg-[#005B3C] hover:text-white border border-green-200 hover:border-transparent transition-all"
                                    >
                                        Lihat Detail
                                    </Link>
                                </td>
                            </tr>

                            <!-- State Kosong / Belum Ada Data -->
                            <tr v-if="alumnis.length === 0">
                                <td colspan="7" class="py-12 text-center text-gray-400">
                                    <p class="text-sm font-semibold text-gray-600">Tidak ada data alumni lulus untuk periode ini.</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Silakan pilih tahun yudisium lain pada dropdown atau atur ulang kata kunci pencarian.
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Rekap Data -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 flex flex-col sm:flex-row justify-between items-center gap-2">
                    <div>
                        Menampilkan alumni lulus yudisium untuk periode: <strong class="text-gray-800">{{ selectedSemester }}</strong>
                    </div>
                    <div>
                        Total: <strong class="text-[#005B3C]">{{ alumnis.length }}</strong> alumni terdata
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>
