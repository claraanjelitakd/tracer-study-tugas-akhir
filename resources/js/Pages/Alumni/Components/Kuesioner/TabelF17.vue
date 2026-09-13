<!--
  Komponen: Tabel Evaluasi Kompetensi F17 Tracer Study Alumni
  File: resources/js/Pages/Alumni/Components/Kuesioner/TabelF17.vue
  Fungsi: Menampilkan tabel perbandingan 2 sisi untuk butir F17:
          Kolom A: Kemampuan Diri Saat Lulus
          Tengah: Aspek Kompetensi & Badge Komparasi
          Kolom B: Kontribusi Perguruan Tinggi UKDW
-->
<script setup>
import { computed } from 'vue';

const props = defineProps({
    pairs: {
        type: Array,
        default: () => [],
    },
    form: {
        type: Object,
        required: true,
    },
});

// Hitung berapa aspek F17 yang sudah terisi lengkap (A dan B)
const completedCount = computed(() => {
    return props.pairs.filter(pair => {
        return !!props.form.answers[pair.qA.id] && !!props.form.answers[pair.qB.id];
    }).length;
});

const ratingScores = {
    'Sangat Rendah': 1,
    'Rendah': 2,
    'Cukup': 3,
    'Tinggi': 4,
    'Sangat Tinggi': 5,
};

const getRatingScore = (val) => {
    if (val === undefined || val === null || val === '') return null;
    if (ratingScores[val]) return ratingScores[val];
    const n = Number(val);
    if (!isNaN(n) && n >= 1 && n <= 5) return n;
    return null;
};

// Indikator ringkas perbandingan nilai A (Kemampuan Diri) dan B (Kontribusi Kampus)
const getF17ComparisonBadge = (valA, valB) => {
    const a = getRatingScore(valA);
    const b = getRatingScore(valB);

    if (a !== null && b !== null) {
        if (a > b) {
            return {
                text: `A (${a}) > B (${b})`,
                badgeClass: 'bg-emerald-50 text-emerald-800 border-emerald-200'
            };
        } else if (a === b) {
            return {
                text: `A (${a}) = B (${b})`,
                badgeClass: 'bg-purple-50 text-purple-800 border-purple-200'
            };
        } else {
            return {
                text: `A (${a}) < B (${b})`,
                badgeClass: 'bg-blue-50 text-blue-800 border-blue-200'
            };
        }
    }
    return null;
};
</script>

