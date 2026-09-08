<!--
  Halaman Kelola Pertanyaan & Alur Branching Kuesioner (Superadmin)
  
  Fungsi:
  Portal kendali terpusat bagi Superadmin untuk mengelola seluruh instrumen kuesioner Tracer Study.
  Superadmin memiliki wewenang penuh untuk:
  1. Menavigasi section kuesioner secara terstruktur per-bagian (Tabs horizontal).
  2. Menambah, mengedit, dan menghapus butir pertanyaan.
  3. Mengatur alur logika percabangan (jump logic / lompatan pertanyaan ala Google Forms).
  4. Menyusun urutan butir pertanyaan dalam section yang sama (naik/turun).
  5. Mengelola pilihan opsi jawaban beserta kodenya secara otomatis.
-->
<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

// Properti yang diterima dari SuperAdmin\KelolaPertanyaanController
const props = defineProps({
    questions: Array,
    sections: Array,
    prodis: Array,
    availableJumpTargets: Array,
    targetQuestionMap: Object,
});

// Fungsi proses logout superadmin dan kembali ke beranda (Home)
const logout = () => {
    router.post('/logout');
};

// ========================================================
// 1. STATE NAVIGASI PER SECTION & FILTER
// ========================================================
// Storage key untuk menyimpan section aktif pada browser Superadmin saat refresh
const SUPERADMIN_SECTION_STORAGE_KEY = 'tracerstudy_superadmin_pertanyaan_section';

// Mendapatkan section ID awal (dari parameter URL, cache localStorage, atau section pertama)
const getInitialSectionId = () => {
    if (typeof window !== 'undefined') {
        const urlParams = new URLSearchParams(window.location.search);
        const secParam = urlParams.get('sec_id');
        if (secParam) {
            const parsed = parseInt(secParam, 10);
            if (props.sections.some(s => s.id === parsed)) return parsed;
        }
        const cached = localStorage.getItem(SUPERADMIN_SECTION_STORAGE_KEY);
        if (cached) {
            const parsed = parseInt(cached, 10);
            if (props.sections.some(s => s.id === parsed)) return parsed;
        }
    }
    return props.sections.length > 0 ? props.sections[0].id : null;
};

// Section ID yang sedang aktif
const activeSectionId = ref(getInitialSectionId());

// Pantau perubahan section aktif dan sinkronkan ke URL & localStorage
watch(activeSectionId, (newId) => {
    if (typeof window !== 'undefined' && newId) {
        localStorage.setItem(SUPERADMIN_SECTION_STORAGE_KEY, newId.toString());
        const url = new URL(window.location.href);
        url.searchParams.set('sec_id', newId.toString());
        window.history.replaceState({}, '', url.toString());
    }
});

// Mode Tampilan: 'list' (Daftar Alur Kuesioner) atau 'form' (Formulir CRUD Terpisah)
const currentView = ref('list');
const isEditingQuestion = ref(false);

// Filter pencarian teks atau kode pertanyaan
const searchQuery = ref('');
const isReordering = ref(false);

// Pertanyaan yang termasuk di dalam section yang sedang aktif dan sesuai kueri pencarian
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

// Objek data section yang sedang aktif
const currentActiveSection = computed(() => {
    return props.sections.find((s) => s.id === activeSectionId.value) || null;
});

// ========================================================
// 2. FORM PERTANYAAN (CRUD TERPISAH)
// ========================================================
const questionForm = useForm({
    id: null,
    question_section_id: '',
    prodi_id: null,
    code: '',
    question_text: '',
    type: 'single_choice',
    is_required: true,
    order: null,
});

