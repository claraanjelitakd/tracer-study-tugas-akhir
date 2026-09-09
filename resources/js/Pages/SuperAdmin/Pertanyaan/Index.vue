<!--
  Halaman Utama Kelola Pertanyaan & Alur Percabangan Kuesioner (Superadmin)
  
  Fungsi:
  Portal kendali terpusat bagi Superadmin untuk mengelola seluruh instrumen kuesioner Tracer Study.
  Desain mengikuti estetika bersih, profesional, dan modern seperti pada modul Profil Alumni.
-->
<script setup>
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Swal from 'sweetalert2';

import Navbar from '@/Pages/SuperAdmin/Components/Navbar.vue';
import SectionTabs from './Components/SectionTabs.vue';
import QuestionCard from './Components/QuestionCard.vue';
import QuestionModal from './Components/QuestionModal.vue';
import OptionModal from './Components/OptionModal.vue';

// Properti yang dikirimkan oleh SuperAdmin\KelolaPertanyaan\DaftarPertanyaanController
const props = defineProps({
    questions: {
        type: Array,
        default: () => [],
    },
    sections: {
        type: Array,
        default: () => [],
    },
    prodis: {
        type: Array,
        default: () => [],
    },
    availableJumpTargets: {
        type: Array,
        default: () => [],
    },
    targetQuestionMap: {
        type: Object,
        default: () => ({}),
    },
});

// ========================================================
// 1. STATE NAVIGASI SECTION & SINKRONISASI URL
// ========================================================
const SUPERADMIN_SECTION_STORAGE_KEY = 'tracerstudy_superadmin_pertanyaan_section';

const getInitialSectionId = () => {
    if (typeof window !== 'undefined') {
        const urlParams = new URLSearchParams(window.location.search);
        const secParam = urlParams.get('sec_id');
        if (secParam) {
            const parsed = parseInt(secParam, 10);
            if (props.sections.some((s) => s.id === parsed)) return parsed;
        }
        const cached = localStorage.getItem(SUPERADMIN_SECTION_STORAGE_KEY);
        if (cached) {
            const parsed = parseInt(cached, 10);
            if (props.sections.some((s) => s.id === parsed)) return parsed;
        }
    }
    return props.sections.length > 0 ? props.sections[0].id : null;
};

const activeSectionId = ref(getInitialSectionId());

watch(activeSectionId, (newId) => {
    if (typeof window !== 'undefined' && newId) {
        localStorage.setItem(SUPERADMIN_SECTION_STORAGE_KEY, newId.toString());
        const url = new URL(window.location.href);
        url.searchParams.set('sec_id', newId.toString());
        window.history.replaceState({}, '', url.toString());
    }
});

const handleSelectSection = (sectionId) => {
    activeSectionId.value = sectionId;
};

// Objek data section yang sedang aktif
const currentActiveSection = computed(() => {
    return props.sections.find((s) => s.id === activeSectionId.value) || null;
});

// Filter pencarian teks atau kode pertanyaan
const searchQuery = ref('');
const isReordering = ref(false);

// Pertanyaan yang termasuk di dalam section aktif dan lolos filter pencarian
const activeSectionQuestions = computed(() => {
    if (!activeSectionId.value) return [];

    return props.questions
        .filter((q) => q.question_section_id === activeSectionId.value)
        .filter((q) => {
            if (!searchQuery.value.trim()) return true;
            const query = searchQuery.value.toLowerCase();
            const matchCode = q.code?.toLowerCase().includes(query);
            const matchText = q.question_text?.toLowerCase().includes(query);
            return matchCode || matchText;
        })
        .sort((a, b) => a.order - b.order);
});

// ========================================================
// 2. MODAL PERTANYAAN (TAMBAH & EDIT)
// ========================================================
const showQuestionModal = ref(false);
const isEditingQuestion = ref(false);
const selectedQuestion = ref(null);

