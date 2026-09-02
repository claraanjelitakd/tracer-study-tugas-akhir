<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    alumni: Object,
    responsesCount: Number,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const logout = () => {
    useForm().post('/logout');
};

// Check if today is alumni's birthday
const isBirthday = computed(() => {
    if (!props.alumni?.tanggal_lahir) return false;
    const today = new Date();
    const dob = new Date(props.alumni.tanggal_lahir);
    return today.getMonth() === dob.getMonth() && today.getDate() === dob.getDate();
});

// Gamification: Completion progress
// Total questions approximately 32 (F1 to F22 + options). Let's use 32 as a fixed denominator for now.
const TOTAL_QUESTIONS = 32;
const progressPercentage = computed(() => {
    if (!props.responsesCount) return 0;
    const pct = Math.round((props.responsesCount / TOTAL_QUESTIONS) * 100);
    return pct > 100 ? 100 : pct;
});
</script>

<template>
    <Head title="Dashboard Alumni - Tracer Study UKDW" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
        
        <!-- Navbar -->
        <nav class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center space-x-4">
                        <!-- UKDW Logo -->
                        <img src="https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png" alt="UKDW Logo" class="h-12 object-contain">
                        <div class="flex-shrink-0 flex items-center border-l-2 border-gray-200 pl-4 ml-4">
                            <span class="text-[#005B3C] font-bold text-xl tracking-wide uppercase">Tracer Study</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="hidden sm:flex flex-col items-end">
                            <span class="text-gray-900 font-semibold text-sm">{{ user.name }}</span>
                            <span class="text-gray-500 text-xs">Alumni</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-[#005B3C] text-white flex items-center justify-center font-bold text-lg">
                            {{ user.name.charAt(0) }}
                        </div>
                        <button @click="logout" class="text-gray-500 hover:text-red-600 transition-colors flex items-center text-sm font-medium">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            
            <!-- Birthday Banner -->
            <div v-if="isBirthday" class="bg-gradient-to-r from-[#FFD700] to-yellow-400 p-8 rounded-xl flex items-center justify-between shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-[#005B3C] mb-2">Selamat Ulang Tahun, {{ user.name }}!</h2>
                    <p class="text-[#005B3C] font-medium opacity-90">Semoga panjang umur, sehat selalu, dan kariernya semakin cemerlang bersama almamater tercinta.</p>
                </div>
                <svg class="absolute right-0 bottom-0 text-[#005B3C] opacity-10 h-48 w-48 -mr-10 -mb-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"></path></svg>
            </div>

            <!-- Welcome Hero (Corporate Clean Style) -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row">
                <div class="md:w-1/2 relative min-h-[300px]">
                    <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80" alt="Students" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/80 to-gray-900/40 mix-blend-multiply"></div>
                    <div class="absolute inset-0 p-10 flex flex-col justify-center text-white">
                        <span class="text-yellow-400 font-bold tracking-wider uppercase text-sm mb-2">Sistem Ekosistem Rekam Jejak</span>
                        <h1 class="text-4xl font-bold leading-tight mb-4">Tracer Study <br>Universitas Kristen Duta Wacana</h1>
                    </div>
                </div>
                <div class="md:w-1/2 bg-[#005B3C] p-10 flex flex-col justify-center text-white relative">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-semibold mb-4 text-[#FFD700] relative z-10">Tujuan dan Manfaat</h2>
                    <p class="text-gray-100 leading-relaxed mb-6 relative z-10 text-sm">
                        Pengisian Tracer Study sangat berarti bagi perguruan tinggi untuk mengevaluasi relevansi kurikulum dengan dunia kerja masa kini, serta sebagai dasar perhitungan Indikator Kinerja Utama (IKU) untuk pemeringkatan akreditasi.
                    </p>
                    <div class="bg-[#FFD700] p-6 rounded-lg text-[#005B3C] mt-auto relative z-10">
                        <h3 class="font-bold mb-2">Partisipasi Aktif Anda</h3>
                        <p class="text-sm">Bantu almamater menghasilkan lulusan yang lebih baik di masa mendatang melalui pengalaman kerja Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Dashboard Actions & Status -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Kuesioner Action Card -->
                <div class="md:col-span-2 bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="flex items-start mb-6">
                        <div class="p-4 bg-green-50 rounded-xl mr-6">
                            <svg class="w-8 h-8 text-[#005B3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Kuesioner Tracer Study 2026</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">
                                Form ini dirancang untuk mengetahui rekam jejak karier Anda. Dapat diisi secara bertahap dan disimpan otomatis.
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-auto bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-sm font-semibold text-gray-700">Progres Pengisian</span>
                            <span class="text-lg font-bold text-[#005B3C]">{{ progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 h-2.5 rounded-full overflow-hidden mb-6">
                            <div class="bg-[#005B3C] h-full rounded-full transition-all duration-1000 ease-out" :style="{ width: progressPercentage + '%' }"></div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full mr-2" :class="progressPercentage >= 100 ? 'bg-green-500' : (progressPercentage > 0 ? 'bg-yellow-500' : 'bg-gray-400')"></div>
                                <span class="text-sm text-gray-600 font-medium">
                                    {{ progressPercentage >= 100 ? 'Selesai' : (progressPercentage > 0 ? 'Sedang Dikerjakan' : 'Belum Dimulai') }}
                                </span>
                            </div>
                            <Link href="/alumni/kuesioner" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-[#005B3C] hover:bg-green-800 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#005B3C]">
                                {{ progressPercentage > 0 && progressPercentage < 100 ? 'Lanjutkan Pengisian' : 'Mulai Mengisi' }}
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Profil Card -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="p-4 bg-yellow-50 rounded-xl w-16 mb-6">
                        <svg class="w-8 h-8 text-[#FFD700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Profil Alumni</h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-8 flex-1">
                        Kelola data pribadi Anda, sesuaikan informasi kontak agar kampus dapat tetap terhubung dengan Anda.
                    </p>
                    <Link href="#" class="w-full inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Lihat Profil
                    </Link>
                </div>

            </div>
        </main>
    </div>
</template>