// Buka form tambah pertanyaan baru
const openAddQuestionForm = () => {
    isEditingQuestion.value = false;
    questionForm.reset();
    questionForm.question_section_id = activeSectionId.value || (props.sections.length > 0 ? props.sections[0].id : '');
    questionForm.prodi_id = null;
    questionForm.type = 'single_choice';
    questionForm.is_required = true;
    questionForm.order = activeSectionQuestions.value.length + 1;
    currentView.value = 'form';
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Buka form edit pertanyaan
const openEditQuestionForm = (q) => {
    isEditingQuestion.value = true;
    questionForm.id = q.id;
    questionForm.question_section_id = q.question_section_id;
    questionForm.prodi_id = q.prodi_id || null;
    questionForm.code = q.code;
    questionForm.question_text = q.question_text;
    questionForm.type = q.type;
    questionForm.is_required = !!q.is_required;
    questionForm.order = q.order;
    currentView.value = 'form';
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Batalkan form dan kembali ke mode daftar
const cancelQuestionForm = () => {
    questionForm.reset();
    currentView.value = 'list';
};

// Submit simpan atau perbarui data pertanyaan ke rute superadmin
const submitQuestion = () => {
    if (isEditingQuestion.value) {
        questionForm.put(`/superadmin/pertanyaan/${questionForm.id}`, {
            onSuccess: () => {
                currentView.value = 'list';
            },
        });
    } else {
        questionForm.post('/superadmin/pertanyaan', {
            onSuccess: () => {
                currentView.value = 'list';
            },
        });
    }
};

// Hapus butir pertanyaan beserta opsi jawabannya
const deleteQuestion = (q) => {
    if (confirm(`Hapus pertanyaan ${q.code}? Semua opsi jawaban terkait juga akan terhapus.`)) {
        router.delete(`/superadmin/pertanyaan/${q.id}`, {
            preserveScroll: true,
        });
    }
};

// ========================================================
// 3. PEMINDAHAN PERTANYAAN (HANYA DALAM SECTION SAMA)
// ========================================================
const moveQuestion = (q, direction) => {
    isReordering.value = true;
    router.post('/superadmin/pertanyaan/reorder', {
        id: q.id,
        direction: direction,
    }, {
        preserveScroll: true,
        onFinish: () => {
            isReordering.value = false;
        },
    });
};

// ========================================================
// 4. MODAL OPSI JAWABAN (SEDERHANA TANPA URUTAN MANUAL)
// ========================================================
const showOptionModal = ref(false);
const isEditingOption = ref(false);
const selectedQuestion = ref(null);

const optionForm = useForm({
    id: null,
    code: '',
    option_text: '',
    jump_to: '',
});

// Buka modal untuk menambah opsi jawaban
const openAddOptionModal = (q) => {
    selectedQuestion.value = q;
    isEditingOption.value = false;
    optionForm.reset();
    optionForm.code = '';
    optionForm.option_text = '';
    optionForm.jump_to = '';
    showOptionModal.value = true;
};

// Buka modal untuk mengedit opsi jawaban
const openEditOptionModal = (q, opt) => {
    selectedQuestion.value = q;
    isEditingOption.value = true;
    optionForm.id = opt.id;
    optionForm.code = opt.code || '';
    optionForm.option_text = opt.option_text;
    optionForm.jump_to = opt.jump_to || '';
    showOptionModal.value = true;
};

// Submit simpan opsi ke rute superadmin
const submitOption = () => {
    if (isEditingOption.value) {
        optionForm.put(`/superadmin/pertanyaan/options/${optionForm.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showOptionModal.value = false;
            },
        });
    } else {
        optionForm.post(`/superadmin/pertanyaan/${selectedQuestion.value.id}/options`, {
            preserveScroll: true,
            onSuccess: () => {
                showOptionModal.value = false;
            },
        });
    }
};

// Hapus opsi jawaban tertentu
const deleteOption = (opt) => {
    if (confirm(`Hapus pilihan opsi "${opt.option_text}"?`)) {
        router.delete(`/superadmin/pertanyaan/options/${opt.id}`, {
            preserveScroll: true,
        });
    }
};

// Mengambil judul/teks pertanyaan target berdasarkan kode jump_to
const getTargetQuestionTitle = (code) => {
    if (!code) return '';
    return props.targetQuestionMap?.[code] || '';
};

// Format nama label tipe pertanyaan
const getTypeLabel = (type) => {
    const labels = {
        single_choice: 'Pilihan Tunggal (Radio)',
        multiple_choice: 'Pilihan Ganda (Checkbox)',
        text: 'Isian Teks Singkat / Uraian',
        number: 'Isian Angka (Numerik)',
        radio_input: 'Radio dengan Isian Angka',
        radio_text: 'Radio dengan Isian Teks',
        multiple_number: 'Isian Multi Finansial / Numerik',
        matrix: 'Matriks / Skala Penilaian',
        matrix_dual: 'Matriks Komparasi Ganda',
        searchable_select: 'Dropdown Pencarian',
    };
    return labels[type] || type;
};
</script>

<template>
    <Head title="Kelola Kuesioner - Superadmin UKDW" />

    <div class="min-h-screen bg-[#f8fafc] text-slate-800 font-sans pb-24">
        
        <!-- Navbar Superadmin -->
        <nav class="bg-white/95 backdrop-blur-md shadow-xs border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    
                    <!-- Logo & Brand Institusional -->
                    <div class="flex items-center space-x-3">
                        <img src="/uploads/landing/2.png" alt="Logo UKDW" class="h-10 w-10 object-contain" onerror="this.style.display='none'" />
                        <div class="border-l border-gray-200 pl-3">
                            <span class="text-xs uppercase tracking-wider text-gray-500 font-bold block leading-tight">Tracer Study UKDW</span>
                            <span class="text-base font-bold text-gray-900 leading-tight">Pusat Kendali Super Admin</span>
                        </div>
                    </div>

                    <!-- Navigasi Menu Superadmin -->
                    <div class="hidden md:flex items-center space-x-6">
                        <Link 
                            href="/superadmin/dashboard" 
                            class="text-sm font-semibold text-gray-600 hover:text-[#005B3C] transition-colors"
                        >
                            Dashboard
                        </Link>
                        <Link 
                            href="/superadmin/pertanyaan" 
                            class="text-sm font-bold text-[#005B3C] border-b-2 border-[#005B3C] pb-1"
                        >
                            Kelola Kuesioner
                        </Link>
                    </div>

                    <!-- Profil & Logout -->
                    <div class="flex items-center space-x-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold bg-emerald-50 text-[#005B3C] border border-emerald-200 uppercase tracking-wider hidden sm:inline-block">
                            Hak Akses Superadmin
                        </span>
                        <button 
                            @click="logout" 
                            class="px-4 py-2 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                        >
                            Keluar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subnav Mobile -->
            <div class="md:hidden px-4 py-2 bg-gray-50 border-t border-gray-100 flex justify-around text-xs font-semibold">
                <Link href="/superadmin/dashboard" class="text-gray-600">Dashboard</Link>
                <Link href="/superadmin/pertanyaan" class="text-[#005B3C] font-bold">Kelola Kuesioner</Link>
            </div>
        </nav>

        <!-- Banner Header (Warna Resmi UKDW: Hijau #005B3C & Aksen Kuning #FACC15) -->
        <header class="bg-gradient-to-r from-[#005B3C] to-[#007b55] pt-10 pb-20 relative overflow-hidden text-white">
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 text-white rounded-full text-xs font-bold tracking-wide backdrop-blur-sm mb-2">
                        Konfigurasi Instrumen Kuesioner &mdash; Superadmin
                    </span>
                    <h1 class="text-3xl font-extrabold tracking-tight">Kelola Butir Pertanyaan & Alur Branching</h1>
                    <p class="text-green-100 text-sm mt-1 max-w-2xl">
                        Atur struktur instrumen pertanyaan per bagian, logika alur percabangan (*jump logic*), serta sasaran program studi kuesioner universitas.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-3">
                    <button 
                        v-if="currentView === 'form'"
                        @click="cancelQuestionForm"
                        class="px-5 py-2.5 bg-white/15 hover:bg-white/25 text-white font-bold text-xs rounded-xl backdrop-blur-sm transition-all flex items-center gap-1.5"
                    >
                        <span>&larr;</span>
                        <span>Kembali ke Daftar Alur</span>
                    </button>
                    <button 
                        v-if="currentView === 'list'"
                        @click="openAddQuestionForm"
                        class="px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-green-950 font-bold text-xs uppercase tracking-wider rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex items-center gap-2"
                    >
                        <span>+ Tambah Pertanyaan Baru</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Wrapper Konten -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
            
            <!-- Notifikasi Flash Message -->
            <div v-if="$page.props.flash?.success" class="mb-4 p-4 rounded-xl bg-white border-l-4 border-[#005B3C] shadow-sm flex items-center justify-between text-xs text-green-900 font-semibold">
                <span>{{ $page.props.flash.success }}</span>
            </div>

            <!-- ======================================================== -->
            <!-- TAMPILAN 1: FORMULIR TAMBAH / EDIT PERTANYAAN (TERPISAH) -->
            <!-- ======================================================== -->
            <div v-if="currentView === 'form'" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold" :class="isEditingQuestion ? 'bg-yellow-100 text-yellow-900' : 'bg-green-100 text-[#005B3C]'">
                            {{ isEditingQuestion ? 'Edit Data Pertanyaan' : 'Tambah Pertanyaan Baru' }}
                        </span>
                        <h2 class="text-xl font-extrabold text-gray-900 mt-1">
                            {{ isEditingQuestion ? `Edit Butir Pertanyaan [ ${questionForm.code} ]` : 'Formulir Penambahan Butir Pertanyaan Baru' }}
                        </h2>
                    </div>
                    <button @click="cancelQuestionForm" class="text-gray-400 hover:text-gray-600 text-sm font-semibold">
                        Batal
                    </button>
                </div>

                <form @submit.prevent="submitQuestion" class="p-8 space-y-6 max-w-4xl">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Bagian Kuesioner (Section) <span class="text-red-500">*</span>
                            </label>
                            <select v-model="questionForm.question_section_id" required class="w-full text-sm font-medium rounded-xl border-gray-300 focus:border-[#005B3C] focus:ring-[#005B3C] shadow-sm py-2.5">
                                <option v-for="sec in sections" :key="sec.id" :value="sec.id">
                                    {{ sec.title }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Target Program Studi
                            </label>
                            <select v-model="questionForm.prodi_id" class="w-full text-sm font-medium rounded-xl border-gray-300 focus:border-[#005B3C] focus:ring-[#005B3C] shadow-sm py-2.5">
                                <option :value="null">Semua Program Studi (Umum)</option>
                                <option v-for="p in prodis" :key="p.id" :value="p.id">
                                    {{ p.kode_prodi }} — {{ p.nama_prodi }}
                                </option>
                            </select>
                            <span class="text-xs text-gray-400 block mt-1">Pilih prodi jika soal ini hanya disajikan khusus untuk prodi tertentu (misal: F2E khusus Teologi).</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Kode Pertanyaan <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                v-model="questionForm.code" 
                                placeholder="Contoh: F3, F8, F11" 
                                required 
                                class="w-full text-sm font-mono font-bold rounded-xl border-gray-300 focus:border-[#005B3C] focus:ring-[#005B3C] shadow-sm py-2.5 uppercase"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Tipe Input Jawaban <span class="text-red-500">*</span>
                            </label>
                            <select v-model="questionForm.type" required class="w-full text-sm font-semibold rounded-xl border-gray-300 focus:border-[#005B3C] focus:ring-[#005B3C] shadow-sm py-2.5">
                                <option value="single_choice">Pilihan Tunggal (Radio)</option>
                                <option value="multiple_choice">Pilihan Ganda (Checkbox)</option>
                                <option value="text">Isian Teks Singkat / Uraian</option>
                                <option value="number">Isian Angka (Numerik)</option>
                                <option value="radio_input">Radio dengan Isian Angka</option>
                                <option value="radio_text">Radio dengan Isian Teks</option>
                                <option value="multiple_number">Isian Multi Finansial / Numerik</option>
                                <option value="searchable_select">Dropdown Pencarian</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                            Teks / Bunyi Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            v-model="questionForm.question_text" 
                            rows="3" 
                            required 
                            placeholder="Tuliskan isi teks pertanyaan..." 
                            class="w-full text-sm font-medium rounded-xl border-gray-300 focus:border-[#005B3C] focus:ring-[#005B3C] shadow-sm p-3"
                        ></textarea>
                    </div>

                    <div class="pt-3 border-t border-gray-100">
                        <label class="inline-flex items-center cursor-pointer gap-3">
                            <input 
                                type="checkbox" 
                                v-model="questionForm.is_required" 
                                class="rounded border-gray-300 text-[#005B3C] focus:ring-[#005B3C] h-4 w-4"
                            >
                            <div>
                                <span class="text-sm font-bold text-gray-800 block">Pertanyaan Wajib Diisi (Required)</span>
                                <span class="text-xs text-gray-400">Alumni tidak dapat menyelesaikan section jika belum diisi.</span>
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="cancelQuestionForm" 
                            class="px-6 py-2.5 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="questionForm.processing" 
                            class="px-8 py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-[#005B3C] hover:bg-[#00422c] rounded-xl shadow-md transition-all disabled:opacity-50"
                        >
                            {{ isEditingQuestion ? 'Simpan Perubahan' : 'Simpan Pertanyaan' }}
                        </button>
                    </div>

                </form>
            </div>

            <!-- ======================================================== -->
            <!-- TAMPILAN 2: DAFTAR PERTANYAAN BERDASARKAN SECTION (TABS) -->
            <!-- ======================================================== -->
            <div v-else class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                
                <!-- Navigasi Horizontal Tabs Section -->
                <div class="flex overflow-x-auto border-b border-gray-100 sticky top-20 z-30 bg-white/95 backdrop-blur-md px-2">
                    <button 
                        v-for="sec in sections" 
                        :key="sec.id"
                        type="button" 
                        @click="activeSectionId = sec.id" 
                        :class="activeSectionId === sec.id ? 'border-[#005B3C] text-[#005B3C] font-extrabold bg-green-50/50' : 'border-transparent text-gray-500 hover:text-gray-800 font-semibold'" 
                        class="whitespace-nowrap py-4 px-6 border-b-2 text-sm transition-all flex-shrink-0 text-center flex items-center gap-2"
                    >
                        <span>{{ sec.title }}</span>
                        <span 
                            class="px-2 py-0.5 text-[10px] rounded-full font-mono font-bold"
                            :class="activeSectionId === sec.id ? 'bg-[#005B3C] text-white' : 'bg-gray-100 text-gray-600'"
                        >
                            {{ questions.filter(q => q.question_section_id === sec.id).length }}
                        </span>
                    </button>
                </div>

                <!-- Kontrol Pencarian & Ringkasan Section -->
                <div class="p-6 bg-gray-50/60 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-900">
                            {{ currentActiveSection?.title || 'Daftar Pertanyaan' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Menampilkan {{ activeSectionQuestions.length }} butir pertanyaan pada bagian ini.
                        </p>
                    </div>

                    <div class="w-full sm:w-72">
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            placeholder="Cari kode atau teks di bagian ini..." 
                            class="w-full text-xs rounded-xl border-gray-300 py-2 px-3 focus:border-[#005B3C] focus:ring-[#005B3C]"
                        >
                    </div>
                </div>

                <!-- Daftar Kartu Pertanyaan di Section Aktif -->
                <div class="p-6 space-y-6">
                    
                    <div 
                        v-for="(q, idx) in activeSectionQuestions" 
                        :key="q.id" 
                        class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden"
                    >
                        <!-- Header Kartu Pertanyaan -->
                        <div class="p-4 bg-gray-50/80 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            
                            <!-- Kontrol Urutan & Badge -->
                            <div class="flex items-center gap-2.5 flex-wrap">
                                
                                <!-- Tombol Naik / Turun (Hanya dalam Section yang Sama) -->
                                <div class="inline-flex rounded-lg border border-gray-200 bg-white overflow-hidden shadow-xs">
                                    <button 
                                        @click="moveQuestion(q, 'up')" 
                                        :disabled="idx === 0 || isReordering"
                                        title="Pindahkan ke atas di bagian ini"
                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed border-r border-gray-100 transition-colors"
                                    >
                                        &uarr;
                                    </button>
                                    <button 
                                        @click="moveQuestion(q, 'down')" 
                                        :disabled="idx === activeSectionQuestions.length - 1 || isReordering"
                                        title="Pindahkan ke bawah di bagian ini"
                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                    >
                                        &darr;
                                    </button>
                                </div>

                                <!-- Kode Pertanyaan (Hijau Resmi UKDW) -->
                                <span class="px-2.5 py-0.5 bg-[#005B3C] text-white font-mono font-bold text-xs rounded-md">
                                    {{ q.code }}
                                </span>

                                <!-- Tipe Input -->
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 font-semibold text-xs rounded-md">
                                    {{ getTypeLabel(q.type) }}
                                </span>

                                <!-- Wajib / Opsional (Kuning Elegan / Netral) -->
                                <span v-if="q.is_required" class="px-2 py-0.5 bg-yellow-100 text-yellow-900 font-semibold text-xs rounded-md border border-yellow-300">
                                    Wajib
                                </span>
                                <span v-else class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-md">
                                    Opsional
                                </span>

                                <!-- Target Prodi Khusus -->
                                <span v-if="q.prodi" class="px-2 py-0.5 bg-purple-50 text-purple-800 font-bold text-xs rounded-md border border-purple-200">
                                    Khusus: {{ q.prodi.nama_prodi }}
                                </span>
                            </div>

                            <!-- Tombol Aksi Soal -->
                            <div class="flex items-center gap-3">
                                <button 
                                    @click="openEditQuestionForm(q)" 
                                    class="text-xs font-bold text-[#005B3C] hover:underline"
                                >
                                    Edit Pertanyaan
                                </button>
                                <span class="text-gray-300">|</span>
                                <button 
                                    @click="deleteQuestion(q)" 
                                    class="text-xs font-semibold text-gray-500 hover:text-red-600"
                                >
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Teks Pertanyaan -->
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-900 leading-snug">
                                {{ q.question_text }}
                            </h3>
                        </div>

                        <!-- Tabel Opsi Jawaban & Alur Percabangan -->
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-600">
                                    Pilihan Opsi Jawaban & Alur Percabangan ({{ q.options?.length || 0 }})
                                </span>
                                <button 
                                    @click="openAddOptionModal(q)" 
                                    class="px-3 py-1.5 text-xs font-bold text-[#005B3C] bg-green-50 hover:bg-green-100 rounded-lg transition-colors border border-green-200"
                                >
                                    + Tambah Pilihan Opsi
                                </button>
                            </div>

                            <!-- Bila Tanpa Opsi -->
                            <div v-if="!q.options || q.options.length === 0" class="bg-gray-50 border border-dashed border-gray-200 rounded-lg p-3 text-center text-xs text-gray-400 italic">
                                Pertanyaan tipe ini berupa isian langsung tanpa opsi pilihan.
                            </div>

                            <!-- Tabel Formal Rapi -->
                            <div v-else class="overflow-x-auto border border-gray-100 rounded-xl">
                                <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
                                    <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                                        <tr>
                                            <th class="py-2.5 px-4 w-12 text-center">No</th>
                                            <th class="py-2.5 px-4 w-28">Kode Opsi</th>
                                            <th class="py-2.5 px-4">Teks Jawaban</th>
                                            <th class="py-2.5 px-4 w-80">Setelah Memilih Opsi (Branching)</th>
                                            <th class="py-2.5 px-4 w-24 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 bg-white">
                                        <tr v-for="(opt, optIdx) in q.options" :key="opt.id" class="hover:bg-gray-50/60 transition-colors">
                                            <td class="py-3 px-4 text-center font-bold text-gray-400">
                                                {{ optIdx + 1 }}
                                            </td>
                                            <td class="py-3 px-4 font-mono font-bold text-gray-700">
                                                {{ opt.code || '-' }}
                                            </td>
                                            <td class="py-3 px-4 font-medium text-gray-900">
                                                {{ opt.option_text }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <!-- Jika ada jump_to: tampilkan kode & teks pertanyaan target dengan aksen kuning landing page -->
                                                <div v-if="opt.jump_to" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 text-yellow-950 border border-yellow-300 rounded-md font-medium text-xs max-w-md">
                                                    <span class="font-bold text-[#005B3C]">Lompat ke {{ opt.jump_to }}:</span>
                                                    <span class="truncate" :title="getTargetQuestionTitle(opt.jump_to)">
                                                        {{ getTargetQuestionTitle(opt.jump_to) || 'Pertanyaan ' + opt.jump_to }}
                                                    </span>
                                                </div>
                                                <div v-else class="text-gray-400 text-xs">
                                                    Lanjut ke pertanyaan berikutnya (Alur Normal)
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-right space-x-2">
                                                <button 
                                                    @click="openEditOptionModal(q, opt)" 
                                                    class="text-[#005B3C] hover:underline font-bold text-xs"
                                                >
                                                    Edit
                                                </button>
                                                <button 
                                                    @click="deleteOption(opt)" 
                                                    class="text-gray-400 hover:text-red-600 font-semibold text-xs"
                                                >
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                    <!-- Bila Tidak Ada Pertanyaan di Section -->
                    <div v-if="activeSectionQuestions.length === 0" class="p-12 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <p class="text-sm font-bold text-gray-700">Belum ada butir pertanyaan pada bagian ini.</p>
                        <p class="text-xs text-gray-400 mt-1">Klik "+ Tambah Pertanyaan Baru" untuk mulai menambahkan pertanyaan ke bagian ini.</p>
                    </div>

                </div>

            </div>

        </main>

        <!-- ======================================================== -->
        <!-- MODAL FOKUS: TAMBAH / EDIT OPSI JAWABAN (SEDERHANA)       -->
        <!-- ======================================================== -->
        <div v-if="showOptionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl border border-gray-100 max-w-lg w-full shadow-2xl overflow-hidden">
                
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">
                            {{ isEditingOption ? 'Edit Pilihan Opsi' : 'Tambah Pilihan Opsi' }}
                        </h3>
                        <p class="text-xs text-gray-500 font-mono mt-0.5">
                            Pertanyaan: [ {{ selectedQuestion?.code }} ] {{ selectedQuestion?.question_text }}
                        </p>
                    </div>
                    <button @click="showOptionModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg leading-none">
                        &times;
                    </button>
                </div>

                <form @submit.prevent="submitOption" class="p-6 space-y-4">
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Teks Pilihan Jawaban <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            v-model="optionForm.option_text" 
                            required 
                            placeholder="Contoh: Bekerja purna waktu / Wirausaha / Belum bekerja" 
                            class="w-full text-xs font-semibold rounded-xl border-gray-300 focus:border-[#005B3C] focus:ring-[#005B3C] shadow-sm py-2.5"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Kode Opsi (Opsional)
                        </label>
                        <input 
                            type="text" 
                            v-model="optionForm.code" 
                            :placeholder="`Otomatis (misal: ${selectedQuestion?.code || 'F3'}-01)`" 
                            class="w-full text-xs font-mono font-bold rounded-xl border-gray-300 focus:border-[#005B3C] focus:ring-[#005B3C] shadow-sm py-2 uppercase"
                        >
                        <span class="text-[10px] text-gray-400 block mt-1">Kosongkan jika ingin nomor kode digenerate otomatis.</span>
                    </div>

                    <!-- Dropdown Branching Lompatan -->
                    <div class="p-4 bg-yellow-50/70 rounded-xl border border-yellow-200 space-y-1.5">
                        <label class="block text-xs font-bold text-yellow-950">
                            Setelah Memilih Opsi Ini (Alur Branching / Lompatan)
                        </label>
                        <select 
                            v-model="optionForm.jump_to" 
                            class="w-full text-xs font-medium rounded-lg border-yellow-300 bg-white focus:border-[#005B3C] focus:ring-[#005B3C] text-gray-800 py-2"
                        >
                            <option value="">Lanjut ke pertanyaan berikutnya (Alur Normal)</option>
                            <option 
                                v-for="target in availableJumpTargets" 
                                :key="target.code" 
                                :value="target.code"
                            >
                                Lompat ke: [ {{ target.code }} ] {{ target.text }}
                            </option>
                        </select>
                        <span class="text-[11px] text-yellow-900 block leading-tight">
                            Pilih kode tujuan jika ingin melewati pertanyaan di antaranya saat opsi ini dipilih.
                        </span>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showOptionModal = false" 
                            class="px-4 py-2 text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="optionForm.processing" 
                            class="px-5 py-2 text-xs font-bold uppercase tracking-wider text-white bg-[#005B3C] hover:bg-[#00422c] rounded-xl shadow-md transition-all disabled:opacity-50"
                        >
                            {{ isEditingOption ? 'Simpan Perubahan' : 'Tambahkan Opsi' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</template>