const openAddQuestionModal = () => {
    isEditingQuestion.value = false;
    selectedQuestion.value = null;
    showQuestionModal.value = true;
};

const openEditQuestionModal = (q) => {
    isEditingQuestion.value = true;
    selectedQuestion.value = q;
    showQuestionModal.value = true;
};

const closeQuestionModal = () => {
    showQuestionModal.value = false;
    selectedQuestion.value = null;
};

// Hapus butir pertanyaan via SweetAlert2
const handleDeleteQuestion = (q) => {
    Swal.fire({
        title: 'Hapus Pertanyaan?',
        html: `
            <div class="text-center space-y-2">
                <p class="text-sm text-gray-600">Apakah Anda yakin ingin menghapus butir pertanyaan:</p>
                <div class="inline-block px-3 py-1 rounded-lg bg-red-50 text-red-700 font-mono font-bold text-sm border border-red-200">
                    ${q.code} — ${q.question_text?.substring(0, 50)}...
                </div>
                <p class="text-xs text-red-500 font-medium">Seluruh opsi jawaban terkait juga akan terhapus.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/superadmin/pertanyaan/${q.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Pertanyaan ${q.code} telah dihapus.`,
                        icon: 'success',
                        confirmButtonColor: '#005B3C',
                        confirmButtonText: 'Tutup',
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Gagal Menghapus!',
                        text: 'Terjadi kesalahan pada sistem saat menghapus data.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                    });
                },
            });
        }
    });
};

// Pindah urutan pertanyaan naik / turun
const handleMoveQuestion = (q, direction) => {
    isReordering.value = true;
    router.post(
        '/superadmin/pertanyaan/reorder',
        {
            id: q.id,
            direction: direction,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                isReordering.value = false;
            },
        }
    );
};

// ========================================================
// 3. MODAL OPSI JAWABAN (TAMBAH & EDIT)
// ========================================================
const showOptionModal = ref(false);
const isEditingOption = ref(false);
const selectedQuestionForOption = ref(null);
const selectedOption = ref(null);

const openAddOptionModal = (q) => {
    selectedQuestionForOption.value = q;
    isEditingOption.value = false;
    selectedOption.value = null;
    showOptionModal.value = true;
};

const openEditOptionModal = (q, opt) => {
    selectedQuestionForOption.value = q;
    isEditingOption.value = true;
    selectedOption.value = opt;
    showOptionModal.value = true;
};

const closeOptionModal = () => {
    showOptionModal.value = false;
    selectedQuestionForOption.value = null;
    selectedOption.value = null;
};

