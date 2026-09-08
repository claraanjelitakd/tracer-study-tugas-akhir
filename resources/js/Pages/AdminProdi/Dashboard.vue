<!--
  Halaman Dashboard Admin Prodi
-->
<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

// Method proses logout Admin Prodi dan kembali ke beranda (Home)
const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head title="Dashboard Admin Prodi" />

    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin Prodi</h1>
                <button @click="logout" class="text-red-600 hover:text-red-800 font-medium">Logout</button>
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        Selamat datang di Dashboard Admin Prodi. Anda dapat melihat ringkasan data alumni per prodi di sini.
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
