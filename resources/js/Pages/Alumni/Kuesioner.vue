<!--
  Halaman Kuesioner Tracer Study Alumni
  Fungsi: Menampilkan kuesioner tracer study per section dengan stepper navigasi interaktif.
  Fitur: 
  - Stepper tahapan dinamis, validasi form per step, navigasi responsif tanpa bayangan tebal.
  - Percabangan (Jump Logic) berbasis `question_options.jump_to` murni dari database.
  - Struktur Form Inertia (`form.answers`):
    * Kunci `[question_id]`: Menyimpan jawaban utama (array untuk multiple_choice, object untuk radio_input, teks/angka untuk single_choice/number).
    * Kunci `[question_id + '_custom']`: Menyimpan teks uraian bebas jika alumni memilih opsi 'Lainnya / Tuliskan'.
  - Backend Sinkronisasi:
    * Menerima payload Inertia dan menyimpannya secara rapi ke `responses` (Dual Storage: `answer_text` untuk teks bersih siap ekspor Excel & `answer_json` untuk array murni).
    * Data profil (F1..F2H) terintegrasi otomatis dan tidak perlu diisi ulang oleh alumni.
-->
<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import SearchableSelect from '@/components/form/searchable-select.vue';

// Menerima data kuesioner dari Backend yang sudah diproses
const props = defineProps({
    questionnaire: Object,
    initialAnswers: Object,
    error: String,
});

// Storage keys untuk persistensi sesi jika halaman direfresh
const SECTION_STORAGE_KEY = 'tracerstudy_alumni_kuesioner_section';
const ANSWERS_STORAGE_KEY = 'tracerstudy_alumni_kuesioner_answers';

// Inisialisasi jawaban: gabungkan data backend dengan draft lokal jika pernah direfresh
const getInitialAnswers = () => {
    let base = JSON.parse(JSON.stringify(props.initialAnswers || {}));
    if (typeof window !== 'undefined') {
        try {
            const cached = localStorage.getItem(ANSWERS_STORAGE_KEY);
            if (cached) {
                const parsed = JSON.parse(cached);
                base = { ...base, ...parsed };
            }
        } catch (e) {
            console.error('Gagal membaca cache jawaban:', e);
        }
    }

    // Normalisasi struktur radio_input agar inputs per-opsi selalu terisolasi mandiri
    if (props.questionnaire?.sections) {
        props.questionnaire.sections.forEach(sec => {
            sec.questions?.forEach(q => {
                if (['radio_input', 'radio_text'].includes(q.type)) {
                    if (!base[q.id] || typeof base[q.id] !== 'object') {
                        base[q.id] = { selected: '', input: '', inputs: {} };
                    } else {
                        if (!base[q.id].inputs || typeof base[q.id].inputs !== 'object') {
                            base[q.id].inputs = {};
                        }
                        // Jika dari cache lama ada input string tunggal tapi inputs per-opsi belum ada
                        if (base[q.id].selected && base[q.id].input !== undefined && q.options) {
                            const matchedOpt = q.options.find(o => o.option_text === base[q.id].selected);
                            if (matchedOpt && !base[q.id].inputs[matchedOpt.id]) {
                                base[q.id].inputs[matchedOpt.id] = base[q.id].input;
                            }
                        }
                        // Pastikan setiap opsi memiliki key tersendiri dan tidak kosong null
                        if (q.options) {
                            q.options.forEach(o => {
                                if (base[q.id].inputs[o.id] === undefined) {
                                    base[q.id].inputs[o.id] = '';
                                }
                            });
                        }
                    }
                }
            });
        });
    }

    return base;
};

// Inisialisasi section aktif: baca dari query URL atau localStorage
const getInitialSectionIndex = () => {
    if (typeof window !== 'undefined') {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const secParam = urlParams.get('sec');
            if (secParam !== null) {
                const parsed = parseInt(secParam, 10);
                if (!isNaN(parsed) && parsed >= 0) return parsed;
            }
            const cachedSec = localStorage.getItem(SECTION_STORAGE_KEY);
            if (cachedSec !== null) {
                const parsed = parseInt(cachedSec, 10);
                if (!isNaN(parsed) && parsed >= 0) return parsed;
            }
        } catch (e) {
            console.error('Gagal membaca cache section:', e);
        }
    }
    return 0;
};

// Helper getter nilai input untuk pertanyaan bertipe radio_input secara independen per opsi
const getRadioInputVal = (qId, optId) => {
    if (!form.answers[qId] || typeof form.answers[qId] !== 'object') return '';
    if (!form.answers[qId].inputs) return '';
    return form.answers[qId].inputs[optId] ?? '';
};

// Helper setter nilai input untuk pertanyaan bertipe radio_input secara independen per opsi
const setRadioInputVal = (qId, optId, val) => {
    if (!form.answers[qId] || typeof form.answers[qId] !== 'object') {
        form.answers[qId] = { selected: '', input: '', inputs: {} };
    }
    if (!form.answers[qId].inputs) {
        form.answers[qId].inputs = {};
    }
    form.answers[qId].inputs[optId] = val;
    form.answers[qId].input = val;
};

// Handler saat opsi radio_input dipilih/berganti: update input aktif tanpa merusak input opsi lain
const handleRadioOptionSelect = (qId, optId) => {
    if (!form.answers[qId] || typeof form.answers[qId] !== 'object') {
        form.answers[qId] = { selected: '', input: '', inputs: {} };
    }
    if (!form.answers[qId].inputs) {
        form.answers[qId].inputs = {};
    }
    form.answers[qId].input = form.answers[qId].inputs[optId] ?? '';
};

