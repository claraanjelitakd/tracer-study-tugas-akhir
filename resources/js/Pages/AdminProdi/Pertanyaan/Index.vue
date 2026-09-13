<!--
  Halaman Kelola Pertanyaan & Section Kuesioner Khusus Program Studi
  File: resources/js/Pages/AdminProdi/Pertanyaan/Index.vue

  Desain Standar Resmi Super Admin UKDW:
  - Header Hijau Solid Resmi UKDW #0D542B
  - Kartu Putih Bersih, Bebas Border Bertumpuk
  - Bebas Icon/Emoji Berlebihan (No Slop)
-->
<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';
import Navbar from '../Components/Navbar.vue';

const props = defineProps({
    user: Object,
    prodi: Object,
    questions: {
        type: Array,
        default: () => [],
    },
    sections: {
        type: Array,
        default: () => [],
    },
});

// =========================================================================
// 1. STATE & NAVIGASI SECTION PRODI
// =========================================================================
const selectedSectionId = ref(props.sections.length > 0 ? props.sections[0].id : 'all');
const showSectionModal = ref(false);
const isEditSection = ref(false);

const sectionForm = useForm({
    id: null,
    title: '',
    description: '',
    order: 1,
});

const currentActiveSection = computed(() => {
    if (selectedSectionId.value === 'all') return null;
    return props.sections.find(s => s.id === Number(selectedSectionId.value)) || null;
});

const filteredQuestions = computed(() => {
    if (selectedSectionId.value === 'all') {
        return props.questions;
    }
    return props.questions.filter(q => q.prodi_question_section_id === Number(selectedSectionId.value));
});

const openCreateSectionModal = () => {
    isEditSection.value = false;
    const nextOrder = props.sections.length + 1;
    sectionForm.reset();
    sectionForm.id = null;
    sectionForm.title = '';
    sectionForm.description = '';
    sectionForm.order = nextOrder;
    showSectionModal.value = true;
};

const openEditSectionModal = (sec) => {
    isEditSection.value = true;
    sectionForm.id = sec.id;
    sectionForm.title = sec.title;
    sectionForm.description = sec.description || '';
    sectionForm.order = sec.order;
    showSectionModal.value = true;
};

const submitSection = () => {
    if (isEditSection.value) {
        sectionForm.put(`/prodi/section/${sectionForm.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showSectionModal.value = false;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Bagian kuesioner prodi berhasil diperbarui.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    } else {
        sectionForm.post('/prodi/section', {
            preserveScroll: true,
            onSuccess: () => {
                showSectionModal.value = false;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Bagian kuesioner prodi baru berhasil ditambahkan.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    }
};

const deleteSection = (sec) => {
    Swal.fire({
        title: 'Hapus Bagian Ini?',
        text: `Bagian "${sec.title}" beserta seluruh pertanyaan di dalamnya akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/prodi/section/${sec.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    selectedSectionId.value = 'all';
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dihapus',
                        text: 'Bagian kuesioner berhasil dihapus.',
                        confirmButtonColor: '#0D542B',
                    });
                },
            });
        }
    });
};

// =========================================================================
// 2. STATE & MODAL PERTANYAAN PRODI
// =========================================================================
const showQuestionModal = ref(false);
const isEditQuestion = ref(false);

const questionForm = useForm({
    id: null,
    code: '',
    question_text: '',
    type: 'single_choice',
    is_required: true,
    prodi_question_section_id: null,
    order: 1,
    options: ['', ''],
});

const strPad = (n, width) => {
    const s = String(n);
    return s.length >= width ? s : new Array(width - s.length + 1).join('0') + s;
};

const openCreateQuestionModal = () => {
    isEditQuestion.value = false;
    const nextNum = props.questions.length + 1;
    const prodiCode = props.prodi?.kode_prodi ? 'P' + props.prodi.kode_prodi : 'P';

    let defaultSecId = null;
    if (selectedSectionId.value !== 'all') {
        defaultSecId = Number(selectedSectionId.value);
    } else if (props.sections.length > 0) {
        defaultSecId = props.sections[0].id;
    }

    questionForm.reset();
    questionForm.id = null;
    questionForm.code = `${prodiCode}-${strPad(nextNum, 2)}`;
    questionForm.question_text = '';
    questionForm.type = 'single_choice';
    questionForm.is_required = true;
    questionForm.prodi_question_section_id = defaultSecId;
    questionForm.order = nextNum;
    questionForm.options = ['', ''];
    showQuestionModal.value = true;
};

