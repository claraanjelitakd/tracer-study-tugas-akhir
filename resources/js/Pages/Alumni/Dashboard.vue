<!--
  Halaman: Dashboard Alumni (Frontend)
  File: resources/js/Pages/Alumni/Dashboard.vue
  
  DIRELOAD OLEH BACKEND DARI:
  Controller: App\Http\Controllers\Alumni\Dashboard\DashboardController.php (method tampilkanDashboard)
  Route URL : /alumni/dashboard (GET)
-->
<script setup>
// Mengimpor modul resmi dari Inertia.js:
// - Head   : Untuk mengubah judul tab browser (<title>)
// - Link   : Komponen navigasi pengganti <a href> agar pindah halaman tanpa reload layar putih
// - router : Untuk mengirim perintah cepat ke backend (seperti logout, delete, POST)
import { Head, Link, router } from '@inertiajs/vue3';

/**
 * ====================================================================
 * MENERIMA DATA (PROPS) DARI BACKEND
 * ====================================================================
 * Data di bawah ini dikirim langsung oleh DashboardController.php (baris 52-58)
 * melalui fungsi Inertia::render('Alumni/Dashboard', [...])
 */
defineProps({
    // Berisi data akun user yang login: { id, name, email, role, ... }
    user: Object,

    // Nilai true jika alumni sudah mengisi data profil (kolom is_profile_completed di database)
    profileCompleted: Boolean,

    // Nilai true jika alumni sudah pernah mengisi kuesioner (tabel responses > 0)
    questionnaireCompleted: Boolean,
});

/**
 * ====================================================================
 * FUNGSI-FUNGSI AKSI JAVASCRIPT
 * ====================================================================
 */

/**
 * Fungsi logout:
 * - Dijalankan saat tombol "Logout" di navbar atas diklik (@click="logout")
 * - Mengirim request POST ke URL '/logout'
 * - Ditangani di Backend oleh: Laravel Fortify / AuthenticatedSessionController
 * - Efek: Sesi user dihapus dari server, lalu user diarahkan kembali ke halaman Login/Home
 */