// Hapus opsi jawaban via SweetAlert2
const handleDeleteOption = (opt) => {
    Swal.fire({
        title: 'Hapus Opsi Jawaban?',
        text: `Hapus opsi "${opt.option_text}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/superadmin/pertanyaan/options/${opt.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Pilihan opsi jawaban telah dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#005B3C',
                        confirmButtonText: 'Tutup',
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Gagal Menghapus!',
                        text: 'Terjadi kendala saat menghapus opsi jawaban.',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                    });
                },
            });
        }
    });
};
</script>

<template>
    <Head title="Kelola Kuesioner - Super Admin" />

    <div class="min-h-screen bg-[#f8fafc] flex flex-col font-sans pb-24">
        
        <!-- Navbar Terpadu -->
        <Navbar />

        <!-- Header Profil Style (Elegan & Muted) -->
        <header class="bg-gradient-to-r from-[#005B3C] to-[#007b55] pt-12 pb-24 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Kelola Kuesioner
                </h1>
                <p class="text-green-100 font-medium mt-2 max-w-2xl text-sm sm:text-base leading-relaxed">
                    Konfigurasi butir pertanyaan, opsi jawaban, dan alur percabangan (*jump logic*) kuesioner Tracer Study.
                </p>
            </div>
        </header>

        <!-- Main Card Container (Mirip Form Profil Alumni) -->
        <main class="w-full max-w-[1400px] mx-auto -mt-16 px-4 sm:px-6 lg:px-8 relative z-20">
            <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden relative transition-all duration-300">
                
                <!-- Section Tabs (Terintegrasi rapi di bagian atas kartu) -->
                <SectionTabs
                    :sections="sections"
                    :activeSectionId="activeSectionId"
                    :questions="questions"
                    @select="handleSelectSection"
                />

                <!-- Konten Bagian Kuesioner -->
                <div class="p-6 md:p-10 space-y-6">
                    
                    <!-- Bar Aksi: Judul Bagian, Search, dan Tombol Tambah -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-100">
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-900 tracking-tight">
                                {{ currentActiveSection?.title || 'Bagian Kuesioner' }}
                            </h2>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5 font-medium">
                                Bagian {{ currentActiveSection?.order || '•' }} • Memuat {{ activeSectionQuestions.length }} butir pertanyaan aktif.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <!-- Search Bar -->
                            <div class="relative min-w-[240px]">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Cari kode atau teks..."
                                    class="w-full pl-9 pr-4 py-2.5 rounded-xl text-xs sm:text-sm border border-gray-200 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent bg-gray-50/50"
                                />
                            </div>

                            <!-- Tombol Tambah Pertanyaan (Gaya Tombol Alumni) -->
                            <button
                                type="button"
                                @click="openAddQuestionModal"
                                class="px-6 py-2.5 bg-gray-900 hover:bg-[#005B3C] text-white font-bold text-xs sm:text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0 active:scale-98"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah Pertanyaan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Daftar Kartu Pertanyaan -->
                    <div v-if="activeSectionQuestions.length > 0" class="space-y-4">
                        <QuestionCard
                            v-for="(q, index) in activeSectionQuestions"
                            :key="q.id"
                            :question="q"
                            :index="index"
                            :totalInActiveSection="activeSectionQuestions.length"
                            :targetQuestionMap="targetQuestionMap"
                            :isReordering="isReordering"
                            @editQuestion="openEditQuestionModal"
                            @deleteQuestion="handleDeleteQuestion"
                            @moveQuestion="handleMoveQuestion"
                            @addOption="openAddOptionModal"
                            @editOption="openEditOptionModal"
                            @deleteOption="handleDeleteOption"
                        />
                    </div>

                    <!-- Empty State Bersih & Minimal -->
                    <div 
                        v-else 
                        class="p-12 text-center border border-dashed border-gray-200 rounded-2xl bg-gray-50/50 space-y-3"
                    >
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-[#005B3C] mx-auto flex items-center justify-center text-xl font-bold">
                            ?
                        </div>
                        <h3 class="text-base font-bold text-gray-800">
                            {{ searchQuery ? 'Pertanyaan Tidak Ditemukan' : 'Belum Ada Pertanyaan di Bagian Ini' }}
                        </h3>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto">
                            {{ searchQuery ? `Tidak ada hasil pencarian untuk "${searchQuery}".` : 'Bagian ini belum memiliki butir pertanyaan. Tambahkan butir pertanyaan baru untuk memulai.' }}
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal Tambah / Edit Pertanyaan -->
        <QuestionModal
            :show="showQuestionModal"
            :isEdit="isEditingQuestion"
            :question="selectedQuestion"
            :sections="sections"
            :prodis="prodis"
            :defaultSectionId="activeSectionId"
            :nextOrder="activeSectionQuestions.length + 1"
            @close="closeQuestionModal"
            @saved="closeQuestionModal"
        />

        <!-- Modal Tambah / Edit Opsi Jawaban & Jump Logic -->
        <OptionModal
            :show="showOptionModal"
            :isEdit="isEditingOption"
            :question="selectedQuestionForOption"
            :option="selectedOption"
            :availableJumpTargets="availableJumpTargets"
            @close="closeOptionModal"
            @saved="closeOptionModal"
        />
    </div>
</template>