const openEditQuestionModal = (q) => {
    isEditQuestion.value = true;
    questionForm.id = q.id;
    questionForm.code = q.code;
    questionForm.question_text = q.question_text;
    questionForm.type = q.type;
    questionForm.is_required = !!q.is_required;
    questionForm.prodi_question_section_id = q.prodi_question_section_id;
    questionForm.order = q.order;
    questionForm.options = [];
    showQuestionModal.value = true;
};

const submitQuestion = () => {
    if (isEditQuestion.value) {
        questionForm.put(`/prodi/pertanyaan/${questionForm.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showQuestionModal.value = false;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diperbarui',
                    text: 'Pertanyaan berhasil diperbarui.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    } else {
        questionForm.post('/prodi/pertanyaan', {
            preserveScroll: true,
            onSuccess: () => {
                showQuestionModal.value = false;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Ditambahkan',
                    text: 'Pertanyaan baru berhasil ditambahkan.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    }
};

const deleteQuestion = (q) => {
    Swal.fire({
        title: 'Hapus Pertanyaan?',
        text: `Pertanyaan "${q.code}" beserta seluruh opsi jawabannya akan dihapus permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/prodi/pertanyaan/${q.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dihapus',
                        text: 'Pertanyaan berhasil dihapus.',
                        confirmButtonColor: '#0D542B',
                    });
                },
            });
        }
    });
};

// =========================================================================
// 3. STATE & MODAL OPSI JAWABAN
// =========================================================================
const showOptionModal = ref(false);
const isEditOption = ref(false);
const activeQuestionForOption = ref(null);

const optionForm = useForm({
    id: null,
    question_id: null,
    code: '',
    option_text: '',
    jump_to: '',
    order: 1,
});

const openAddOptionModal = (q) => {
    isEditOption.value = false;
    activeQuestionForOption.value = q;
    const nextOptNum = (q.options?.length || 0) + 1;
    optionForm.reset();
    optionForm.id = null;
    optionForm.question_id = q.id;
    optionForm.code = `${q.code}-${strPad(nextOptNum, 2)}`;
    optionForm.option_text = '';
    optionForm.jump_to = '';
    optionForm.order = nextOptNum;
    showOptionModal.value = true;
};

const openEditOptionModal = (q, opt) => {
    isEditOption.value = true;
    activeQuestionForOption.value = q;
    optionForm.id = opt.id;
    optionForm.question_id = q.id;
    optionForm.code = opt.code || '';
    optionForm.option_text = opt.option_text;
    optionForm.jump_to = opt.jump_to || '';
    optionForm.order = opt.order || 1;
    showOptionModal.value = true;
};

const submitOption = () => {
    if (isEditOption.value) {
        optionForm.put(`/prodi/opsi/${optionForm.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showOptionModal.value = false;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diperbarui',
                    text: 'Opsi jawaban berhasil diperbarui.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    } else {
        optionForm.post('/prodi/opsi', {
            preserveScroll: true,
            onSuccess: () => {
                showOptionModal.value = false;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Ditambahkan',
                    text: 'Opsi jawaban baru berhasil ditambahkan.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    }
};

const deleteOption = (opt) => {
    Swal.fire({
        title: 'Hapus Opsi Ini?',
        text: `Opsi "${opt.option_text}" akan dihapus dari pertanyaan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/prodi/opsi/${opt.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dihapus',
                        text: 'Opsi berhasil dihapus.',
                        confirmButtonColor: '#0D542B',
                    });
                },
            });
        }
    });
};

