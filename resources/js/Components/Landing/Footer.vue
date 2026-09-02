<script setup>
import { Link } from '@inertiajs/vue3';

const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="md:flex md:items-center md:justify-between">
                
                <div class="flex justify-center md:justify-start mb-6 md:mb-0">
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-green-700 leading-none">UKDW</span>
                        <span class="text-xs text-gray-500 font-medium tracking-wide">Universitas Kristen Duta Wacana</span>
                    </div>
                </div>

                <div class="flex justify-center space-x-6 md:order-2">
                    <Link href="/" class="text-gray-500 hover:text-green-600 text-sm font-medium transition">Beranda</Link>
                    <a href="#tentang" class="text-gray-500 hover:text-green-600 text-sm font-medium transition">Tentang</a>
                    <a href="#alumni" class="text-gray-500 hover:text-green-600 text-sm font-medium transition">Alumni</a>
                    <a href="#tracer-study" class="text-gray-500 hover:text-green-600 text-sm font-medium transition">Tracer Study</a>
                </div>
            </div>
            
            <div class="mt-8 border-t border-gray-100 pt-8 flex flex-col md:flex-row md:items-center md:justify-between">
                <p class="text-center text-sm text-gray-400">
                    &copy; {{ currentYear }} Universitas Kristen Duta Wacana. Hak Cipta Dilindungi.
                </p>
                <p class="text-center text-sm text-gray-400 mt-2 md:mt-0">
                    Sistem Ekosistem Rekam Jejak Alumni (SERU)
                </p>
            </div>
        </div>
    </footer>
</template>
