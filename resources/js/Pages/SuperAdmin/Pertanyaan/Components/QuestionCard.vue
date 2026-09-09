<!--
  Komponen Kartu Pertanyaan (QuestionCard.vue)
  
  Fungsi:
  Menampilkan satu butir kartu pertanyaan secara lengkap:
  1. Identitas pertanyaan (nomor urut, kode unik, prodi sasaran, tipe input, status wajib).
  2. Teks pertanyaan kuesioner.
  3. Daftar pilihan opsi jawaban beserta alur percabangan (*jump logic*).
  4. Kontrol aksi: pindah posisi (naik/turun), tambah/edit/hapus opsi, serta edit/hapus pertanyaan.
-->
<script setup>
import { computed } from 'vue';

const props = defineProps({
    question: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        default: 0,
    },
    totalInActiveSection: {
        type: Number,
        default: 1,
    },
    targetQuestionMap: {
        type: Object,
        default: () => ({}),
    },
    isReordering: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    'editQuestion',
    'deleteQuestion',
    'moveQuestion',
    'addOption',
    'editOption',
    'deleteOption',
]);

// Apakah tipe pertanyaan ini mendukung penambahan opsi pilihan jawaban
const isOptionSupported = computed(() => {
    return ['single_choice', 'multiple_choice', 'rating_5', 'dropdown'].includes(props.question.type);
});

// Format label tipe pertanyaan agar mudah dipahami
const formatQuestionType = (type) => {
    const map = {
        single_choice: 'Pilihan Tunggal (Radio)',
        multiple_choice: 'Pilihan Ganda (Checkbox)',
        text: 'Isian Teks Singkat',
        number: 'Isian Angka',
        multiple_number: 'Isian Nominal / Gaji',
        rating_5: 'Skala Rating (1-5)',
        textarea: 'Uraian / Teks Panjang',
        dropdown: 'Dropdown Pilihan',
        date: 'Tanggal',
    };
    return map[type] || type;
};

