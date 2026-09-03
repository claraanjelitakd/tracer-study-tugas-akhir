<!--
  Halaman Profil Alumni Tracer Study
  Fungsi: Menampilkan dan menyimpan biodata diri, data akademik, dan riwayat profesional.
  Controller: ProfilController (Tampil), SimpanProfilController (Simpan)
-->
<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import FormPribadi from './Components/FormPribadi.vue';
import FormAkademik from './Components/FormAkademik.vue';
import FormOrangTua from './Components/FormOrangTua.vue';
import FormKarier from './Components/FormKarier.vue';

const props = defineProps({
    alumniData: Object,
    formData: Object,
    provinces: Array,
    kabupatens: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Menerima form data yang sudah dirakit 100% oleh backend
const form = useForm(props.formData);

// State untuk active tab
const activeTab = ref('pribadi');

const submit = () => {
    form.post('/alumni/profile', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Profil Alumni - Tracer Study" />

    <div class="min-h-screen bg-gray-50 pb-20">
        <!-- Navbar Minimal -->
        <nav class="bg-[#005B3C] shadow-lg border-b border-[#00422c] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center space-x-4">
                        <Link href="/alumni/dashboard" class="text-white hover:text-yellow-400 p-2 rounded-full transition-colors flex items-center font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Dashboard
                        </Link>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="hidden sm:flex flex-col items-end">
                            <span class="text-white font-semibold text-sm">{{ user.name }}</span>
                            <span class="text-green-200 text-xs">Alumni</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Header Profil -->
        <header class="bg-white border-b shadow-sm">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row items-center md:items-start justify-between">
                <div class="flex flex-col md:flex-row items-center md:items-center space-y-4 md:space-y-0 md:space-x-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-green-100 to-[#005B3C] text-white rounded-full flex items-center justify-center text-4xl font-bold shadow-md">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ user.name }}</h1>
                        <p class="text-gray-500 font-medium mt-1">NIM: <span class="text-[#005B3C] font-semibold">{{ alumniData.nim }}</span> &bull; {{ alumniData.prodi?.nama_prodi || 'Program Studi' }}</p>
                    </div>
                </div>
                <div class="mt-6 md:mt-0 flex items-center space-x-3">
                    <button @click="submit" :disabled="form.processing" class="px-6 py-2.5 bg-[#005B3C] text-white text-sm font-bold rounded-full shadow-lg hover:bg-[#00422c] hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto mt-8 px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
                
                <!-- Pesan Sukses -->
                <div v-if="page.props.flash.success" class="bg-green-50 text-green-700 px-6 py-4 flex items-center border-b border-green-100">
                    <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ page.props.flash.success }}</span>
                </div>

                <!-- Tabs Navigation -->
                <div class="flex overflow-x-auto border-b bg-gray-50/50 sticky top-0 z-10 backdrop-blur-sm">
                    <button type="button" @click="activeTab = 'pribadi'" :class="activeTab === 'pribadi' ? 'border-[#005B3C] text-[#005B3C] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex-1 text-center transition-colors">
                        Identitas & Alamat
                    </button>
                    <button type="button" @click="activeTab = 'akademik'" :class="activeTab === 'akademik' ? 'border-[#005B3C] text-[#005B3C] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex-1 text-center transition-colors">
                        Akademik & Yudisium
                    </button>
                    <button type="button" @click="activeTab = 'orangtua'" :class="activeTab === 'orangtua' ? 'border-[#005B3C] text-[#005B3C] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex-1 text-center transition-colors">
                        Data Orang Tua
                    </button>
                    <button type="button" @click="activeTab = 'karier'" :class="activeTab === 'karier' ? 'border-[#005B3C] text-[#005B3C] bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex-1 text-center transition-colors">
                        Karier & Jejaring
                    </button>
                </div>

                <!-- Tab Contents -->
                <div class="p-6 md:p-8 min-h-[400px]">
                    <div v-show="activeTab === 'pribadi'">
                        <FormPribadi :form="form" :provinces="provinces" :kabupatens="kabupatens" />
                    </div>
                    
                    <div v-show="activeTab === 'akademik'">
                        <FormAkademik :form="form" />
                    </div>
                    
                    <div v-show="activeTab === 'orangtua'">
                        <FormOrangTua :form="form" :provinces="provinces" :kabupatens="kabupatens" />
                    </div>
                    
                    <div v-show="activeTab === 'karier'">
                        <FormKarier :form="form" :provinces="provinces" :kabupatens="kabupatens" />
                    </div>
                </div>

                <div class="px-6 md:px-8 py-5 bg-gray-50 flex justify-end items-center border-t border-gray-100">
                    <span v-if="form.isDirty" class="text-sm text-yellow-600 mr-4 flex items-center animate-pulse">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Ada perubahan yang belum disimpan
                    </span>
                    <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-[#005B3C] text-white font-bold rounded-xl shadow-lg hover:bg-[#00422c] hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