const getTypeLabel = (type) => {
    switch (type) {
        case 'single_choice': return 'Pilihan Tunggal (Radio)';
        case 'multiple_choice': return 'Pilihan Ganda (Checkbox)';
        case 'text': return 'Isian Teks Singkat/Panjang';
        case 'number': return 'Isian Angka (Number)';
        case 'rating_5': return 'Skala Penilaian (1 - 5)';
        case 'radio_input': return 'Pilihan + Titik Isian';
        case 'date': return 'Format Tanggal';
        default: return type;
    }
};

const getSectionName = (secId) => {
    const s = props.sections.find(sec => sec.id === secId);
    return s ? s.title : 'Umum';
};
</script>

<template>
    <Head :title="`Kelola Kuesioner - ${prodi?.nama_prodi || 'Program Studi'}`" />

    <div class="min-h-screen bg-[#f8fafc] text-gray-800 font-sans pb-24">
        <!-- Navbar Terpadu Admin Prodi -->
        <Navbar :user="user" :prodi="prodi" />

        <!-- Header Solid Hijau Resmi UKDW #0D542B -->
        <header class="bg-[#0D542B] text-white pt-10 pb-20">
            <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 text-xs text-white/80 font-medium mb-2">
                        <Link href="/prodi/dashboard" class="hover:underline">Dashboard</Link>
                        <span>/</span>
                        <span class="text-white font-bold">Kelola Kuesioner</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Kelola Butir Pertanyaan Kuesioner
                    </h1>
                    <p class="text-white/90 text-sm sm:text-base font-normal mt-1 max-w-2xl leading-relaxed">
                        Atur butir pertanyaan dan opsi pilihan jawaban khusus evaluasi kurikulum Program Studi {{ prodi?.nama_prodi }}.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <Link 
                        href="/prodi/sections"
                        class="px-4 py-2.5 bg-white/15 hover:bg-white/25 border border-white/20 text-white text-xs font-bold rounded-xl transition-all cursor-pointer"
                    >
                        Kelola Section
                    </Link>
                    <button 
                        @click="openCreateQuestionModal" 
                        class="px-5 py-2.5 bg-[#FDC700] hover:bg-[#e5b500] text-black font-extrabold text-xs rounded-xl shadow-xs transition-colors cursor-pointer"
                    >
                        + Tambah Pertanyaan
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="w-full max-w-[1400px] mx-auto -mt-10 px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Section Filter Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between gap-2 mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">
                            Bagian (Section) Kuesioner Prodi
                        </span>
                        <span class="text-xs text-gray-400">Pilih bagian untuk menyaring butir pertanyaan</span>
                    </div>
                    <button 
                        @click="openCreateSectionModal" 
                        class="text-xs font-bold text-[#0D542B] hover:underline cursor-pointer"
                    >
                        + Tambah Section Baru
                    </button>
                </div>

                <!-- Tabs Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <button 
                        @click="selectedSectionId = 'all'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer"
                        :class="[
                            selectedSectionId === 'all'
                                ? 'bg-[#0D542B] text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        Semua Section ({{ questions.length }})
                    </button>

                    <button 
                        v-for="sec in sections" 
                        :key="sec.id"
                        @click="selectedSectionId = sec.id"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-2"
                        :class="[
                            selectedSectionId === sec.id
                                ? 'bg-[#0D542B] text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        ]"
                    >
                        <span>Bagian {{ sec.order }}: {{ sec.title }}</span>
                        <span 
                            class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                            :class="selectedSectionId === sec.id ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-700'"
                        >
                            {{ questions.filter(q => q.prodi_question_section_id === sec.id).length }}
                        </span>
                    </button>
                </div>

                <!-- Active Section Details & Actions -->
                <div v-if="currentActiveSection" class="mt-4 p-4 rounded-xl bg-gray-50 border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-extrabold text-[#0D542B] uppercase tracking-wider">
                            Bagian {{ currentActiveSection.order }}: {{ currentActiveSection.title }}
                        </div>
                        <p v-if="currentActiveSection.description" class="text-xs text-gray-600 mt-0.5">
                            {{ currentActiveSection.description }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button 
                            @click="openEditSectionModal(currentActiveSection)"
                            class="px-3 py-1.5 bg-white text-gray-700 hover:bg-gray-100 border border-gray-200 font-bold rounded-lg text-xs transition-all cursor-pointer"
                        >
                            Edit Bagian
                        </button>
                        <button 
                            @click="deleteSection(currentActiveSection)"
                            class="px-3 py-1.5 bg-white text-red-600 hover:bg-red-50 border border-red-200 font-bold rounded-lg text-xs transition-all cursor-pointer"
                        >
                            Hapus Bagian
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State jika belum ada pertanyaan -->
            <div v-if="filteredQuestions.length === 0" class="bg-white rounded-2xl p-12 text-center shadow-sm border border-dashed border-gray-200 space-y-3">
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-[#0D542B] mx-auto flex items-center justify-center text-xl font-bold">
                    ?
                </div>
                <h3 class="text-base font-bold text-gray-800">
                    {{ selectedSectionId === 'all' ? 'Belum Ada Pertanyaan Khusus Prodi' : 'Belum Ada Pertanyaan di Bagian Ini' }}
                </h3>
                <p class="text-gray-500 text-xs max-w-md mx-auto">
                    {{ selectedSectionId === 'all' 
                        ? 'Program Studi Anda belum memiliki butir pertanyaan kuesioner. Klik tombol di bawah untuk membuat pertanyaan pertama.'
                        : 'Bagian ini belum memiliki butir pertanyaan. Tambahkan pertanyaan untuk melengkapi bagian ini.' }}
                </p>
                <div class="pt-2">
                    <button 
                        @click="openCreateQuestionModal" 
                        class="px-5 py-2.5 bg-[#0D542B] hover:bg-[#093c1f] text-white font-extrabold rounded-xl shadow-xs transition-all text-xs cursor-pointer"
                    >
                        + Buat Pertanyaan Baru
                    </button>
                </div>
            </div>

            <!-- List Pertanyaan -->
            <div v-else class="space-y-4">
                <div 
                    v-for="q in filteredQuestions" 
                    :key="q.id" 
                    class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100"
                >
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                        <div class="flex items-start gap-3">
                            <span class="shrink-0 px-3 py-1 bg-[#0D542B] text-white font-mono font-bold text-xs rounded-lg">
                                {{ q.code }}
                            </span>
                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-gray-900 leading-snug">
                                    {{ q.question_text }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 mt-2">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                                        {{ getSectionName(q.prodi_question_section_id) }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                                        {{ getTypeLabel(q.type) }}
                                    </span>
                                    <span 
                                        class="px-2.5 py-0.5 rounded-lg text-xs font-bold"
                                        :class="q.is_required ? 'bg-[#FDC700] text-black' : 'bg-gray-100 text-gray-500'"
                                    >
                                        {{ q.is_required ? 'Wajib Diisi' : 'Opsional' }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium">
                                        Urutan: #{{ q.order }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Pertanyaan -->
                        <div class="flex items-center gap-2 self-end sm:self-start shrink-0">
                            <button 
                                @click="openEditQuestionModal(q)" 
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition-all cursor-pointer"
                            >
                                Edit
                            </button>
                            <button 
                                @click="deleteQuestion(q)" 
                                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-lg text-xs transition-all cursor-pointer"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Kotak Opsi Jawaban (Jika pertanyaan bertipe pilihan) -->
                    <div v-if="['single_choice', 'multiple_choice', 'radio_input'].includes(q.type)" class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                Pilihan Jawaban ({{ q.options?.length || 0 }} Opsi):
                            </span>
                            <button 
                                @click="openAddOptionModal(q)" 
                                class="text-xs font-bold text-[#0D542B] hover:underline cursor-pointer"
                            >
                                + Tambah Opsi
                            </button>
                        </div>

                        <!-- Daftar Opsi -->
                        <div v-if="q.options && q.options.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div 
                                v-for="opt in q.options" 
                                :key="opt.id" 
                                class="p-2.5 rounded-xl bg-gray-50 flex items-center justify-between border border-gray-100 text-xs"
                            >
                                <span class="font-medium text-gray-800 truncate pr-2">{{ opt.option_text }}</span>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button 
                                        @click="openEditOptionModal(q, opt)" 
                                        class="text-xs font-bold text-gray-500 hover:text-gray-900 cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        @click="deleteOption(opt)" 
                                        class="text-xs font-bold text-red-500 hover:text-red-700 cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-xs text-amber-700 italic bg-amber-50 p-2.5 rounded-xl border border-amber-100">
                            Belum ada opsi jawaban. Silakan klik "+ Tambah Opsi" agar alumni dapat memilih jawaban.
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- ========================================================================= -->
        <!-- MODAL FORM SECTION PRODI (TAMBAH / EDIT)                                 -->
        <!-- ========================================================================= -->
        <div v-if="showSectionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-lg w-full shadow-xl relative max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900">
                            {{ isEditSection ? 'Edit Bagian Kuesioner' : 'Tambah Bagian Kuesioner Baru' }}
                        </h2>
                        <span class="text-xs text-gray-400">Program Studi: {{ prodi?.nama_prodi }}</span>
                    </div>
                    <button @click="showSectionModal = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form @submit.prevent="submitSection" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Judul / Nama Bagian</label>
                        <input 
                            type="text" 
                            v-model="sectionForm.title" 
                            required 
                            placeholder="Contoh: Evaluasi Capaian Pembelajaran Lulusan"
                            class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Deskripsi / Penjelasan (Opsional)</label>
                        <textarea 
                            v-model="sectionForm.description" 
                            rows="3" 
                            placeholder="Tuliskan petunjuk atau keterangan tambahan..."
                            class="w-full rounded-xl border border-gray-200 py-2 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nomor Urut Posisi</label>
                        <input 
                            type="number" 
                            v-model.number="sectionForm.order" 
                            min="1"
                            class="w-full rounded-xl border border-gray-200 py-2 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showSectionModal = false" 
                            class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="sectionForm.processing"
                            class="px-5 py-2 bg-[#0D542B] hover:bg-[#093c1f] text-white font-extrabold rounded-xl text-xs shadow-xs transition-colors cursor-pointer disabled:opacity-50"
                        >
                            {{ isEditSection ? 'Simpan Perubahan' : 'Tambah Section' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL FORM PERTANYAAN PRODI (TAMBAH / EDIT)                              -->
        <!-- ========================================================================= -->
        <div v-if="showQuestionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-xl w-full shadow-xl relative max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900">
                            {{ isEditQuestion ? 'Edit Butir Pertanyaan' : 'Tambah Pertanyaan Baru' }}
                        </h2>
                        <span class="text-xs text-gray-400">Program Studi: {{ prodi?.nama_prodi }}</span>
                    </div>
                    <button @click="showQuestionModal = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form @submit.prevent="submitQuestion" class="space-y-4">
                    <!-- Pilih Section -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Bagian / Section Pertanyaan</label>
                        <select 
                            v-model="questionForm.prodi_question_section_id" 
                            required
                            class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                            <option :value="null" disabled>-- Pilih Section --</option>
                            <option v-for="sec in sections" :key="sec.id" :value="sec.id">
                                Bagian {{ sec.order }}: {{ sec.title }}
                            </option>
                        </select>
                    </div>

                    <!-- Kode & Urutan -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Kode Pertanyaan</label>
                            <input 
                                type="text" 
                                v-model="questionForm.code" 
                                required 
                                placeholder="Contoh: PSI-01"
                                class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-mono font-bold text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nomor Urut</label>
                            <input 
                                type="number" 
                                v-model.number="questionForm.order" 
                                min="1"
                                class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                            >
                        </div>
                    </div>

                    <!-- Teks Pertanyaan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Kalimat Pertanyaan</label>
                        <textarea 
                            v-model="questionForm.question_text" 
                            rows="3" 
                            required 
                            placeholder="Tuliskan butir pertanyaan kuesioner prodi di sini..."
                            class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        ></textarea>
                    </div>

                    <!-- Tipe Input Pertanyaan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Tipe Masukan Jawaban</label>
                        <select 
                            v-model="questionForm.type" 
                            required
                            class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                            <option value="single_choice">Pilihan Tunggal (Radio - 1 pilihan)</option>
                            <option value="multiple_choice">Pilihan Ganda (Checkbox - multi pilihan)</option>
                            <option value="text">Isian Uraian / Text Area</option>
                            <option value="number">Isian Angka (Number)</option>
                            <option value="rating_5">Skala Penilaian (Rating 1 - 5)</option>
                            <option value="radio_input">Pilihan Radio + Titik Isian</option>
                            <option value="date">Format Tanggal (Date)</option>
                        </select>
                    </div>

                    <!-- Input Opsi Awal -->
                    <div v-if="!isEditQuestion && ['single_choice', 'multiple_choice', 'radio_input'].includes(questionForm.type)" class="space-y-2 pt-1">
                        <label class="block text-xs font-bold text-gray-700 uppercase">Pilihan Opsi Jawaban Awal</label>
                        <div v-for="(opt, oIdx) in questionForm.options" :key="oIdx" class="flex items-center gap-2">
                            <input 
                                type="text" 
                                v-model="questionForm.options[oIdx]" 
                                :placeholder="'Teks Opsi ' + (oIdx + 1)"
                                class="flex-1 rounded-xl border border-gray-200 py-2 px-3 text-xs"
                            >
                            <button 
                                v-if="questionForm.options.length > 2"
                                type="button" 
                                @click="questionForm.options.splice(oIdx, 1)" 
                                class="text-xs text-red-500 hover:text-red-700 font-bold px-2 cursor-pointer"
                            >
                                Hapus
                            </button>
                        </div>
                        <button 
                            type="button" 
                            @click="questionForm.options.push('')" 
                            class="text-xs font-bold text-[#0D542B] hover:underline block pt-1 cursor-pointer"
                        >
                            + Tambah Baris Opsi
                        </button>
                    </div>

                    <!-- Switch Wajib Diisi -->
                    <div class="flex items-center gap-2 pt-2">
                        <input 
                            type="checkbox" 
                            id="is_required" 
                            v-model="questionForm.is_required"
                            class="w-4 h-4 text-[#0D542B] rounded-md focus:ring-0 cursor-pointer"
                        >
                        <label for="is_required" class="text-xs font-bold text-gray-700 cursor-pointer">
                            Wajib dijawab oleh alumni
                        </label>
                    </div>

                    <!-- Tombol Modal -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showQuestionModal = false" 
                            class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="questionForm.processing"
                            class="px-5 py-2 bg-[#0D542B] hover:bg-[#093c1f] text-white font-extrabold rounded-xl text-xs shadow-xs transition-colors cursor-pointer disabled:opacity-50"
                        >
                            {{ questionForm.processing ? 'Menyimpan...' : 'Simpan Pertanyaan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL FORM OPSI JAWABAN (TAMBAH / EDIT)                                  -->
        <!-- ========================================================================= -->
        <div v-if="showOptionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-xl relative">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900">
                            {{ isEditOption ? 'Edit Pilihan Opsi' : 'Tambah Opsi Baru' }}
                        </h2>
                        <span class="text-xs text-gray-400">Pertanyaan: {{ activeQuestionForOption?.code }}</span>
                    </div>
                    <button @click="showOptionModal = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form @submit.prevent="submitOption" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Teks Opsi Jawaban</label>
                        <input 
                            type="text" 
                            v-model="optionForm.option_text" 
                            required 
                            placeholder="Contoh: Sangat Relevan / Rekayasa Web"
                            class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Kode Opsi (Opsional)</label>
                        <input 
                            type="text" 
                            v-model="optionForm.code" 
                            placeholder="Contoh: PSI-01-01"
                            class="w-full rounded-xl border border-gray-200 py-2.5 px-3.5 text-xs font-mono text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        >
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showOptionModal = false" 
                            class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="optionForm.processing"
                            class="px-5 py-2 bg-[#0D542B] hover:bg-[#093c1f] text-white font-extrabold rounded-xl text-xs shadow-xs transition-colors cursor-pointer disabled:opacity-50"
                        >
                            {{ optionForm.processing ? 'Menyimpan...' : 'Simpan Opsi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
