<!--
  Komponen: Tombol Navigasi Kuesioner Tracer Study Alumni
  File: resources/js/Pages/Alumni/Components/Kuesioner/Navigasi.vue
  Fungsi: Menampilkan kontrol navigasi:
          1. Desktop: Tombol melayang di pojok kiri bawah (<) dan kanan bawah (>)
          2. Mobile: Fixed bottom bar dengan tombol Kembali (<), indikator bagian, dan tombol Lanjut/Selesai
-->
<script setup>
defineProps({
    activeSectionIndex: {
        type: Number,
        required: true,
    },
    totalSections: {
        type: Number,
        required: true,
    },
    isLastVisibleSection: {
        type: Boolean,
        default: false,
    },
    isProcessing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['prev']);
</script>

<template>
    <div>
        <!-- =============================================================================================== -->
        <!-- TOMBOL NAVIGASI DESKTOP / PC (Melayang di kiri & kanan bawah)                                   -->
        <!-- =============================================================================================== -->
        <!-- 1. Tombol Kembali (<) Desktop -->
        <button 
            type="button"
            @click="emit('prev')"
            :disabled="activeSectionIndex === 0"
            class="hidden md:flex fixed left-8 bottom-8 z-40 w-14 h-14 rounded-full justify-center items-center bg-[#FFD700] text-[#005B3C] shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all disabled:opacity-0 disabled:pointer-events-none group border-0 cursor-pointer"
            title="Kembali ke Bagian Sebelumnya"
        >
            <svg class="w-7 h-7 pr-0.5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <!-- 2. Tombol Lanjut (>) / Selesai Desktop -->
        <button 
            type="submit"
            :disabled="isProcessing"
            class="hidden md:flex fixed right-8 bottom-8 z-40 w-14 h-14 rounded-full justify-center items-center bg-[#005B3C] text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed group border-0 cursor-pointer"
            :title="isLastVisibleSection ? 'Selesai & Simpan Seluruh Kuesioner' : 'Lanjutkan ke Bagian Berikutnya'"
        >
            <!-- Ikon Panah Kanan > jika belum selesai -->
            <svg v-if="!isLastVisibleSection" class="w-7 h-7 pl-0.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M9 5l7 7-7 7"></path>
            </svg>
            <!-- Ikon Centang jika sudah di bagian terakhir -->
            <svg v-else class="w-7 h-7 pl-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </button>

        <!-- =============================================================================================== -->
        <!-- BOTTOM BAR NAVIGASI MOBILE / HP (Bilah tetap di bawah layar smartphone)                         -->
        <!-- =============================================================================================== -->
        <div class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 px-3 py-2 flex items-center justify-between gap-2 shadow-[0_-4px_16px_rgba(0,0,0,0.08)]">
            <!-- 1. Tombol Kembali Mobile (<) -->
            <button 
                type="button"
                @click="emit('prev')"
                :disabled="activeSectionIndex === 0"
                class="px-3.5 py-2 rounded-xl flex items-center gap-1.5 bg-[#FFD700] text-[#005B3C] font-black text-xs shadow-xs active:scale-95 transition-all disabled:opacity-0 disabled:pointer-events-none cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span>Kembali</span>
            </button>

            <!-- 2. Indikator Bagian Mobile di Tengah -->
            <span class="text-[11px] font-bold text-gray-500">
                Bagian {{ activeSectionIndex + 1 }} / {{ totalSections }}
            </span>

            <!-- 3. Tombol Lanjutkan / Selesai Mobile (>) -->
            <button 
                type="submit"
                :disabled="isProcessing"
                class="py-2 px-4 rounded-xl flex items-center justify-center gap-1.5 bg-[#005B3C] text-white font-black text-xs shadow-sm active:scale-95 transition-all disabled:opacity-50 cursor-pointer"
            >
                <span v-if="!isLastVisibleSection">Lanjut</span>
                <span v-else>Selesai</span>
                <svg v-if="!isLastVisibleSection" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M9 5l7 7-7 7"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </button>
        </div>
    </div>
</template>
