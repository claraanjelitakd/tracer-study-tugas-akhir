<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const logout = () => {
    useForm().post('/logout');
};
</script>

<template>
    <Head title="Dashboard Admin Prodi - SERU UKDW" />

    <div class="min-h-screen bg-gray-50">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin Prodi</h1>
                <button @click="logout" class="text-red-600 hover:text-red-800 font-medium">Logout</button>
            </div>
        </header>
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="p-6 text-gray-900">
                        Selamat datang, Admin Prodi.
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