// Kelas warna untuk badge tipe pertanyaan
const getTypeBadgeClass = (type) => {
    const map = {
        single_choice: 'bg-emerald-50 text-emerald-800 border-emerald-200',
        multiple_choice: 'bg-blue-50 text-blue-800 border-blue-200',
        text: 'bg-amber-50 text-amber-800 border-amber-200',
        number: 'bg-purple-50 text-purple-800 border-purple-200',
        multiple_number: 'bg-teal-50 text-teal-800 border-teal-200',
        rating_5: 'bg-indigo-50 text-indigo-800 border-indigo-200',
        textarea: 'bg-gray-100 text-gray-800 border-gray-200',
    };
    return map[type] || 'bg-gray-50 text-gray-700 border-gray-200';
};
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/90 hover:border-emerald-500/50 hover:shadow-md transition-all duration-200 overflow-hidden">
        <!-- Header Kartu: Kode, Tipe, Prodi, & Action Buttons -->
        <div class="p-5 sm:p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-3">
            
            <!-- Metadata Pertanyaan -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Badge Nomor & Kode -->
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-[#005B3C] text-white font-black text-xs shadow-2xs">
                    <span>#{{ question.order }}</span>
                    <span class="opacity-60">•</span>
                    <span class="tracking-wide">{{ question.code }}</span>
                </span>

                <!-- Badge Tipe Pertanyaan -->
                <span 
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border"
                    :class="getTypeBadgeClass(question.type)"
                >
                    {{ formatQuestionType(question.type) }}
                </span>

                <!-- Badge Wajib / Opsional -->
                <span 
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold"
                    :class="[
                        question.is_required
                            ? 'bg-rose-50 text-rose-700 border border-rose-200'
                            : 'bg-gray-100 text-gray-600 border border-gray-200'
                    ]"
                >
                    {{ question.is_required ? 'Wajib Diisi' : 'Opsional' }}
                </span>

                <!-- Badge Prodi -->
                <span 
                    v-if="question.prodi" 
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200"
                >
                    Khusus: {{ question.prodi.kode_prodi }}
                </span>
                <span 
                    v-else 
                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium text-gray-500 bg-gray-100/70 border border-gray-200"
                >
                    Semua Prodi
                </span>
            </div>

            <!-- Tombol Pengatur Posisi & Aksi Pertanyaan -->
            <div class="flex items-center gap-1.5 self-end md:self-auto">
                <!-- Pindah Urutan Naik -->
                <button
                    type="button"
                    :disabled="index === 0 || isReordering"
                    @click="emit('moveQuestion', question, 'up')"
                    class="p-2 rounded-lg text-gray-600 hover:text-[#005B3C] hover:bg-emerald-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors border border-gray-200 bg-white shadow-2xs"
                    title="Pindahkan ke atas"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                </button>

                <!-- Pindah Urutan Turun -->
                <button
                    type="button"
                    :disabled="index === totalInActiveSection - 1 || isReordering"
                    @click="emit('moveQuestion', question, 'down')"
                    class="p-2 rounded-lg text-gray-600 hover:text-[#005B3C] hover:bg-emerald-50 disabled:opacity-30 disabled:cursor-not-allowed transition-colors border border-gray-200 bg-white shadow-2xs"
                    title="Pindahkan ke bawah"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div class="h-4 w-px bg-gray-300 mx-1"></div>

                <!-- Tombol Edit Pertanyaan -->
                <button
                    type="button"
                    @click="emit('editQuestion', question)"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 hover:text-[#005B3C] hover:bg-emerald-50 border border-gray-200 bg-white shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </button>

                <!-- Tombol Hapus Pertanyaan -->
                <button
                    type="button"
                    @click="emit('deleteQuestion', question)"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold text-red-600 hover:bg-red-50 border border-red-200 bg-white shadow-2xs transition-colors flex items-center gap-1 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus
                </button>
            </div>
        </div>

        <!-- Isi Teks Pertanyaan -->
        <div class="p-5 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 leading-snug">
                {{ question.question_text }}
            </h3>

            <!-- Bagian Opsi Pilihan Jawaban (Jika Didukung) -->
            <div v-if="isOptionSupported" class="mt-5 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase text-gray-500 tracking-wider">
                            Pilihan Opsi Jawaban ({{ question.options?.length || 0 }})
                        </span>
                    </div>

                    <!-- Tombol Tambah Opsi -->
                    <button
                        type="button"
                        @click="emit('addOption', question)"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold text-[#005B3C] bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors flex items-center gap-1 cursor-pointer shadow-2xs"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Opsi
                    </button>
                </div>

                <!-- Daftar Butir Opsi -->
                <div v-if="question.options && question.options.length > 0" class="space-y-2">
                    <div 
                        v-for="opt in question.options" 
                        :key="opt.id"
                        class="flex items-center justify-between p-3 rounded-xl bg-gray-50/80 hover:bg-gray-100/70 border border-gray-200/60 transition-colors group"
                    >
                        <!-- Kiri: Bulatan/Kotak Simbol, Kode Opsi, & Teks Opsi -->
                        <div class="flex items-center gap-3 min-w-0 pr-2">
                            <div 
                                class="w-4 h-4 shrink-0 flex items-center justify-center border-2"
                                :class="[
                                    question.type === 'multiple_choice' 
                                        ? 'rounded-md border-gray-400 bg-white' 
                                        : 'rounded-full border-gray-400 bg-white'
                                ]"
                            ></div>

                            <span class="text-xs font-mono font-bold text-gray-500 bg-white px-1.5 py-0.5 rounded border border-gray-200 shrink-0">
                                {{ opt.code || '•' }}
                            </span>

                            <span class="text-xs sm:text-sm font-semibold text-gray-800 truncate">
                                {{ opt.option_text }}
                            </span>

                            <!-- Badge Alur Percabangan Jump Logic -->
                            <span 
                                v-if="opt.jump_to" 
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-200 shrink-0 shadow-2xs"
                                :title="targetQuestionMap[opt.jump_to] ? 'Lompat ke: ' + targetQuestionMap[opt.jump_to] : 'Lompat ke ' + opt.jump_to"
                            >
                                <svg class="w-3 h-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                <span>Lompat ke: <strong>{{ opt.jump_to }}</strong></span>
                            </span>
                        </div>

                        <!-- Kanan: Aksi Edit & Hapus Opsi -->
                        <div class="flex items-center gap-1 shrink-0 opacity-80 group-hover:opacity-100 transition-opacity">
                            <button
                                type="button"
                                @click="emit('editOption', question, opt)"
                                class="p-1.5 text-gray-500 hover:text-[#005B3C] hover:bg-white rounded-md transition-colors"
                                title="Edit opsi"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button
                                type="button"
                                @click="emit('deleteOption', opt)"
                                class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-white rounded-md transition-colors"
                                title="Hapus opsi"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-4 bg-gray-50/50 rounded-xl border border-dashed border-gray-200 text-xs text-gray-400">
                    Belum ada pilihan opsi jawaban. Klik <strong class="text-gray-600">"Tambah Opsi"</strong> untuk menambahkan.
                </div>
            </div>
        </div>
    </div>
</template>
