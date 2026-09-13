<!--
  Komponen: Banner Judul Bagian Kuesioner Tracer Study Alumni
  File: resources/js/Pages/Alumni/Components/Kuesioner/Banner.vue
  Fungsi: Menampilkan kartu banner hijau judul section aktif dengan indikator "Bagian X dari Y" dan instruksi pengisian.
-->
<script setup>
defineProps({
    currentIndex: {
        type: Number,
        required: true,
    },
    totalSections: {
        type: Number,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: 'Pilih jawaban yang paling sesuai. Kolom bertanda * wajib diisi.',
    },
});
</script>

<template>
    <div class="mb-4 sm:mb-8 text-center bg-[#005B3C] p-4 sm:p-7 md:p-9 rounded-2xl sm:rounded-[2rem] text-white shadow-sm w-full">
        <!-- Chip Bagian X dari Y -->
        <span class="inline-block bg-[#FFD700] text-[#005B3C] text-[10px] sm:text-xs font-black uppercase px-3 py-0.5 sm:px-4 sm:py-1 rounded-full mb-2 sm:mb-3 tracking-wider shadow-xs">
            Bagian {{ currentIndex + 1 }} dari {{ totalSections }}
        </span>
        <!-- Judul Nama Bagian -->
        <h1 class="text-xl sm:text-3xl md:text-4xl font-black tracking-tight mb-1.5 sm:mb-2">
            {{ title }}
        </h1>
        <!-- Petunjuk Singkat -->
        <p class="text-green-100 font-medium text-xs sm:text-sm md:text-base">
            Pilih jawaban yang paling sesuai. Kolom <span class="text-[#FFD700] font-black">*</span> wajib diisi.
        </p>
    </div>
</template>
