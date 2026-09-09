<!--
  Komponen Navigasi Terpadu Superadmin (Navbar.vue)
  
  Fungsi:
  Navbar tunggal resmi SuperAdmin yang bersih, minimalis, dan profesional (mengikuti desain Alumni).
-->
<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();

// Ambil user dari props atau dari shared Inertia auth state
const currentUser = computed(() => {
    return props.user?.name ? props.user : (page.props.auth?.user || { name: 'Super Administrator' });
});

// Deteksi menu aktif
const isDashboardActive = computed(() => {
    return page.url.startsWith('/superadmin/dashboard');
});

const isPertanyaanActive = computed(() => {
    return page.url.startsWith('/superadmin/pertanyaan');
});

// Logout dengan konfirmasi SweetAlert2
const handleLogout = () => {
    Swal.fire({
        title: 'Konfirmasi Keluar',
        text: 'Apakah Anda yakin ingin keluar dari sesi Super Admin?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#005B3C',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            router.post('/logout');
        }
    });
};
</script>

<template>
    <nav class="bg-white/90 backdrop-blur-md shadow-xs border-b border-gray-100 sticky top-0 z-50 transition-all">
        <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Kiri: Brand & Logo -->
                <div class="flex items-center space-x-6">
                    <Link href="/superadmin/dashboard" class="flex items-center space-x-3 group">
                        <img src="/uploads/landing/2.png" alt="Logo UKDW" class="h-9 w-9 object-contain" onerror="this.style.display='none'" />
                        <div>
                            <span class="text-gray-900 font-extrabold text-base sm:text-lg tracking-tight group-hover:text-[#005B3C] transition-colors">
                                Tracer Study UKDW
                            </span>
                            <span class="hidden sm:inline-block ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-[#005B3C] border border-emerald-200/80">
                                Super Admin
                            </span>
                        </div>
                    </Link>

                    <!-- Navigasi Menu Desktop -->
                    <div class="hidden md:flex items-center space-x-1 pl-4 border-l border-gray-200">
                        <Link 
                            href="/superadmin/dashboard" 
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                            :class="[
                                isDashboardActive
                                    ? 'text-[#005B3C] bg-emerald-50/70 font-bold'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            Dashboard
                        </Link>
                        <Link 
                            href="/superadmin/pertanyaan" 
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                            :class="[
                                isPertanyaanActive
                                    ? 'text-[#005B3C] bg-emerald-50/70 font-bold'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            Kelola Kuesioner
                        </Link>
                    </div>
                </div>

                <!-- Kanan: Profil User & Logout -->
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:block text-right">
                        <span class="text-gray-900 font-bold text-sm block">{{ currentUser.name }}</span>
                    </div>

                    <button 
                        @click="handleLogout" 
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-red-600 hover:bg-red-50 border border-gray-200 rounded-full transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span>Keluar</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Subnav Mobile -->
        <div class="md:hidden bg-gray-50/90 px-4 py-2.5 border-t border-gray-100 flex items-center justify-around text-xs">
            <Link 
                href="/superadmin/dashboard" 
                class="px-3 py-1.5 font-bold rounded-lg transition-colors"
                :class="isDashboardActive ? 'text-[#005B3C] bg-white shadow-2xs' : 'text-gray-600'"
            >
                Dashboard
            </Link>
            <Link 
                href="/superadmin/pertanyaan" 
                class="px-3 py-1.5 font-bold rounded-lg transition-colors"
                :class="isPertanyaanActive ? 'text-[#005B3C] bg-white shadow-2xs' : 'text-gray-600'"
            >
                Kelola Kuesioner
            </Link>
        </div>
    </nav>
</template>
