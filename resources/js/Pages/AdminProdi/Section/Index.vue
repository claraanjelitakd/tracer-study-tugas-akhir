<!--
  Halaman Kelola Bagian (Section) Kuesioner Program Studi
  File: resources/js/Pages/AdminProdi/Section/Index.vue

  Desain Standar Super Admin UKDW:
  - Header Hijau Solid Resmi UKDW #0D542B
  - Kartu Putih Bersih, Bebas Border Bertumpuk
  - Minimalis, Tanpa Icon/Grafik Berlebihan
-->
<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';
import Navbar from '../Components/Navbar.vue';

const props = defineProps({
    user: Object,
    prodi: Object,
    sections: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({
            total_sections: 0,
            total_questions: 0,
        }),
    },
});

// Search State
const searchQuery = ref('');
const isReordering = ref(false);

const filteredSections = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.sections;
    }
    const q = searchQuery.value.toLowerCase();
    return props.sections.filter(s => 
        s.title?.toLowerCase().includes(q) || 
        s.description?.toLowerCase().includes(q) ||
        s.order?.toString().includes(q)
    );
});

// Modal State
const showModal = ref(false);
const isEdit = ref(false);

const form = useForm({
    id: null,
    title: '',
    description: '',
    order: 1,
});

const openCreateModal = () => {
    isEdit.value = false;
    const maxOrder = props.sections.reduce((max, s) => Math.max(max, s.order || 0), 0);
    form.reset();
    form.id = null;
    form.title = '';
    form.description = '';
    form.order = maxOrder + 1;
    showModal.value = true;
};

