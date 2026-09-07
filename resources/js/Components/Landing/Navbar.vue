<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const isOpen = ref(false);

const getDashboardUrl = (role) => {
    switch(role) {
        case 'superadmin': return '/superadmin/dashboard';
        case 'admin_biro3': return '/biro3/dashboard';
        case 'admin_prodi': return '/prodi/dashboard';
        case 'alumni': return '/alumni/dashboard';
        default: return '/';
    }
};
</script>

<template>
    <nav class="bg-white sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Branding -->
                <div class="flex items-center">
                    <Link href="/" class="flex items-center space-x-2">
                        <div class="flex-shrink-0 flex flex-col justify-center">
                            <span class="text-xl font-bold text-green-700 leading-none">UKDW</span>
                            <span class="text-xs text-gray-500 font-medium tracking-wide">Universitas Kristen Duta Wacana</span>
                        </div>
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden sm:flex sm:items-center sm:space-x-8">
                    <Link href="/" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-semibold transition-all duration-300 hover:scale-105">Beranda</Link>
                    <a href="#tentang" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-semibold transition-all duration-300 hover:scale-105">Tentang</a>
                    <a href="#alumni" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-semibold transition-all duration-300 hover:scale-105">Alumni</a>
                    <a href="#tracer-study" class="text-gray-600 hover:text-green-600 px-3 py-2 rounded-md text-sm font-semibold transition-all duration-300 hover:scale-105">Tracer Study</a>
                    
                    <Link
                        v-if="!$page.props.auth?.user"
                        href="/login"
                        class="bg-green-600 hover:bg-green-500 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5 active:scale-95"
                    >
                        Masuk
                    </Link>
                    <Link
                        v-else
                        :href="getDashboardUrl($page.props.auth.user.role)"
                        class="bg-green-600 hover:bg-green-500 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all duration-300 shadow-md hover:shadow-lg hover:shadow-green-500/30 hover:-translate-y-0.5 active:scale-95"
                    >
                        Dashboard
                    </Link>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button @click="isOpen = !isOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div v-show="isOpen" class="sm:hidden border-t border-gray-100 bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link href="/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-gray-50">Beranda</Link>
                <a href="#tentang" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-gray-50">Tentang</a>
                <a href="#alumni" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-gray-50">Alumni</a>
                <a href="#tracer-study" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-green-600 hover:bg-gray-50">Tracer Study</a>
                
                <Link
                    v-if="!$page.props.auth?.user"
                    href="/login"
                    class="block w-full text-center mt-4 bg-green-600 text-white px-3 py-2 rounded-md text-base font-medium hover:bg-green-700"
                >
                    Masuk
                </Link>
                <Link
                    v-else
                    :href="getDashboardUrl($page.props.auth.user.role)"
                    class="block w-full text-center mt-4 bg-green-600 text-white px-3 py-2 rounded-md text-base font-medium hover:bg-green-700"
                >
                    Dashboard
                </Link>
            </div>
        </div>
    </nav>
</template>
