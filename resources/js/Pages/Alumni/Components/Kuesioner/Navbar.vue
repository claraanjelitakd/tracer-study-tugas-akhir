<!--
  Komponen: Navbar Kuesioner Tracer Study Alumni
  File: resources/js/Pages/Alumni/Components/Kuesioner/Navbar.vue
  Fungsi: Menampilkan header atas dengan logo resmi UKDW dan tombol kembali ke dashboard alumni.
-->
<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Tracer Study',
    },
    backUrl: {
        type: String,
        default: '/alumni/dashboard',
    },
});
</script>

<template>
    <nav class="bg-[#005B3C] py-2 sm:py-2.5 md:py-3 transition-all">
        <div class="max-w-5xl mx-auto px-3 sm:px-4 flex justify-between items-center">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="bg-white p-1 sm:p-1.5 rounded-lg sm:rounded-xl shadow-xs">
                    <img 
                        src="/uploads/logo/logo-ukdw.png" 
                        onerror="this.src='https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png'" 
                        alt="UKDW Logo" 
                        class="h-7 w-7 sm:h-9 sm:w-9 md:h-10 md:w-10 object-contain"
                    />
                </div>
                <span class="text-white font-black text-lg sm:text-xl md:text-2xl tracking-wide">
                    {{ title }}
                </span>
            </div>
            <!-- Tombol Kembali ke Dashboard Alumni -->
            <Link 
                :href="backUrl" 
                class="px-3 py-1.5 sm:px-4 sm:py-2 md:px-5 md:py-2.5 bg-white/15 text-white hover:bg-white/25 font-bold rounded-xl sm:rounded-2xl transition-colors shadow-xs text-xs sm:text-sm md:text-base"
            >
                Kembali
            </Link>
        </div>
    </nav>
</template>