// Menyimpan index section kuesioner yang sedang aktif
const activeSectionIndex = ref(getInitialSectionIndex());

// Inisialisasi data form menggunakan useForm bawaan Inertia
const form = useForm({ answers: getInitialAnswers() });

// Validasi batas index section saat data kuesioner tersedia
onMounted(() => {
    if (props.questionnaire?.sections?.length) {
        if (activeSectionIndex.value >= props.questionnaire.sections.length) {
            activeSectionIndex.value = 0;
        }
    }
});

// Simpan perpindahan section ke URL & localStorage agar saat direfresh tidak pindah tampilan
watch(activeSectionIndex, (newIdx) => {
    if (typeof window !== 'undefined') {
        localStorage.setItem(SECTION_STORAGE_KEY, newIdx.toString());
        const url = new URL(window.location.href);
        url.searchParams.set('sec', newIdx.toString());
        window.history.replaceState({}, '', url.toString());
    }
});

// Simpan draft jawaban ke localStorage secara real-time agar jika direfresh data tidak hilang
let draftTimer = null;
watch(() => form.answers, (newAnswers) => {
    if (typeof window !== 'undefined') {
        clearTimeout(draftTimer);
        draftTimer = setTimeout(() => {
            try {
                localStorage.setItem(ANSWERS_STORAGE_KEY, JSON.stringify(newAnswers));
            } catch (e) {
                console.error('Gagal menyimpan cache jawaban:', e);
            }
        }, 250);
    }
}, { deep: true });

// Daftar urutan seluruh pertanyaan di kuesioner (flattened secara terurut)
const allQuestionsList = computed(() => {
    if (!props.questionnaire?.sections) return [];
    const list = [];
    props.questionnaire.sections.forEach(sec => {
        if (sec.questions) {
            sec.questions.forEach(q => {
                list.push(q);
            });
        }
    });
    return list;
});

// Peta index urutan pertanyaan berdasarkan kode pertanyaan
const questionIndexMap = computed(() => {
    const map = {};
    allQuestionsList.value.forEach((q, idx) => {
        map[q.code] = idx;
    });
    return map;
});

// Hitung pertanyaan mana saja yang dilewati (skipped) karena branching jump_to
const skippedQuestionIds = computed(() => {
    const skipped = new Set();
    const questions = allQuestionsList.value;
    const indexMap = questionIndexMap.value;

    questions.forEach((q, currentIndex) => {
        if (skipped.has(q.id)) return;

        const currentAnswer = form.answers[q.id];
        if (!currentAnswer) return;

        let selectedValue = currentAnswer;
        if (typeof currentAnswer === 'object' && currentAnswer !== null && currentAnswer.selected !== undefined) {
            selectedValue = currentAnswer.selected;
        }
        if (!selectedValue) return;

        let targetCode = null;

        // Ambil target lompatan murni dari opsi yang dipilih (QuestionOption.jump_to)
        if (q.options && q.options.length > 0) {
            const selectedOpt = q.options.find(opt => opt.option_text === selectedValue || opt.code === selectedValue);
            if (selectedOpt && selectedOpt.jump_to) {
                targetCode = selectedOpt.jump_to;
            }
        }

        // Jika ada target jump, lewati semua pertanyaan antara pertanyaan saat ini dan target
        if (targetCode) {
            let targetIndex = indexMap[targetCode];
            if (targetIndex === undefined) {
                const matchedIndex = questions.findIndex((item, idx) => idx > currentIndex && (item.code === targetCode || item.code.startsWith(targetCode + '-')));
                if (matchedIndex !== -1) {
                    targetIndex = matchedIndex;
                }
            }

            if (targetIndex !== undefined && targetIndex > currentIndex) {
                for (let i = currentIndex + 1; i < targetIndex; i++) {
                    skipped.add(questions[i].id);
                }
            }
        }
    });

    return skipped;
});

const isQuestionVisible = (questionId) => {
    return !skippedQuestionIds.value.has(questionId);
};

// Mengecek apakah suatu section memiliki pertanyaan yang terlihat
const isSectionVisible = (section) => {
    if (!section || !section.questions || section.questions.length === 0) return false;
    return section.questions.some(q => isQuestionVisible(q.id));
};

// Section yang sedang aktif saat ini
const currentSection = computed(() => {
    return props.questionnaire?.sections?.[activeSectionIndex.value] || null;
});

// Menentukan apakah saat ini berada di section yang dapat diisi terakhir
const isLastVisibleSection = computed(() => {
    if (!props.questionnaire?.sections) return true;
    for (let i = activeSectionIndex.value + 1; i < props.questionnaire.sections.length; i++) {
        if (isSectionVisible(props.questionnaire.sections[i])) {
            return false;
        }
    }
    return true;
});

// Deteksi khusus Section F17 (Evaluasi Kompetensi Dual Matrix A vs B)
const isF17Section = computed(() => {
    return currentSection.value?.questions?.some(q => q.code && q.code.startsWith('F17-')) || false;
});

// Helper pembersih nama aspek kompetensi
const getCleanAspectName = (text) => {
    if (!text) return '';
    if (text.includes('—')) return text.split('—')[0].trim();
    if (text.includes('-')) return text.split('-')[0].trim();
    return text;
};

// Pasangan pertanyaan F17 berdampingan (A: Kompetensi yang dikuasai vs B: Kontribusi PT)
const f17AspectPairs = computed(() => {
    if (!isF17Section.value || !currentSection.value?.questions) return [];
    const questions = currentSection.value.questions;
    const pairs = [];

    for (let i = 0; i < questions.length; i += 2) {
        const qA = questions[i];
        const qB = questions[i + 1];
        if (!qA || !qB) continue;

        pairs.push({
            aspectNumber: Math.floor(i / 2) + 1,
            aspectName: getCleanAspectName(qA.question_text),
            qA,
            qB,
        });
    }
    return pairs;
});