const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <Head title="Dashboard Alumni - Tracer Study" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-[#f0f7f4] to-gray-50 relative overflow-hidden">
        <!-- Background Ornaments -->
        <div class="absolute top-0 left-0 w-full h-96 bg-[#005B3C] rounded-b-[40%] shadow-2xl z-0 transform -translate-y-20 opacity-90"></div>
        <div class="absolute top-10 right-10 w-64 h-64 bg-green-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>

        <!-- Navbar Minimal -->
        <nav class="relative z-10 bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center space-x-3">
                        <img src="/uploads/landing/2.png" alt="Logo" class="h-10 w-10 object-contain drop-shadow-md bg-white rounded-full p-1" onerror="this.style.display='none'" />
                        <span class="text-white font-bold text-xl tracking-wide drop-shadow-md">Tracer Study UKDW</span>
                    </div>
                    <div class="flex items-center space-x-6">
                        <span class="text-white font-medium text-sm hidden md:block drop-shadow-md">{{ user.name }}</span>
                        <button @click="logout" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-sm font-semibold rounded-full transition-all duration-300 shadow-sm">Logout</button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8 mt-4">
            
            <!-- Welcome Header -->
            <transition appear name="fade-down">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-white drop-shadow-lg tracking-tight mb-3">Selamat datang kembali, {{ user.name }}!</h2>
                    <p class="text-green-100 text-lg max-w-2xl mx-auto drop-shadow-md">Terima kasih telah berkontribusi. Mari lengkapi data Anda untuk membantu peningkatan kualitas pendidikan kampus kita tercinta.</p>
                </div>
            </transition>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- ======================================================= -->
                <!-- CARD 1: STATUS PROFIL & BIODATA                         -->
                <!-- ======================================================= -->
                <transition appear name="fade-up-1">
                    <div class="group bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden">
                        <!-- Garis warna atas: Hijau jika profil lengkap, Kuning jika belum lengkap -->
                        <div class="absolute top-0 left-0 w-full h-1" :class="profileCompleted ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-yellow-400 to-yellow-600'"></div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-inner" :class="profileCompleted ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            
                            <!-- v-if: Jika props 'profileCompleted' == true, tampilkan badge hijau -->
                            <span v-if="profileCompleted" class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 ring-1 ring-inset ring-green-600/20 shadow-sm">
                                Sudah Lengkap
                            </span>
                            <!-- v-else: Jika belum lengkap (false), tampilkan badge kuning kelap-kelip -->
                            <span v-else class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 ring-1 ring-inset ring-yellow-600/20 shadow-sm animate-pulse">
                                Belum Lengkap
                            </span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Profil & Biodata</h3>
                        <p class="text-gray-500 mb-8 leading-relaxed">Kelola identitas diri, data akademik, riwayat pekerjaan, dan informasi personal Anda lainnya di sini.</p>
                        
                        <!-- 
                          Komponen Link Inertia:
                          - Mengarahkan alumni ke URL '/alumni/profile'
                          - Ditangani di Backend oleh: App\Http\Controllers\Alumni\Profil\ProfilController::class (method index)
                          - Membuka halaman Vue: resources/js/Pages/Alumni/Profil/Index.vue
                        -->
                        <Link href="/alumni/profile" class="inline-flex items-center justify-center w-full px-6 py-3.5 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-gradient-to-r from-[#005B3C] to-[#00422c] hover:from-[#006f49] hover:to-[#005B3C] transform transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                            Kelola Data Profil
                            <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </Link>
                    </div>
                </transition>

                <!-- ======================================================= -->
                <!-- CARD 2: STATUS KUESIONER TRACER STUDY                   -->
                <!-- ======================================================= -->
                <transition appear name="fade-up-2">
                    <div class="group bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl p-8 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 relative overflow-hidden">
                        <!-- Garis warna atas: Hijau jika kuesioner selesai, Merah jika belum selesai -->
                        <div class="absolute top-0 left-0 w-full h-1" :class="questionnaireCompleted ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-red-400 to-red-600'"></div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-inner" :class="questionnaireCompleted ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            
                            <!-- v-if: Jika props 'questionnaireCompleted' == true, tampilkan badge hijau -->
                            <span v-if="questionnaireCompleted" class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 ring-1 ring-inset ring-green-600/20 shadow-sm">
                                Sudah Diselesaikan
                            </span>
                            <!-- v-else: Jika respon masih 0 (false), tampilkan badge merah kelap-kelip -->
                            <span v-else class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 ring-1 ring-inset ring-red-600/10 shadow-sm animate-pulse">
                                Belum Diselesaikan
                            </span>
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Kuesioner Tracer Study</h3>
                        <p class="text-gray-500 mb-8 leading-relaxed">Isi kuesioner resmi dari universitas untuk memberikan umpan balik (<em>feedback</em>) yang berharga bagi pengembangan kurikulum.</p>
                        
                        <!-- 
                          Komponen Link Inertia:
                          - Mengarahkan alumni ke URL '/alumni/kuesioner'
                          - Ditangani di Backend oleh: App\Http\Controllers\Alumni\Kuesioner\KuesionerController.php (method tampilkanKuesioner)
                          - Membuka halaman Vue: resources/js/Pages/Alumni/Kuesioner.vue
                        -->
                        <Link href="/alumni/kuesioner" class="inline-flex items-center justify-center w-full px-6 py-3.5 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 transform transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                            Mulai Isi Kuesioner
                            <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </Link>
                    </div>
                </transition>
            </div>
        </main>
    </div>
</template>

<style scoped>
.fade-down-enter-active {
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-down-enter-from {
    opacity: 0;
    transform: translateY(-30px);
}

.fade-up-1-enter-active {
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s;
}
.fade-up-1-enter-from {
    opacity: 0;
    transform: translateY(40px);
}

.fade-up-2-enter-active {
    transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.25s;
}
.fade-up-2-enter-from {
    opacity: 0;
    transform: translateY(40px);
}
</style>