<template>
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Banner F17 -->
        <div class="bg-white p-4 sm:p-6 md:p-8 rounded-2xl sm:rounded-[2rem] shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-block bg-[#005B3C] text-white text-[10px] sm:text-[11px] font-bold uppercase px-2 py-0.5 rounded-md tracking-wider">
                            Instrumen F17
                        </span>
                        <span class="text-[11px] sm:text-xs text-gray-500 font-medium">Evaluasi Kompetensi</span>
                    </div>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-black text-gray-900 leading-snug">
                        Perbandingan Penguasaan Diri vs Kontribusi Kampus
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                        Bandingkan tingkat kompetensi yang Anda kuasai saat lulus (Kolom A) dengan kontribusi perguruan tinggi UKDW (Kolom B).
                    </p>
                </div>
                <!-- Progress Counter -->
                <div class="flex items-center gap-2.5 sm:gap-3 bg-gray-50 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl shrink-0 border border-gray-200/80 self-start sm:self-auto">
                    <div class="text-left sm:text-right">
                        <div class="text-[10px] sm:text-[11px] font-semibold text-gray-500">Progres Pengisian</div>
                        <div class="text-sm sm:text-base font-black text-[#005B3C]">
                            {{ completedCount }} / {{ pairs.length }} Aspek
                        </div>
                    </div>
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-[#005B3C] text-white flex items-center justify-center font-black text-xs shadow-xs">
                        {{ Math.round((completedCount / (pairs.length || 1)) * 100) }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Komparasi Berdampingan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Petunjuk Geser Horizontal di Mobile -->
            <div class="block md:hidden px-3.5 py-2 bg-emerald-50/80 text-[#005B3C] text-xs font-bold border-b border-emerald-100 flex items-center justify-between">
                <span class="flex items-center gap-1.5">
                    <span>👉</span>
                    <span>Geser tabel ke samping untuk melihat Kolom B</span>
                </span>
                <span class="text-[10px] bg-emerald-200/60 px-2 py-0.5 rounded-full font-mono">Scroll &rarr;</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[840px] border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <!-- Header Kolom A (Kemampuan Diri) -->
                            <th class="py-4 px-4 w-[330px] bg-emerald-50/70 text-left border-r border-gray-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-6 h-6 rounded-md bg-[#005B3C] text-white flex items-center justify-center text-xs font-black">A</span>
                                    <span class="font-black text-emerald-950 text-sm">Kemampuan Diri Anda</span>
                                </div>
                                <div class="text-xs text-emerald-800 font-medium mb-3">
                                    Tingkat kompetensi yang Anda kuasai saat lulus
                                </div>
                                <div class="flex items-center justify-between max-w-[240px] mx-auto px-1 text-xs font-bold text-emerald-900">
                                    <span class="text-[11px] text-emerald-700 font-medium">1 (Rendah)</span>
                                    <div class="flex gap-4">
                                        <span class="w-6 text-center">2</span>
                                        <span class="w-6 text-center">3</span>
                                        <span class="w-6 text-center">4</span>
                                    </div>
                                    <span class="text-[11px] text-emerald-700 font-medium">5 (Tinggi)</span>
                                </div>
                            </th>

                            <!-- Header Tengah (Aspek Kompetensi) -->
                            <th class="py-4 px-4 text-center bg-gray-50 text-gray-800 font-black text-xs uppercase tracking-wider">
                                Aspek Kompetensi
                            </th>

                            <!-- Header Kolom B (Kontribusi Kampus) -->
                            <th class="py-4 px-4 w-[330px] bg-blue-50/70 text-left border-l border-gray-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-6 h-6 rounded-md bg-blue-700 text-white flex items-center justify-center text-xs font-black">B</span>
                                    <span class="font-black text-blue-950 text-sm">Kontribusi Kampus UKDW</span>
                                </div>
                                <div class="text-xs text-blue-800 font-medium mb-3">
                                    Peran kurikulum & dosen UKDW membekali Anda
                                </div>
                                <div class="flex items-center justify-between max-w-[240px] mx-auto px-1 text-xs font-bold text-blue-900">
                                    <span class="text-[11px] text-blue-700 font-medium">1 (Rendah)</span>
                                    <div class="flex gap-4">
                                        <span class="w-6 text-center">2</span>
                                        <span class="w-6 text-center">3</span>
                                        <span class="w-6 text-center">4</span>
                                    </div>
                                    <span class="text-[11px] text-blue-700 font-medium">5 (Tinggi)</span>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr 
                            v-for="pair in pairs" 
                            :key="'pair_' + pair.aspectNumber" 
                            class="transition-colors hover:bg-gray-50/60"
                            :class="{'bg-gray-50/30': pair.aspectNumber % 2 === 0}"
                        >
                            <!-- Pilihan Kolom A -->
                            <td class="py-4 px-4 bg-emerald-50/20 border-r border-gray-200">
                                <div class="flex items-center justify-between max-w-[240px] mx-auto">
                                    <label 
                                        v-for="score in 5" 
                                        :key="score" 
                                        class="cursor-pointer select-none"
                                        :title="'Kolom A Skor: ' + score"
                                    >
                                        <input 
                                            type="radio" 
                                            :name="'question_' + pair.qA.id" 
                                            :value="score" 
                                            v-model="form.answers[pair.qA.id]" 
                                            :required="pair.qA.is_required" 
                                            class="sr-only"
                                        >
                                        <div 
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm transition-all"
                                            :class="[
                                                Number(form.answers[pair.qA.id]) === score
                                                    ? 'bg-[#005B3C] text-white ring-2 ring-offset-1 ring-emerald-500 shadow-sm scale-105 font-black'
                                                    : 'bg-white text-gray-700 border border-emerald-300 hover:bg-emerald-100/70 hover:border-emerald-400'
                                            ]"
                                        >
                                            {{ score }}
                                        </div>
                                    </label>
                                </div>
                            </td>

                            <!-- Aspek Kompetensi (Tengah) -->
                            <td class="py-4 px-5 text-center">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <span class="font-bold text-gray-900 text-xs sm:text-sm md:text-base leading-snug">
                                        {{ pair.aspectNumber }}. {{ pair.aspectName }}
                                    </span>
                                    <!-- Badge perbandingan ringkas (A > B, A = B, A < B) jika keduanya terisi -->
                                    <span 
                                        v-if="getF17ComparisonBadge(form.answers[pair.qA.id], form.answers[pair.qB.id])"
                                        class="inline-block px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-semibold border"
                                        :class="getF17ComparisonBadge(form.answers[pair.qA.id], form.answers[pair.qB.id]).badgeClass"
                                    >
                                        {{ getF17ComparisonBadge(form.answers[pair.qA.id], form.answers[pair.qB.id]).text }}
                                    </span>
                                </div>
                            </td>

                            <!-- Pilihan Kolom B -->
                            <td class="py-4 px-4 bg-blue-50/20 border-l border-gray-200">
                                <div class="flex items-center justify-between max-w-[240px] mx-auto">
                                    <label 
                                        v-for="score in 5" 
                                        :key="score" 
                                        class="cursor-pointer select-none"
                                        :title="'Kolom B Skor: ' + score"
                                    >
                                        <input 
                                            type="radio" 
                                            :name="'question_' + pair.qB.id" 
                                            :value="score" 
                                            v-model="form.answers[pair.qB.id]" 
                                            :required="pair.qB.is_required" 
                                            class="sr-only"
                                        >
                                        <div 
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm transition-all"
                                            :class="[
                                                Number(form.answers[pair.qB.id]) === score
                                                    ? 'bg-blue-600 text-white ring-2 ring-offset-1 ring-blue-500 shadow-sm scale-105 font-black'
                                                    : 'bg-white text-gray-700 border border-blue-300 hover:bg-blue-100/70 hover:border-blue-400'
                                            ]"
                                        >
                                            {{ score }}
                                        </div>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