// Hitung berapa aspek F17 yang sudah terisi lengkap (A dan B)
const f17CompletedCount = computed(() => {
    if (!isF17Section.value) return 0;
    return f17AspectPairs.value.filter(pair => {
        return !!form.answers[pair.qA.id] && !!form.answers[pair.qB.id];
    }).length;
});

// Kelompokkan pertanyaan dalam section: jika ada pertanyaan number/text pendek berurutan seperti F6 dan F7, jadikan pasangan side-by-side
const groupedQuestions = computed(() => {
    if (!currentSection.value?.questions) return [];
    const questions = currentSection.value.questions;
    const groups = [];
    let i = 0;

    while (i < questions.length) {
        const q = questions[i];
        const nextQ = questions[i + 1];

        // Khusus F6 dan F7: buat berdampingan kanan-kiri (grid 2 kolom)
        if (q.code === 'F6' && nextQ && nextQ.code === 'F7') {
            groups.push({
                type: 'pair',
                items: [q, nextQ]
            });
            i += 2;
        } else {
            groups.push({
                type: 'single',
                items: [q]
            });
            i += 1;
        }
    }
    return groups;
});

// Mencegah karakter aneh pada input angka
const filterNumberInput = (event) => {
    if (['e', 'E', '+', '-', '.'].includes(event.key)) {
        event.preventDefault();
    }
};