const openEditModal = (sec) => {
    isEdit.value = true;
    form.id = sec.id;
    form.title = sec.title;
    form.description = sec.description || '';
    form.order = sec.order;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const handleSubmit = () => {
    if (isEdit.value) {
        form.put(`/prodi/sections/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diperbarui',
                    text: 'Bagian kuesioner program studi berhasil diperbarui.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    } else {
        form.post('/prodi/sections', {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Ditambahkan',
                    text: 'Bagian kuesioner program studi baru berhasil ditambahkan.',
                    confirmButtonColor: '#0D542B',
                });
            },
        });
    }
};

const handleMove = (sec, direction) => {
    isReordering.value = true;
    router.post('/prodi/sections/reorder', {
        id: sec.id,
        direction: direction,
    }, {
        preserveScroll: true,
        onFinish: () => {
            isReordering.value = false;
        },
    });
};

const handleDelete = (sec) => {
    const questionCount = sec.questions_count || 0;
    Swal.fire({
        title: 'Hapus Bagian Kuesioner?',
        text: questionCount > 0 
            ? `Bagian "${sec.title}" memiliki ${questionCount} pertanyaan di dalamnya. Seluruh pertanyaan dan opsi di dalamnya akan ikut terhapus permanen.`
            : `Apakah Anda yakin ingin menghapus bagian "${sec.title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((res) => {
        if (res.isConfirmed) {
            router.delete(`/prodi/sections/${sec.id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dihapus',
                        text: 'Bagian kuesioner telah dihapus.',
                        confirmButtonColor: '#0D542B',
                    });
                },
            });
        }
    });
};
</script>

<template>
    <Head :title="`Kelola Section Kuesioner - ${prodi?.nama_prodi || 'Program Studi'}`" />

    <div class="min-h-screen bg-[#f8fafc] text-gray-800 font-sans pb-24">
        <!-- Navbar Terpadu Admin Prodi -->
        <Navbar :user="user" :prodi="prodi" />

        <!-- Header Solid Hijau UKDW #0D542B -->
        <header class="bg-[#0D542B] text-white pt-10 pb-20">
            <div class="w-full max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 text-xs text-white/80 font-medium mb-2">
                        <Link href="/prodi/dashboard" class="hover:underline">Dashboard</Link>
                        <span>/</span>
                        <span class="text-white font-bold">Kelola Section</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Kelola Bagian (Section) Kuesioner
                    </h1>
                    <p class="text-white/90 text-sm sm:text-base font-normal mt-1 max-w-2xl leading-relaxed">
                        Atur struktur tahapan dan pembagian bagian kuesioner khusus Program Studi {{ prodi?.nama_prodi }}.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        @click="openCreateModal"
                        class="px-5 py-2.5 bg-[#FDC700] hover:bg-[#e5b500] text-black font-extrabold rounded-xl text-xs transition-colors shadow-xs cursor-pointer"
                    >
                        + Tambah Section Baru
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="w-full max-w-[1400px] mx-auto -mt-10 px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Kartu Ringkasan KPI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Total Bagian (Section)</span>
                    <span class="text-3xl font-extrabold text-gray-900 tracking-tight mt-2 block">{{ stats.total_sections }}</span>
                    <span class="text-xs text-gray-400 mt-1 block">Tahapan kuesioner aktif</span>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <span class="text-xs font-bold text-[#0D542B] uppercase tracking-wider block">Total Butir Pertanyaan</span>
                    <span class="text-3xl font-extrabold text-[#0D542B] tracking-tight mt-2 block">{{ stats.total_questions }}</span>
                    <span class="text-xs text-gray-400 mt-1 block">Telah terdistribusi dalam section</span>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm sm:col-span-2 lg:col-span-1 flex flex-col justify-center">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Akses Kuesioner</span>
                    <p class="text-xs text-gray-600 mt-1">
                        Pertanyaan dalam section ini akan muncul pada halaman kuesioner prodi alumni {{ prodi?.nama_prodi }}.
                    </p>
                    <div class="mt-3">
                        <Link 
                            href="/prodi/pertanyaan" 
                            class="text-xs font-bold text-[#0D542B] hover:underline"
                        >
                            Ke Halaman Kelola Butir Pertanyaan &rarr;
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tabel Section -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <!-- Search Bar -->
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                    <div class="w-full sm:w-80">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Cari nama bagian atau urutan..."
                            class="w-full text-xs rounded-xl border border-gray-200 bg-white py-2 px-3.5 font-medium text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        />
                    </div>
                    <div class="text-xs text-gray-500 font-medium">
                        Menampilkan <span class="font-bold text-gray-900">{{ filteredSections.length }}</span> bagian kuesioner
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100 uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-4 px-5 text-center w-20">Urutan</th>
                                <th class="py-4 px-5">Nama Bagian (Section)</th>
                                <th class="py-4 px-5">Deskripsi / Catatan Petunjuk</th>
                                <th class="py-4 px-5 text-center">Jumlah Soal</th>
                                <th class="py-4 px-5 text-center">Pindah Urutan</th>
                                <th class="py-4 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="filteredSections.length === 0">
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    Belum ada bagian kuesioner yang sesuai.
                                </td>
                            </tr>
                            <tr 
                                v-for="(sec, idx) in filteredSections" 
                                :key="sec.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="py-4 px-5 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#0D542B] text-white font-bold text-xs">
                                        {{ sec.order }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="font-extrabold text-gray-900 text-sm">{{ sec.title }}</div>
                                    <span class="text-[11px] text-gray-400 font-mono">ID: {{ sec.id }}</span>
                                </td>
                                <td class="py-4 px-5 max-w-xs text-gray-600">
                                    {{ sec.description || '-' }}
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                        {{ sec.questions_count || 0 }} Pertanyaan
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <button
                                            type="button"
                                            :disabled="idx === 0 || isReordering"
                                            @click="handleMove(sec, 'up')"
                                            class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                                            title="Pindah Naik"
                                        >
                                            &uarr;
                                        </button>
                                        <button
                                            type="button"
                                            :disabled="idx === filteredSections.length - 1 || isReordering"
                                            @click="handleMove(sec, 'down')"
                                            class="p-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer"
                                            title="Pindah Turun"
                                        >
                                            &darr;
                                        </button>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <button
                                            @click="openEditModal(sec)"
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-colors cursor-pointer"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="handleDelete(sec)"
                                            class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-lg transition-colors cursor-pointer"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Modal Tambah / Edit Section -->
        <div 
            v-if="showModal"
            class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4 backdrop-blur-xs"
        >
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-extrabold text-gray-900">
                        {{ isEdit ? 'Edit Bagian Kuesioner' : 'Tambah Bagian Kuesioner Baru' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 text-lg leading-none cursor-pointer">&times;</button>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Judul / Nama Bagian <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.title"
                            required
                            placeholder="Contoh: Evaluasi Capaian Pembelajaran Lulusan"
                            class="w-full text-xs rounded-xl border border-gray-200 py-2.5 px-3.5 text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        />
                        <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Deskripsi / Petunjuk Pengisian
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Tuliskan petunjuk atau keterangan tambahan untuk alumni..."
                            class="w-full text-xs rounded-xl border border-gray-200 py-2 px-3.5 text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        ></textarea>
                        <p v-if="form.errors.description" class="text-xs text-red-500 mt-1">{{ form.errors.description }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Nomor Urut Posisi
                        </label>
                        <input
                            type="number"
                            v-model.number="form.order"
                            min="1"
                            class="w-full text-xs rounded-xl border border-gray-200 py-2 px-3.5 text-gray-900 focus:border-[#0D542B] focus:ring-1 focus:ring-[#0D542B] outline-none"
                        />
                        <p v-if="form.errors.order" class="text-xs text-red-500 mt-1">{{ form.errors.order }}</p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2 text-xs font-extrabold text-white bg-[#0D542B] hover:bg-[#093c1f] rounded-xl transition-colors shadow-xs cursor-pointer disabled:opacity-50"
                        >
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Section' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
