<!--
  Halaman Daftar Alumni (Biro 3)
  Fungsi: Menampilkan daftar alumni beserta fitur pencarian dan filter.
-->
<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    alumnis: Object,
    prodis: Array,
    filters: Object,
});

// State untuk filter pencarian
const search = ref(props.filters.search || '');
const prodi_id = ref(props.filters.prodi_id || '');

// Fungsi untuk melakukan pencarian ke backend
const performSearch = () => {
    router.get('/biro3/alumni', { search: search.value, prodi_id: prodi_id.value }, {
        preserveState: true,
        replace: true,
    });
};

const logout = () => {
    useForm().post('/logout');
};
</script>

<template>
    <Head title="Data Alumni - Biro 3" />

    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Data Alumni</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-500">Admin Biro 3</span>
                    <button @click="logout" class="text-red-600 hover:text-red-800 font-medium">Logout</button>
                </div>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                
                <!-- Filter & Search -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700">Cari Alumni</label>
                        <input type="text" id="search" v-model="search" @keyup.enter="performSearch" placeholder="Tekan Enter untuk mencari Nama, NIM, atau URL LinkedIn..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#005B3C] focus:ring-[#005B3C] sm:text-sm">
                    </div>
                    <div class="w-full md:w-64">
                        <label for="prodi" class="block text-sm font-medium text-gray-700">Filter Prodi</label>
                        <select id="prodi" v-model="prodi_id" @change="performSearch" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#005B3C] focus:ring-[#005B3C] sm:text-sm">
                            <option value="">Semua Prodi</option>
                            <option v-for="prodi in prodis" :key="prodi.id" :value="prodi.id">{{ prodi.nama_prodi }}</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="performSearch" class="bg-[#005B3C] text-white px-4 py-2 rounded-md hover:bg-[#00422c] shadow-sm">Cari</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pekerjaan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="alumni in alumnis.data" :key="alumni.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ alumni.user.username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alumni.user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alumni.prodi?.nama_prodi }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ alumni.expert || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link :href="`/biro3/alumni/${alumni.id}`" class="text-[#005B3C] hover:text-[#00422c] bg-green-100 hover:bg-green-200 px-3 py-1 rounded-full transition-colors">Lihat Detail</Link>
                                    </td>
                                </tr>
                                <tr v-if="alumnis.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data alumni ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