// Navigasi Section: Berpindah ke section tertentu via stepper
const setSection = (index) => {
    activeSectionIndex.value = index;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Navigasi Section: Kembali ke section terlihat sebelumnya
const prevSection = () => {
    if (activeSectionIndex.value > 0) {
        let prevIdx = activeSectionIndex.value - 1;
        while (prevIdx > 0 && !isSectionVisible(props.questionnaire.sections[prevIdx])) {
            prevIdx--;
        }
        activeSectionIndex.value = prevIdx;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// Navigasi Section: Lanjut atau Simpan Form
const handleNextOrSubmit = () => {
    if (isLastVisibleSection.value) {
        // Halaman terakhir: Simpan seluruh jawaban
        form.post('/alumni/kuesioner', {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (typeof window !== 'undefined') {
                    localStorage.removeItem(ANSWERS_STORAGE_KEY);
                    localStorage.removeItem(SECTION_STORAGE_KEY);
                }
            }
        });
    } else {
        // Simpan progress parsial ke backend dan lompat ke section berikutnya yang terlihat
        form.post('/alumni/kuesioner', {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                let nextIdx = activeSectionIndex.value + 1;
                while (nextIdx < props.questionnaire.sections.length && !isSectionVisible(props.questionnaire.sections[nextIdx])) {
                    nextIdx++;
                }

                if (nextIdx < props.questionnaire.sections.length) {
                    activeSectionIndex.value = nextIdx;
                }
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }
};
</script>

<template>
    <Head title="Kuesioner Tracer Study" />

    <div class="min-h-screen bg-[#E8F5E9] font-sans text-gray-900 antialiased flex flex-col relative pb-32">
        
        <!-- Top Navigation -->
        <nav class="bg-[#005B3C] sticky top-0 z-50 py-3">
            <div class="max-w-5xl mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-1.5 rounded-xl">
                        <img src="/uploads/logo/logo-ukdw.png" onerror="this.src='https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png'" alt="UKDW Logo" class="h-10 w-10 object-contain">
                    </div>
                    <span class="text-white font-black text-2xl tracking-wide">Tracer Study</span>
                </div>
                <Link href="/alumni/dashboard" class="px-5 py-2.5 bg-white/15 text-white hover:bg-white/25 font-bold rounded-2xl transition-colors shadow-xs">
                    Kembali
                </Link>
            </div>
        </nav>

        <!-- Stepper Navigasi Tahapan Section -->
        <div v-if="questionnaire?.sections" class="w-full bg-white shadow-xs border-b border-gray-100 mb-8 overflow-x-auto pb-3 pt-4 custom-scrollbar">
            <div class="flex items-center justify-between px-4 md:px-8 min-w-max max-w-5xl mx-auto">
                <template v-for="(section, index) in questionnaire.sections" :key="section.id">
                    
                    <!-- Lingkaran Tahapan -->
                    <div 
                        class="flex flex-col relative items-center justify-center cursor-pointer group px-2 md:px-3 py-1 transition-all duration-200"
                        @click="setSection(index)"
                    >
                        <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-full font-black text-sm md:text-lg transition-all duration-200 z-10 shadow-xs"
                            :class="[
                                activeSectionIndex === index ? 'bg-[#FFD700] text-[#005B3C] shadow-md scale-110' : 
                                (index < activeSectionIndex ? 'bg-[#005B3C] text-white shadow-xs' : 
                                'bg-gray-100 text-gray-400')
                            ]"
                        >
                            <svg v-if="index < activeSectionIndex" class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        
                        <!-- Judul Tahapan -->
                        <span class="text-[11px] md:text-xs font-black mt-2 text-center w-24 md:w-28 leading-tight transition-colors line-clamp-2"
                            :class="activeSectionIndex === index ? 'text-[#005B3C]' : 'text-gray-400 group-hover:text-gray-600'"
                        >
                            {{ section.title }}
                        </span>
                    </div>
                    
                    <!-- Garis Penghubung antar Lingkaran -->
                    <div v-if="index < questionnaire.sections.length - 1" class="flex-1 h-1.5 md:h-2 rounded-full transition-colors duration-300 mx-1 md:mx-2 min-w-[20px]" :class="index < activeSectionIndex ? 'bg-[#005B3C]' : 'bg-gray-200'"></div>
                </template>
            </div>
        </div>

        <!-- Main Form Content Area -->
        <main class="flex-1 p-4 w-full mx-auto" :class="isF17Section ? 'max-w-6xl' : 'max-w-4xl'">
            
            <div v-if="error" class="bg-red-50 text-red-700 p-6 rounded-3xl font-bold mb-6 shadow-xs">
                {{ error }}
            </div>

            <div v-else-if="questionnaire && currentSection">
                
                <!-- Section Header Banner -->
                <div class="mb-8 text-center bg-[#005B3C] p-8 md:p-10 rounded-[2rem] text-white shadow-sm">
                    <span class="inline-block bg-[#FFD700] text-[#005B3C] text-xs font-black uppercase px-3.5 py-1 rounded-full mb-2 tracking-wider shadow-xs">
                        Bagian {{ activeSectionIndex + 1 }} dari {{ questionnaire.sections.length }}
                    </span>
                    <h1 class="text-2xl md:text-4xl font-black tracking-tight mb-2">{{ currentSection.title }}</h1>
                    <p class="text-green-100 font-medium text-base md:text-lg">Pilih jawaban yang paling sesuai. Kolom <span class="text-[#FFD700] font-black">*</span> wajib diisi.</p>
                </div>

                <!-- Banner Integrasi Profil & Perusahaan -->
                <div class="mb-8 p-5 bg-emerald-50 rounded-[1.8rem] flex items-center gap-4 text-emerald-900 shadow-xs">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="flex-1 text-xs md:text-sm">
                        <span class="font-black block text-emerald-950 text-sm md:text-base">Data Identitas, Perusahaan & Atasan Terintegrasi</span>
                        Identitas pribadi, detail perusahaan, dan atasan Anda otomatis ditarik dari profil alumni Anda dan tidak perlu diisi ulang di sini.
                    </div>
                </div>

                <form @submit.prevent="handleNextOrSubmit" class="space-y-8">
                    
                    <!-- TAMPILAN KHUSUS F17: Evaluasi Kompetensi Berdampingan (A vs B) -->
                    <div v-if="isF17Section" class="space-y-6">
                        <!-- Header Banner F17 -->
                        <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm">
                            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                                <div>
                                    <span class="inline-block bg-[#FFD700] text-[#005B3C] text-xs font-black uppercase px-3 py-1 rounded-full mb-2 tracking-wider shadow-xs">
                                        Instrumen F17
                                    </span>
                                    <h2 class="text-2xl font-black text-gray-800">Evaluasi Kompetensi Lulusan & Kontribusi Kampus</h2>
                                    <p class="text-sm font-semibold text-gray-500 mt-1">Bandingkan tingkat kompetensi yang Anda kuasai saat lulus dengan kontribusi yang diberikan oleh perguruan tinggi.</p>
                                </div>
                                <!-- Progress Counter -->
                                <div class="flex items-center gap-3 bg-green-50 px-4 py-2.5 rounded-2xl shrink-0 shadow-xs">
                                    <div class="text-right">
                                        <div class="text-xs font-bold text-gray-500">Progres Pengisian</div>
                                        <div class="text-lg font-black text-[#005B3C]">{{ f17CompletedCount }} / {{ f17AspectPairs.length }} Aspek</div>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-[#005B3C] text-white flex items-center justify-center font-black text-xs shadow-xs">
                                        {{ Math.round((f17CompletedCount / (f17AspectPairs.length || 1)) * 100) }}%
                                    </div>
                                </div>
                            </div>

                            <!-- Dua Kolom Petunjuk A & B -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">
                                <div class="p-4 rounded-2xl bg-emerald-50 flex items-start gap-3 shadow-xs">
                                    <span class="w-8 h-8 rounded-xl bg-[#005B3C] text-white flex items-center justify-center font-black text-sm shrink-0 shadow-xs">A</span>
                                    <div>
                                        <div class="font-black text-[#005B3C] text-sm">Tingkat Penguasaan Kompetensi Saat Lulus</div>
                                        <div class="text-xs font-medium text-emerald-900 mt-0.5">Pada saat lulus, pada tingkat mana kompetensi di bawah ini Anda kuasai?</div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-2xl bg-amber-50 flex items-start gap-3 shadow-xs">
                                    <span class="w-8 h-8 rounded-xl bg-[#FFD700] text-[#005B3C] flex items-center justify-center font-black text-sm shrink-0 shadow-xs">B</span>
                                    <div>
                                        <div class="font-black text-amber-900 text-sm">Kontribusi Perguruan Tinggi (Kampus)</div>
                                        <div class="text-xs font-medium text-amber-950 mt-0.5">Pada saat lulus, bagaimana kontribusi perguruan tinggi dalam hal kompetensi di bawah ini?</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan Skala 1-5 -->
                            <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-4 mt-5 pt-4 border-t border-gray-100 text-xs font-bold text-gray-600">
                                <span class="text-gray-400 font-extrabold uppercase tracking-wider text-[11px]">Skala Penilaian:</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-black shadow-xs">1</span> Sangat Rendah</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-black shadow-xs">2</span> Rendah</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-black shadow-xs">3</span> Cukup</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-black shadow-xs">4</span> Tinggi</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-black shadow-xs">5</span> Sangat Tinggi</span>
                            </div>
                        </div>

                        <!-- Tabel Komparasi Berdampingan -->
                        <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm min-w-[840px]">
                                    <thead>
                                        <tr class="bg-gray-50/80 text-gray-700">
                                            <!-- Header Sisi A -->
                                            <th class="py-4 px-3 w-[270px] bg-emerald-50/70">
                                                <div class="flex items-center justify-center gap-2 mb-2">
                                                    <span class="px-2 py-0.5 rounded-lg bg-[#005B3C] text-white text-xs font-black shadow-xs">A</span>
                                                    <span class="font-black text-[#005B3C] text-xs uppercase tracking-wide">Kompetensi Dikuasai</span>
                                                </div>
                                                <div class="flex justify-between items-center px-1 text-xs font-bold text-gray-500">
                                                    <span class="text-[11px] text-emerald-800">Sangat Rendah</span>
                                                    <div class="flex gap-2">
                                                        <span v-for="n in 5" :key="'ha'+n" class="w-7 text-center font-black text-emerald-900">{{ n }}</span>
                                                    </div>
                                                    <span class="text-[11px] text-emerald-800">Sangat Tinggi</span>
                                                </div>
                                            </th>

                                            <!-- Header Tengah (Aspek) -->
                                            <th class="py-4 px-4 text-center font-black text-gray-700 uppercase tracking-wide text-xs">
                                                Aspek Kompetensi
                                            </th>

                                            <!-- Header Sisi B -->
                                            <th class="py-4 px-3 w-[270px] bg-amber-50/70">
                                                <div class="flex items-center justify-center gap-2 mb-2">
                                                    <span class="px-2 py-0.5 rounded-lg bg-[#FFD700] text-[#005B3C] text-xs font-black shadow-xs">B</span>
                                                    <span class="font-black text-amber-900 text-xs uppercase tracking-wide">Kontribusi Kampus</span>
                                                </div>
                                                <div class="flex justify-between items-center px-1 text-xs font-bold text-gray-500">
                                                    <span class="text-[11px] text-amber-900">Sangat Rendah</span>
                                                    <div class="flex gap-2">
                                                        <span v-for="n in 5" :key="'hb'+n" class="w-7 text-center font-black text-amber-950">{{ n }}</span>
                                                    </div>
                                                    <span class="text-[11px] text-amber-900">Sangat Tinggi</span>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr 
                                            v-for="pair in f17AspectPairs" 
                                            :key="'pair_' + pair.aspectNumber" 
                                            class="hover:bg-gray-50/80 transition-colors"
                                            :class="[
                                                form.answers[pair.qA.id] && form.answers[pair.qB.id] ? 'bg-green-50/40' : ''
                                            ]"
                                        >
                                            <!-- Opsi Lingkaran Sisi A -->
                                            <td class="py-3 px-3 bg-emerald-50/20">
                                                <div class="flex items-center justify-between max-w-[240px] mx-auto">
                                                    <label 
                                                        v-for="(opt, idx) in pair.qA.options" 
                                                        :key="opt.id" 
                                                        class="cursor-pointer relative p-0.5 select-none"
                                                        :title="opt.option_text"
                                                    >
                                                        <input 
                                                            type="radio" 
                                                            :name="'question_' + pair.qA.id" 
                                                            :value="opt.option_text" 
                                                            v-model="form.answers[pair.qA.id]" 
                                                            :required="pair.qA.is_required" 
                                                            class="sr-only"
                                                        >
                                                        <div 
                                                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-sm transition-all duration-150 shadow-xs"
                                                            :class="[
                                                                form.answers[pair.qA.id] === opt.option_text
                                                                    ? 'bg-[#005B3C] text-white shadow-sm scale-110'
                                                                    : 'bg-white text-gray-600 hover:bg-emerald-100 hover:text-[#005B3C]'
                                                            ]"
                                                        >
                                                            {{ idx + 1 }}
                                                        </div>
                                                    </label>
                                                </div>
                                            </td>

                                            <!-- Nama Aspek (Tengah) -->
                                            <td class="py-3 px-4 text-center">
                                                <div class="flex flex-col items-center justify-center gap-1">
                                                    <span class="font-black text-gray-800 text-sm md:text-base leading-snug">
                                                        {{ pair.aspectNumber }}. {{ pair.aspectName }}
                                                    </span>
                                                    <span class="inline-block text-[11px] font-mono font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md shadow-xs">
                                                        {{ pair.qA.code }} & {{ pair.qB.code }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Opsi Lingkaran Sisi B -->
                                            <td class="py-3 px-3 bg-amber-50/20">
                                                <div class="flex items-center justify-between max-w-[240px] mx-auto">
                                                    <label 
                                                        v-for="(opt, idx) in pair.qB.options" 
                                                        :key="opt.id" 
                                                        class="cursor-pointer relative p-0.5 select-none"
                                                        :title="opt.option_text"
                                                    >
                                                        <input 
                                                            type="radio" 
                                                            :name="'question_' + pair.qB.id" 
                                                            :value="opt.option_text" 
                                                            v-model="form.answers[pair.qB.id]" 
                                                            :required="pair.qB.is_required" 
                                                            class="sr-only"
                                                        >
                                                        <div 
                                                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-sm transition-all duration-150 shadow-xs"
                                                            :class="[
                                                                form.answers[pair.qB.id] === opt.option_text
                                                                    ? 'bg-[#FFD700] text-[#005B3C] shadow-sm scale-110'
                                                                    : 'bg-white text-gray-600 hover:bg-amber-100 hover:text-amber-900'
                                                            ]"
                                                        >
                                                            {{ idx + 1 }}
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

                    <!-- TAMPILAN STANDAR: Pertanyaan di Section Lainnya -->
                    <div v-else class="space-y-8">
                        <template v-for="(group, gIdx) in groupedQuestions" :key="'grp_' + gIdx">
                            
                            <!-- KELOMPOK DUA PERTANYAAN BERDAMPINGAN (Khusus F6 dan F7: Kanan-Kiri Kotak) -->
                            <div v-if="group.type === 'pair'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <template v-for="q in group.items" :key="q.id">
                                    <div 
                                        v-show="isQuestionVisible(q.id)" 
                                        class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm relative z-0 flex flex-col justify-between"
                                    >
                                        <div>
                                            <h2 class="text-lg md:text-xl font-black text-gray-800 mb-6 leading-relaxed flex items-start gap-3">
                                                <span class="shrink-0 bg-[#FFD700] text-[#005B3C] px-3 py-1 rounded-xl text-sm font-black shadow-xs">{{ q.code }}</span>
                                                <span>{{ q.question_text }} <span v-if="q.is_required" class="text-red-500 font-black">*</span></span>
                                            </h2>
                                        </div>

                                        <div class="mt-4">
                                            <input 
                                                type="number" 
                                                v-model="form.answers[q.id]" 
                                                :required="q.is_required && isQuestionVisible(q.id)"
                                                @keydown="filterNumberInput"
                                                min="0"
                                                class="w-full rounded-2xl bg-gray-50/90 p-5 text-gray-900 font-black font-mono focus:bg-white focus:ring-2 focus:ring-[#005B3C] transition-all text-2xl text-center border-0 shadow-xs"
                                                placeholder="0"
                                            >
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- PERTANYAAN TUNGGAL (Ukuran Penuh) -->
                            <template v-else>
                                <div 
                                    v-for="q in group.items" 
                                    :key="q.id"
                                    v-show="isQuestionVisible(q.id)" 
                                    class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm mb-6 relative z-0"
                                    :style="{ zIndex: q.type === 'searchable_select' ? 10 : 1 }"
                                >
                                
                                <h2 class="text-xl md:text-2xl font-black text-gray-800 mb-6 leading-relaxed flex items-start gap-4">
                                    <span class="shrink-0 bg-[#FFD700] text-[#005B3C] px-3.5 py-1 rounded-xl text-base font-black shadow-xs">{{ q.code }}</span>
                                    <span>{{ q.question_text }} <span v-if="q.is_required" class="text-red-500 font-black">*</span></span>
                                </h2>

                                <!-- Tipe Text -->
                                <div v-if="q.type === 'text'">
                                    <div v-if="q.code === 'F1'" class="relative max-w-md">
                                        <input 
                                            type="text" 
                                            v-model="form.answers[q.id]" 
                                            readonly
                                            class="w-full rounded-2xl bg-gray-100 p-5 text-gray-800 font-mono font-black text-xl cursor-not-allowed border-0 shadow-inner"
                                        >
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 bg-green-100 text-[#005B3C] text-xs font-black px-3.5 py-1.5 rounded-full shadow-xs">
                                            NIM Terverifikasi
                                        </span>
                                    </div>
                                    <input 
                                        v-else
                                        type="text" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required && isQuestionVisible(q.id)"
                                        class="w-full rounded-2xl bg-gray-50/90 p-5 text-gray-900 font-bold focus:bg-white focus:ring-2 focus:ring-[#005B3C] transition-all text-lg border-0 shadow-xs"
                                        placeholder="Ketik jawabanmu di sini..."
                                    >
                                </div>

                                <!-- Tipe Number -->
                                <div v-else-if="q.type === 'number'">
                                    <input 
                                        type="number" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required && isQuestionVisible(q.id)"
                                        @keydown="filterNumberInput"
                                        min="0"
                                        class="w-full max-w-xs rounded-2xl bg-gray-50/90 p-5 text-gray-900 font-black font-mono focus:bg-white focus:ring-2 focus:ring-[#005B3C] transition-all text-xl text-center border-0 shadow-xs"
                                        placeholder="0"
                                    >
                                </div>

                                <!-- Tipe Searchable Select -->
                                <div v-else-if="q.type === 'searchable_select'" class="relative">
                                    <SearchableSelect 
                                        v-model="form.answers[q.id]" 
                                        :options="q.options" 
                                        :required="q.is_required && isQuestionVisible(q.id)" 
                                        placeholder="Cari bidang pekerjaan..." 
                                    />
                                </div>

                                <!-- Tipe Radio / Single Choice -->
                                <div v-else-if="q.type === 'radio' || q.type === 'single_choice'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group block select-none">
                                        <input 
                                            type="radio" 
                                            :name="'question_'+q.id" 
                                            :value="opt.option_text" 
                                            v-model="form.answers[q.id]" 
                                            :required="q.is_required && isQuestionVisible(q.id)" 
                                            class="sr-only"
                                        >
                                        <div 
                                            class="h-full p-5 rounded-2xl font-bold text-lg transition-all duration-200 flex flex-col items-center justify-center text-center shadow-xs"
                                            :class="[
                                                form.answers[q.id] === opt.option_text
                                                    ? 'bg-[#005B3C] text-white shadow-md hover:bg-[#00482f]' 
                                                    : 'bg-gray-50/90 text-gray-700 hover:bg-emerald-50 hover:text-[#005B3C] hover:shadow-sm'
                                            ]"
                                        >
                                            <span>{{ opt.option_text }}</span>
                                            <!-- Input Teks Ekstra untuk opsi 'Lainnya' pada Radio / Single Choice -->
                                            <div 
                                                v-if="form.answers[q.id] === opt.option_text && (opt.option_text.toLowerCase().includes('lainnya') || opt.option_text.toLowerCase().includes('tuliskan') || opt.option_text.includes('...'))" 
                                                class="w-full mt-4" 
                                                @click.stop
                                            >
                                                <input 
                                                    type="text"
                                                    v-model="form.answers[q.id + '_custom']"
                                                    placeholder="Tuliskan di sini..."
                                                    class="w-full rounded-xl bg-white text-gray-900 placeholder-gray-400 p-3.5 font-bold shadow-inner focus:ring-2 focus:ring-[#FFD700] text-center text-base border-0"
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Tipe Checkbox / Multiple Choice -->
                                <div v-else-if="q.type === 'checkbox' || q.type === 'multiple_choice'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group block select-none">
                                        <input 
                                            type="checkbox" 
                                            :value="opt.option_text" 
                                            v-model="form.answers[q.id]" 
                                            class="sr-only"
                                        >
                                        <div 
                                            class="h-full p-5 rounded-2xl font-bold text-lg transition-all duration-200 flex flex-col items-center justify-center text-center shadow-xs"
                                            :class="[
                                                Array.isArray(form.answers[q.id]) && form.answers[q.id].includes(opt.option_text)
                                                    ? 'bg-[#005B3C] text-white shadow-md hover:bg-[#00482f]' 
                                                    : 'bg-gray-50/90 text-gray-700 hover:bg-emerald-50 hover:text-[#005B3C] hover:shadow-sm'
                                            ]"
                                        >
                                            <span>{{ opt.option_text }}</span>
                                            <!-- Input Teks Ekstra untuk opsi 'Lainnya' pada Checkbox / Multiple Choice -->
                                            <div 
                                                v-if="Array.isArray(form.answers[q.id]) && form.answers[q.id].includes(opt.option_text) && (opt.option_text.toLowerCase().includes('lainnya') || opt.option_text.includes('...'))" 
                                                class="w-full mt-4" 
                                                @click.stop
                                            >
                                                <input 
                                                    type="text"
                                                    v-model="form.answers[q.id + '_custom']"
                                                    placeholder="Tuliskan di sini..."
                                                    class="w-full rounded-xl bg-white text-gray-900 placeholder-gray-400 p-3.5 font-bold shadow-inner focus:ring-2 focus:ring-[#FFD700] text-center text-base border-0"
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Tipe Radio dengan Input Teks / Angka (radio_input / radio_text) -->
                                <div v-else-if="q.type === 'radio_input' || q.type === 'radio_text'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group block select-none">
                                        <input 
                                            type="radio" 
                                            :name="'question_'+q.id" 
                                            :value="opt.option_text" 
                                            v-model="form.answers[q.id].selected" 
                                            @change="handleRadioOptionSelect(q.id, opt.id)"
                                            class="sr-only"
                                        >
                                        <div 
                                            class="p-5 rounded-2xl font-bold text-lg transition-all duration-200 flex flex-col items-center justify-center text-center shadow-xs"
                                            :class="[
                                                form.answers[q.id]?.selected === opt.option_text
                                                    ? 'bg-[#005B3C] text-white shadow-md hover:bg-[#00482f]' 
                                                    : 'bg-gray-50/90 text-gray-700 hover:bg-emerald-50 hover:text-[#005B3C] hover:shadow-sm'
                                            ]"
                                        >
                                            <span>{{ opt.option_text }}</span>
                                            <div 
                                                v-if="form.answers[q.id]?.selected === opt.option_text && (opt.option_text.includes('...') || opt.option_text.includes('…') || opt.option_text.toLowerCase().includes('lainnya'))" 
                                                class="w-full mt-4" 
                                                @click.stop
                                            >
                                                <input 
                                                    :type="q.type === 'radio_input' ? 'number' : 'text'"
                                                    :value="getRadioInputVal(q.id, opt.id)"
                                                    @input="setRadioInputVal(q.id, opt.id, $event.target.value)"
                                                    :placeholder="q.type === 'radio_input' ? 'Masukkan angka...' : 'Tuliskan di sini...'"
                                                    @keydown="q.type === 'radio_input' ? filterNumberInput($event) : null"
                                                    class="w-full rounded-xl bg-white text-gray-900 placeholder-gray-400 p-3.5 font-bold shadow-inner focus:ring-2 focus:ring-[#FFD700] text-center text-base border-0"
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Tipe Multiple Number (F13 dll) -->
                                <div v-else-if="q.type === 'multiple_number'" class="space-y-4">
                                    <div v-for="opt in q.options" :key="opt.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50/90 rounded-2xl gap-3 shadow-xs">
                                        <label class="font-bold text-gray-700 text-base">
                                            {{ opt.option_text }}
                                        </label>
                                        <div class="relative w-full sm:w-64">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-400">Rp</span>
                                            <input 
                                                type="number" 
                                                v-model="form.answers[q.id][opt.code]"
                                                @keydown="filterNumberInput"
                                                min="0"
                                                placeholder="0"
                                                class="w-full pl-12 pr-4 py-3 rounded-xl bg-white font-mono font-bold focus:ring-2 focus:ring-[#005B3C] border-0 shadow-xs text-right text-lg"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Tipe Matrix (Skala 1-5) -->
                                <div v-else-if="q.type === 'matrix'" class="overflow-x-auto">
                                    <div class="min-w-max space-y-3">
                                        <div class="flex px-4 py-2.5 bg-gray-100 rounded-xl font-black text-gray-500 text-sm">
                                            <div class="w-1/2 uppercase">Aspek</div>
                                            <div class="flex-1 flex justify-between px-4">
                                                <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                                            </div>
                                        </div>
                                        <div v-for="opt in q.options" :key="opt.id" class="flex items-center px-4 py-3.5 bg-gray-50/70 rounded-2xl hover:bg-emerald-50/50 transition-colors shadow-xs">
                                            <div class="w-1/2 font-bold text-gray-800 pr-4">{{ opt.option_text }}</div>
                                            <div class="flex-1 flex justify-between px-4">
                                                <label v-for="i in 5" :key="i" class="cursor-pointer relative">
                                                    <input 
                                                        type="radio" 
                                                        :name="'q_'+q.id+'_opt_'+opt.id" 
                                                        :value="i" 
                                                        v-model="form.answers[q.id][opt.id]" 
                                                        :required="q.is_required" 
                                                        class="sr-only"
                                                    >
                                                    <div 
                                                        class="w-10 h-10 rounded-full flex items-center justify-center font-black text-sm transition-all transform shadow-xs"
                                                        :class="[
                                                            form.answers[q.id][opt.id] === i
                                                                ? 'bg-[#005B3C] text-white scale-110 shadow-sm'
                                                                : 'bg-white text-gray-500 hover:bg-emerald-100/80 hover:text-[#005B3C]'
                                                        ]"
                                                    >
                                                        {{ i }}
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tipe Matrix Dual -->
                                <div v-else-if="q.type === 'matrix_dual'" class="overflow-x-auto">
                                    <div class="min-w-max rounded-3xl overflow-hidden shadow-xs">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="w-1/3 px-4 py-4 text-left font-black text-gray-600 uppercase bg-gray-100">Kompetensi</th>
                                                    <th colspan="5" class="px-2 py-3 text-center font-black text-white bg-[#005B3C] uppercase">(A) Saat Lulus</th>
                                                    <th colspan="5" class="px-2 py-3 text-center font-black text-[#005B3C] bg-[#FFD700] uppercase">(B) Kontribusi PT</th>
                                                </tr>
                                                <tr>
                                                    <th v-for="i in 5" :key="'a'+i" class="bg-green-100 py-2 text-[#005B3C] w-10 text-center font-black">{{ i }}</th>
                                                    <th v-for="i in 5" :key="'b'+i" class="bg-yellow-100 py-2 text-yellow-800 w-10 text-center font-black">{{ i }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-100">
                                                <tr v-for="opt in q.options" :key="opt.id" class="hover:bg-gray-50">
                                                    <td class="px-4 py-4 font-bold text-gray-800">{{ opt.option_text }}</td>
                                                    <td v-for="i in 5" :key="'a_opt'+i" class="text-center p-2">
                                                        <label class="cursor-pointer block">
                                                            <input type="radio" :name="'q_'+q.id+'_opt_'+opt.id+'_A'" :value="i" v-model="form.answers[q.id][opt.id].A" :required="q.is_required" class="sr-only">
                                                            <div 
                                                                class="mx-auto w-8 h-8 rounded-full flex items-center justify-center font-bold transition-all shadow-xs"
                                                                :class="[
                                                                    form.answers[q.id][opt.id].A === i 
                                                                        ? 'bg-[#005B3C] text-white shadow-sm' 
                                                                        : 'bg-gray-100 text-transparent hover:bg-gray-200'
                                                                ]"
                                                            >✓</div>
                                                        </label>
                                                    </td>
                                                    <td v-for="i in 5" :key="'b_opt'+i" class="text-center p-2 bg-yellow-50/20">
                                                        <label class="cursor-pointer block">
                                                            <input type="radio" :name="'q_'+q.id+'_opt_'+opt.id+'_B'" :value="i" v-model="form.answers[q.id][opt.id].B" :required="q.is_required" class="sr-only">
                                                            <div 
                                                                class="mx-auto w-8 h-8 rounded-full flex items-center justify-center font-bold transition-all shadow-xs"
                                                                :class="[
                                                                    form.answers[q.id][opt.id].B === i 
                                                                        ? 'bg-[#FFD700] text-[#005B3C] shadow-sm' 
                                                                        : 'bg-gray-100 text-transparent hover:bg-yellow-100'
                                                                ]"
                                                            >✓</div>
                                                        </label>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </template>
                </div>

                    <!-- Floating Navigations (Bottom Left and Right) -->
                    <!-- Tombol Kembali (Kiri Bawah) -->
                    <button 
                        type="button"
                        @click="prevSection"
                        :disabled="activeSectionIndex === 0"
                        class="fixed left-4 md:left-8 bottom-8 z-40 w-16 h-16 rounded-full flex justify-center items-center bg-[#FFD700] text-[#005B3C] shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all disabled:opacity-0 disabled:pointer-events-none group border-0"
                        title="Kembali ke Bagian Sebelumnya"
                    >
                        <svg class="w-8 h-8 pr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    
                    <!-- Tombol Lanjutkan/Simpan (Kanan Bawah) -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="fixed right-4 md:right-8 bottom-8 z-40 w-16 h-16 rounded-full flex justify-center items-center bg-[#005B3C] text-white shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed group border-0"
                        :title="isLastVisibleSection ? 'Selesai & Simpan Seluruh Kuesioner' : 'Lanjutkan ke Bagian Berikutnya'"
                    >
                        <svg v-if="!isLastVisibleSection" class="w-8 h-8 pl-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        <svg v-else class="w-8 h-8 pl-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                    </button>

                </form>
            </div>

        </main>
    </div>
</template>

<style scoped>
/* Menyembunyikan Scrollbar bawaan browser pada Stepper */
.custom-scrollbar::-webkit-scrollbar {
    display: none;
}
.custom-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
