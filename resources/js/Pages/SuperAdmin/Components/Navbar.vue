<!--
  Komponen Navigasi Terpadu Super Admin (Navbar.vue)
  File: resources/js/Pages/SuperAdmin/Components/Navbar.vue
  
  Desain Mengikuti Estetika Modul Alumni:
  Bersih, profesional, minimalis, dan elegan tanpa border tebal kaku.
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

const currentUser = computed(() => {
    return props.user?.name ? props.user : (page.props.auth?.user || { name: 'Super Administrator' });
});

const isDashboardActive = computed(() => {
    return page.url === '/superadmin' || page.url.startsWith('/superadmin/dashboard');
});

const isPertanyaanActive = computed(() => {
    return page.url.startsWith('/superadmin/pertanyaan');
});

const isSectionsActive = computed(() => {
    return page.url.startsWith('/superadmin/sections');
});

const isAlumniActive = computed(() => {
    return page.url.startsWith('/superadmin/alumni');
});

const handleLogout = () => {
    Swal.fire({
        title: 'Konfirmasi Keluar',
        text: 'Apakah Anda yakin ingin keluar dari sesi Super Admin?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0D542B',
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
    <nav class="bg-white/95 shadow-xs border-b border-gray-100 sticky top-0 z-50 transition-all">
        <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Kiri: Brand & Logo -->
                <div class="flex items-center space-x-6">
                    <Link href="/superadmin/dashboard" class="flex items-center space-x-3 group">
                        <img src="/uploads/landing/2.png" alt="Logo UKDW" class="h-9 w-9 object-contain" onerror="this.style.display='none'" />
                        <div>
                            <span class="text-gray-900 font-extrabold text-base sm:text-lg tracking-tight group-hover:text-[#0D542B] transition-colors">
                                Tracer Study UKDW
                            </span>
                            <span class="hidden sm:inline-block ml-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-[#0D542B]">
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
                                    ? 'text-[#0D542B] bg-gray-100 font-bold'
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
                                    ? 'text-[#0D542B] bg-gray-100 font-bold'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            Kelola Kuesioner
                        </Link>
                        <Link 
                            href="/superadmin/sections" 
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                            :class="[
                                isSectionsActive
                                    ? 'text-[#0D542B] bg-gray-100 font-bold'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            Kelola Section
                        </Link>
                        <Link 
                            href="/superadmin/alumni" 
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition-all"
                            :class="[
                                isAlumniActive
                                    ? 'text-[#0D542B] bg-gray-100 font-bold'
                                    : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                            ]"
                        >
                            Data Alumni
                        </Link>
                    </div>
                </div>

                <!-- Kanan: Profil User & Logout -->
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:block text-right">
                        <span class="text-gray-900 font-bold text-sm block">{{ currentUser.name }}</span>
                        <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider block">Administrator</span>
                    </div>

                    <button 
                        @click="handleLogout" 
                        class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 border border-gray-200 rounded-full transition-all cursor-pointer shadow-2xs"
                    >
                        Keluar
                    </button>
                </div>
            </div>
        </div>

        <!-- Subnav Mobile -->
        <div class="md:hidden bg-gray-50/90 px-4 py-2.5 border-t border-gray-100 flex items-center justify-around text-xs">
            <Link 
                href="/superadmin/dashboard" 
                class="px-2.5 py-1.5 font-bold rounded-lg transition-colors"
                :class="isDashboardActive ? 'text-[#005B3C] bg-white shadow-2xs' : 'text-gray-600'"
            >
                Dashboard
            </Link>
            <Link 
                href="/superadmin/pertanyaan" 
                class="px-2.5 py-1.5 font-bold rounded-lg transition-colors"
                :class="isPertanyaanActive ? 'text-[#005B3C] bg-white shadow-2xs' : 'text-gray-600'"
            >
                Kuesioner
            </Link>
            <Link 
                href="/superadmin/sections" 
                class="px-2.5 py-1.5 font-bold rounded-lg transition-colors"
                :class="isSectionsActive ? 'text-[#005B3C] bg-white shadow-2xs' : 'text-gray-600'"
            >
                Section
            </Link>
            <Link 
                href="/superadmin/alumni" 
                class="px-2.5 py-1.5 font-bold rounded-lg transition-colors"
                :class="isAlumniActive ? 'text-[#005B3C] bg-white shadow-2xs' : 'text-gray-600'"
            >
                Data Alumni
            </Link>
        </div>
    </nav>
</template>
