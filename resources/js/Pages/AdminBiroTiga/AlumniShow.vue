<!--
  Halaman Detail Alumni (Biro 3)
  Fungsi: Menampilkan detail alumni dan tombol sinkronisasi LinkedIn.
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
    return !!(props.alumni.linkedin_username || props.alumni.linkedin_url);
});

// Form untuk submit hasil sinkronisasi ke DB
const form = useForm({
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
            syncError.value = data.message || 'Terjadi kesalahan saat sinkronisasi.';
        }
    } catch (error) {
        syncError.value = 'Gagal terhubung ke server atau terjadi kesalahan jaringan.';
    } finally {
        isSyncing.value = false;
    }
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
    <Head title="Detail Alumni - Biro 3" />

    <div class="min-h-screen bg-gray-100 pb-12">
        <header class="bg-white shadow mb-6">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center gap-4">
                <Link href="/biro3/alumni" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">Detail Alumni: {{ alumni.user.name }}</h1>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <div v-if="$page.props.flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                {{ $page.props.flash.success }}
            </div>
            
            <div v-if="$page.props.errors.message" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                {{ $page.props.errors.message }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Data Sistem -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg col-span-1 md:col-span-2">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Pribadi & Akademik</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-0">
                        <dl class="sm:divide-y sm:divide-gray-200">
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{{ alumni.user.name }}</dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">NIM</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ alumni.user.username }}</dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Program Studi</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ alumni.prodi?.nama_prodi }}</dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Tahun Lulus</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ alumni.tahun_lulus || '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Panel Sinkronisasi LinkedIn -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg border-t-4 border-[#0077b5]">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-bold text-[#0077b5] flex items-center">
                            LinkedIn Sync (MCP)
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-1">Username/URL:</p>
                            <p class="font-medium truncate" :class="hasLinkedIn ? 'text-gray-900' : 'text-red-500'">
                                {{ alumni.linkedin_username || alumni.linkedin_url || 'Belum diisi oleh alumni' }}
                            </p>
                        </div>

                        <button 
                            @click="startSync" 
                            :disabled="!hasLinkedIn || isSyncing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#0077b5] hover:bg-[#005582] disabled:opacity-50 transition-colors"
                        >
                            <span v-if="isSyncing">Mencari Data...</span>
                            <span v-else>Tarik Data Pekerjaan Terkini</span>
                        </button>
                        
                        <!-- Hasil / Error Sinkronisasi -->
                        <div v-if="syncError" class="mt-4 p-3 bg-red-50 text-red-700 text-sm rounded border border-red-200">
                            {{ syncError }}
                        </div>
                    </div>
                </div>

                <!-- Form Hasil Sinkronisasi (Hanya muncul jika sinkronisasi berhasil) -->
                <div v-if="syncResult" class="col-span-1 md:col-span-3 bg-white shadow overflow-hidden sm:rounded-lg border-2 border-[#0077b5]">
                    <div class="px-4 py-5 sm:px-6 bg-[#0077b5]/10 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-bold text-[#0077b5]">Data Ditemukan dari LinkedIn</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Tinjau data ini sebelum menyimpannya ke database.</p>
                    </div>
                    
                    <form @submit.prevent="saveSync" class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Pekerjaan / Jabatan (Current Job)</label>
                                <input type="text" v-model="form.current_job" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Perusahaan Saat Ini (Current Company)</label>
                                <input type="text" v-model="form.current_company" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Industri</label>
                                <input type="text" v-model="form.industry" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lokasi / Wilayah</label>
                                <input type="text" v-model="form.location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#0077b5] focus:ring-[#0077b5]">
                            </div>
                        </div>
                        
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="button" @click="syncResult = null" class="mr-3 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#005B3C] hover:bg-[#00422c]">
                                Simpan ke Database
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Data Pekerjaan di Database (Tersimpan) -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg col-span-1 md:col-span-3 mt-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Status Pekerjaan di Database (Tersimpan)</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-0">
                        <dl class="sm:divide-y sm:divide-gray-200">
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Keahlian / Jabatan Tersimpan</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{{ alumni.expert || '-' }}</dd>
                            </div>
                            <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Perusahaan Tersimpan</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ alumni.company?.nama_perusahaan || '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>
