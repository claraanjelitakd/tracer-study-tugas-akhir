<!--
  Halaman Detail Alumni (Biro 3)
  Fungsi: Menampilkan detail alumni, status yudisium kelulusan, semester kelulusan akademik,
          serta manajemen sinkronisasi data karir LinkedIn (MCP).
  Desain: Mengikuti standarisasi navigasi & identitas Biro 3 (Hijau UKDW #005B3C, Kuning Landing Page #FACC15, Putih)
-->
<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    alumni: Object,
});

const isSyncing = ref(false);
const syncResult = ref(null);
const syncError = ref(null);

const hasLinkedIn = computed(() => {
    return !!(props.alumni?.linkedin_username || props.alumni?.linkedin_url);
});

// Helper status yudisium & semester kelulusan
const statusYudisium = computed(() => {
    return props.alumni?.yudisium?.proses_yudisium || props.alumni?.data_akademik?.status_yudisium || 'Lulus';
});

const semesterKelulusan = computed(() => {
    return props.alumni?.data_akademik?.tahun_akademik_lulus || props.alumni?.tahun_lulus || '-';
});

// Form untuk submit hasil sinkronisasi ke DB
const form = useInertiaForm({
    current_job: '',
    current_company: '',
    industry: '',
    location: '',
});

const startSync = async () => {
    isSyncing.value = true;
    syncError.value = null;
    
    try {
        const response = await fetch(`/biro3/alumni/${props.alumni.id}/sync-linkedin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            syncResult.value = data.data;
            // Isi form dengan data yang didapat
            form.current_job = data.data.current_job || '';
            form.current_company = data.data.current_company || '';
            form.industry = data.data.industry || '';
            form.location = data.data.location || '';
        } else {
            syncError.value = data.message || 'Terjadi kesalahan saat sinkronisasi data LinkedIn.';
        }
    } catch (error) {
        syncError.value = 'Gagal terhubung ke server atau terjadi kesalahan jaringan.';
    } finally {
        isSyncing.value = false;
    }
};

// Method proses logout Admin Biro 3 dan kembali ke beranda (Home)
const logout = () => {
    router.post('/logout');
};

const saveSync = () => {
    form.post(`/biro3/alumni/${props.alumni.id}/save-linkedin`, {
        preserveScroll: true,
        onSuccess: () => {
            syncResult.value = null;
        }
    });
};
</script>

<template>
    <Head :title="`Detail Alumni - ${alumni.user?.name || alumni.nim} - Biro 3`" />

    <div class="min-h-screen bg-[#F8FAF9] text-gray-800 font-sans pb-16">
        <!-- ========================================== -->
        <!-- NAVBAR ATAS SERAGAM BIRO 3                 -->
        <!-- ========================================== -->
        <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <!-- Brand & Logo Institusi -->
                    <div class="flex items-center gap-3">
                        <img src="/uploads/landing/2.png" alt="Logo UKDW" class="h-10 w-auto object-contain" />
                        <div>
                            <span class="text-xs font-bold text-[#005B3C] tracking-wider uppercase block">Biro 3 Kemahasiswaan & Alumni</span>
                            <span class="text-sm font-extrabold text-gray-900 block leading-tight">Universitas Kristen Duta Wacana</span>
                        </div>
                    </div>

                    <!-- Navigasi Menu Atas Seragam -->
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
        <header class="bg-gradient-to-r from-[#005B3C] to-[#007b55] pt-8 pb-16 relative overflow-hidden text-white">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-xs text-green-100 mb-4">
                    <Link href="/biro3/dashboard" class="hover:text-yellow-400 font-medium">Dashboard</Link>
                    <span>/</span>
                    <Link href="/biro3/alumni" class="hover:text-yellow-400 font-medium">Data Alumni</Link>
                    <span>/</span>
                    <span class="text-yellow-400 font-bold">Detail Alumni</span>
                </div>

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 bg-yellow-400 text-green-950 font-bold text-xs rounded-full uppercase tracking-wider">
                                Yudisium: {{ statusYudisium }}
                            </span>
                            <span class="px-2.5 py-0.5 bg-white/20 text-white font-medium text-xs rounded-full">
                                Semester: {{ semesterKelulusan }}
                            </span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">{{ alumni.user?.name || 'Alumni UKDW' }}</h1>
                        <p class="text-green-100 text-sm mt-1">
                            NIM: <span class="font-mono font-bold text-white">{{ alumni.nim || alumni.user?.username }}</span> &bull; 
                            Program Studi: <span class="font-semibold text-white">{{ alumni.prodi?.nama_prodi || '-' }}</span>
                        </p>
                    </div>

                    <div class="shrink-0">
                        <Link 
                            href="/biro3/alumni"
                            class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold uppercase tracking-wider rounded-lg border border-white/20 transition-all"
                        >
                            &larr; Kembali ke Daftar Alumni
                        </Link>
                    </div>
                </div>
            </div>
        </header>

        <!-- ========================================== -->
        <!-- KONTEN UTAMA                               -->
        <!-- ========================================== -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20 space-y-6">
            
            <!-- Flash Notifikasi -->
            <div v-if="$page.props.flash?.success" class="bg-green-50 border-l-4 border-[#005B3C] text-green-800 p-4 rounded-xl shadow-sm text-sm font-medium">
                {{ $page.props.flash.success }}
            </div>
            
            <div v-if="$page.props.errors?.message" class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-sm text-sm font-medium">
                {{ $page.props.errors.message }}
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Data Pribadi & Rekam Jejak Akademik -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 lg:col-span-2">
                    <h3 class="text-base font-extrabold text-gray-900 border-b border-gray-100 pb-3 mb-4 flex items-center justify-between">
                        <span>Informasi Pribadi & Akademik</span>
                        <span class="text-xs font-bold text-[#005B3C] bg-green-50 px-2.5 py-1 rounded-md">
                            Terverifikasi Akademik
                        </span>
                    </h3>

                    <dl class="divide-y divide-gray-100 text-sm">
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">Nama Lengkap</dt>
                            <dd class="text-gray-900 font-bold sm:col-span-2">{{ alumni.user?.name || '-' }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">NIM</dt>
                            <dd class="text-gray-900 font-mono font-semibold sm:col-span-2">{{ alumni.nim || alumni.user?.username || '-' }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">Program Studi</dt>
                            <dd class="text-gray-900 font-semibold sm:col-span-2">{{ alumni.prodi?.nama_prodi || '-' }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">Semester Kelulusan</dt>
                            <dd class="text-[#005B3C] font-bold sm:col-span-2">{{ semesterKelulusan }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">Status Yudisium</dt>
                            <dd class="sm:col-span-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    {{ statusYudisium }}
                                </span>
                            </dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">IPK Kelulusan</dt>
                            <dd class="text-gray-900 font-mono font-bold sm:col-span-2">{{ alumni.data_akademik?.ipk || '-' }}</dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">Judul Tugas Akhir / Skripsi</dt>
                            <dd class="text-gray-700 italic sm:col-span-2">
                                {{ alumni.data_akademik?.judul_ta || 'Belum ada catatan judul tugas akhir' }}
                            </dd>
                        </div>
                        <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <dt class="text-gray-500 font-medium">Tahun Lulus Ijazah</dt>
                            <dd class="text-gray-900 sm:col-span-2">{{ alumni.tahun_lulus || alumni.data_akademik?.tahun_lulus || '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Panel Sinkronisasi LinkedIn (MCP) -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-[#0077b5] border-b border-gray-100 pb-3 mb-4 flex items-center justify-between">
                            <span>Sinkronisasi LinkedIn</span>
                            <span class="text-[10px] font-mono font-bold bg-sky-50 text-[#0077b5] px-2 py-0.5 rounded">MCP Agent</span>
                        </h3>

                        <div class="space-y-3 mb-6">
                            <div>
                                <label class="text-xs font-semibold text-gray-500 block mb-1">Akun / Profil LinkedIn:</label>
                                <div 
                                    class="text-xs font-medium p-2.5 rounded-lg border"
                                    :class="hasLinkedIn ? 'bg-slate-50 border-slate-200 text-gray-800 font-mono break-all' : 'bg-rose-50 border-rose-100 text-rose-600'"
                                >
                                    {{ alumni.linkedin_username || alumni.linkedin_url || 'Belum dicantumkan oleh alumni' }}
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Fitur otomatisasi untuk menarik status riwayat karir dan perusahaan terkini alumni langsung dari profil LinkedIn resmi.
                            </p>
                        </div>
                    </div>

                    <div>
                        <button 
                            @click="startSync" 
                            :disabled="!hasLinkedIn || isSyncing"
                            class="w-full flex items-center justify-center py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-[#0077b5] hover:bg-[#005c8a] disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm"
                        >
                            <span v-if="isSyncing">Sedang Menarik Data LinkedIn...</span>
                            <span v-else>Tarik Data Pekerjaan Terkini</span>
                        </button>
                        
                        <!-- Pesan Kesalahan Sinkronisasi -->
                        <div v-if="syncError" class="mt-3 p-3 bg-rose-50 text-rose-700 text-xs rounded-xl border border-rose-100">
                            {{ syncError }}
                        </div>
                    </div>
                </div>

                <!-- Hasil Sinkronisasi LinkedIn (Tinjauan Sebelum Simpan) -->
                <div v-if="syncResult" class="col-span-1 lg:col-span-3 bg-sky-50/50 border-2 border-[#0077b5] rounded-2xl p-6 shadow-md">
                    <div class="flex items-center justify-between border-b border-sky-100 pb-3 mb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-[#0077b5]">Data Hasil Sinkronisasi LinkedIn</h3>
                            <p class="text-xs text-gray-600 mt-0.5">Tinjau atau sesuaikan data pekerjaan sebelum disimpan ke basis data sistem.</p>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-bold bg-[#0077b5] text-white rounded-lg">Siap Disimpan</span>
                    </div>
                    
                    <form @submit.prevent="saveSync" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Pekerjaan / Jabatan (Current Job)</label>
                                <input type="text" v-model="form.current_job" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Perusahaan Saat Ini (Current Company)</label>
                                <input type="text" v-model="form.current_company" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Sektor / Industri</label>
                                <input type="text" v-model="form.industry" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi / Wilayah</label>
                                <input type="text" v-model="form.location" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 pt-4 border-t border-sky-100">
                            <button 
                                type="button" 
                                @click="syncResult = null" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-white transition-colors"
                            >
                                Batalkan
                            </button>
                            <button 
                                type="submit" 
                                :disabled="form.processing" 
                                class="px-5 py-2 rounded-lg text-xs font-bold text-white bg-[#005B3C] hover:bg-[#00422c] shadow-sm transition-colors"
                            >
                                Simpan ke Basis Data
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Status Karir / Pekerjaan Tersimpan di Database -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 col-span-1 lg:col-span-3">
                    <h3 class="text-base font-extrabold text-gray-900 border-b border-gray-100 pb-3 mb-4 flex items-center justify-between">
                        <span>Status Karir Tersimpan di Database</span>
                        <span class="text-xs font-bold text-gray-500">Tracer Study Record</span>
                    </h3>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <dt class="text-xs font-semibold text-gray-500 mb-1">Profesi / Keahlian Tersimpan</dt>
                            <dd class="text-gray-900 font-bold">{{ alumni.expert || 'Belum terisi' }}</dd>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <dt class="text-xs font-semibold text-gray-500 mb-1">Perusahaan / Instansi Tersimpan</dt>
                            <dd class="text-gray-900 font-bold">{{ alumni.company?.nama_perusahaan || 'Belum terisi' }}</dd>
                        </div>
                    </dl>
                </div>

            </div>
        </main>
    </div>
</template>
