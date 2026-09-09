<!--
  Komponen Modal Tambah / Edit Pertanyaan (QuestionModal.vue)
  
  Fungsi:
  Formulir modal dialog terpadu untuk membuat butir pertanyaan baru atau menyunting pertanyaan yang ada.
  Mendukung pemilihan section kuesioner, penentuan kode unik, pembatasan target prodi, tipe input,
  status wajib diisi, nomor urutan, serta feedback SweetAlert2.
-->
<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
    question: {
        type: Object,
        default: null,
    },
    sections: {
        type: Array,
        default: () => [],
    },
    prodis: {
        type: Array,
        default: () => [],
    },
    defaultSectionId: {
        type: [Number, String],
        default: '',
    },
    nextOrder: {
        type: Number,
        default: 1,
    },
});

const emit = defineEmits(['close', 'saved']);

// Formulir reaktif Inertia
const form = useForm({
    id: null,
    question_section_id: '',
    prodi_id: '',
    code: '',
    question_text: '',
    type: 'single_choice',
    is_required: true,
    order: null,
});

// Daftar jenis pertanyaan yang didukung sistem Tracer Study
const questionTypes = [
    { value: 'single_choice', label: 'Pilihan Tunggal (Radio)' },
    { value: 'multiple_choice', label: 'Pilihan Ganda (Checkbox)' },
    { value: 'text', label: 'Isian Teks Singkat' },
    { value: 'number', label: 'Isian Angka' },
    { value: 'multiple_number', label: 'Isian Nominal / Gaji (F13)' },
    { value: 'rating_5', label: 'Skala Rating (1-5 / F17)' },
    { value: 'textarea', label: 'Uraian Teks Panjang' },
    { value: 'dropdown', label: 'Dropdown Menu' },
];

// Pantau pembukaan modal dan inisialisasi form data
watch(() => props.show, (isOpen) => {
    if (isOpen) {
        form.clearErrors();
        if (props.isEdit && props.question) {
            form.id = props.question.id;
            form.question_section_id = props.question.question_section_id;
            form.prodi_id = props.question.prodi_id || '';
            form.code = props.question.code;
            form.question_text = props.question.question_text;
            form.type = props.question.type;
            form.is_required = !!props.question.is_required;
            form.order = props.question.order;
        } else {
            form.reset();
            form.id = null;
            form.question_section_id = props.defaultSectionId || (props.sections[0]?.id || '');
            form.prodi_id = '';
            form.code = '';
            form.question_text = '';
            form.type = 'single_choice';
            form.is_required = true;
            form.order = props.nextOrder;
        }
    }
});

// Tutup modal
const handleClose = () => {
    emit('close');
};

// Proses submit form ke backend Laravel
const handleSubmit = () => {
    if (props.isEdit) {
        form.put(`/superadmin/pertanyaan/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data pertanyaan berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#005B3C',
                    confirmButtonText: 'Selesai',
                });
                emit('saved');
                emit('close');
            },
            onError: (errors) => {
                let errorList = '<ul class="text-left text-xs list-disc list-inside space-y-1 mt-2">';
                for (let key in errors) {
                    errorList += `<li>${errors[key]}</li>`;
                }
                errorList += '</ul>';

                Swal.fire({
                    title: 'Gagal Memperbarui!',
                    html: '<p class="text-sm">Periksa kembali inputan Anda:</p>' + errorList,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Perbaiki',
                });
            },
        });
    } else {
        form.post('/superadmin/pertanyaan', {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Pertanyaan baru berhasil ditambahkan.',
                    icon: 'success',
                    confirmButtonColor: '#005B3C',
                    confirmButtonText: 'Selesai',
                });
                emit('saved');
                emit('close');
            },
            onError: (errors) => {
                let errorList = '<ul class="text-left text-xs list-disc list-inside space-y-1 mt-2">';
                for (let key in errors) {
                    errorList += `<li>${errors[key]}</li>`;
                }
                errorList += '</ul>';

                Swal.fire({
                    title: 'Gagal Menyimpan!',
                    html: '<p class="text-sm">Ada data yang belum valid:</p>' + errorList,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Perbaiki',
                });
            },
        });
    }
};
</script>

<template>
    <div 
        v-if="show" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
    >
        <!-- Backdrop Blur -->
        <div 
            class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" 
            @click="handleClose"
        ></div>

        <!-- Modal Dialog Card -->
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-2xl w-full border border-gray-100 overflow-hidden z-10 transform transition-all">
            
            <!-- Header Modal -->
            <div class="p-6 bg-gradient-to-r from-emerald-800 to-[#005B3C] text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center font-black text-lg text-yellow-300 shadow-xs">
                        {{ isEdit ? '✎' : '+' }}
                    </div>
                    <div>
                        <h2 class="text-lg font-black tracking-tight">
                            {{ isEdit ? 'Edit Butir Pertanyaan' : 'Tambah Pertanyaan Baru' }}
                        </h2>
                        <p class="text-xs text-emerald-100/90 font-medium">
                            {{ isEdit ? 'Perbarui informasi dan konfigurasi pertanyaan kuesioner.' : 'Isi formulir untuk menambahkan butir pertanyaan baru.' }}
                        </p>
                    </div>
                </div>

                <!-- Tombol Close -->
                <button 
                    type="button" 
                    @click="handleClose"
                    class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                >
                    ✕
                </button>
            </div>

            <!-- Form Body -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                
                <!-- Section Kuesioner -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Bagian Kuesioner (Section) <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="form.question_section_id"
                        required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm bg-gray-50/50"
                    >
                        <option value="" disabled>Pilih Bagian Kuesioner</option>
                        <option v-for="sec in sections" :key="sec.id" :value="sec.id">
                            Section {{ sec.order }}: {{ sec.title }}
                        </option>
                    </select>
                    <p v-if="form.errors.question_section_id" class="text-xs text-red-500 mt-1 font-semibold">
                        {{ form.errors.question_section_id }}
                    </p>
                </div>

                <!-- Grid: Kode & Target Prodi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Kode Pertanyaan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Kode Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.code"
                            required
                            placeholder="Contoh: F3, F13, F17-01"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm font-mono uppercase bg-gray-50/50"
                        />
                        <p v-if="form.errors.code" class="text-xs text-red-500 mt-1 font-semibold">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <!-- Target Prodi -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Target Program Studi
                        </label>
                        <select
                            v-model="form.prodi_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm bg-gray-50/50"
                        >
                            <option value="">Semua Program Studi (Umum)</option>
                            <option v-for="prodi in prodis" :key="prodi.id" :value="prodi.id">
                                [{{ prodi.kode_prodi }}] {{ prodi.nama_prodi }}
                            </option>
                        </select>
                        <p v-if="form.errors.prodi_id" class="text-xs text-red-500 mt-1 font-semibold">
                            {{ form.errors.prodi_id }}
                        </p>
                    </div>
                </div>

                <!-- Grid: Tipe Pertanyaan & Urutan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tipe Pertanyaan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Tipe Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="form.type"
                            required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm bg-gray-50/50"
                        >
                            <option v-for="t in questionTypes" :key="t.value" :value="t.value">
                                {{ t.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.type" class="text-xs text-red-500 mt-1 font-semibold">
                            {{ form.errors.type }}
                        </p>
                    </div>

                    <!-- Nomor Urut -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Nomor Urut Posisi
                        </label>
                        <input
                            type="number"
                            v-model.number="form.order"
                            min="1"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm bg-gray-50/50"
                        />
                        <p v-if="form.errors.order" class="text-xs text-red-500 mt-1 font-semibold">
                            {{ form.errors.order }}
                        </p>
                    </div>
                </div>

                <!-- Status Wajib Diisi -->
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/80">
                    <input
                        type="checkbox"
                        id="is_required_check"
                        v-model="form.is_required"
                        class="w-4 h-4 text-[#005B3C] rounded border-gray-300 focus:ring-[#005B3C] cursor-pointer"
                    />
                    <label for="is_required_check" class="text-xs font-bold text-emerald-950 cursor-pointer select-none">
                        Pertanyaan ini Wajib Diisi oleh Alumni (Required)
                    </label>
                </div>

                <!-- Teks Lengkap Pertanyaan -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Teks Kalimat Pertanyaan <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        v-model="form.question_text"
                        required
                        rows="3"
                        placeholder="Ketikkan rumusan pertanyaan kuesioner..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm bg-gray-50/50"
                    ></textarea>
                    <p v-if="form.errors.question_text" class="text-xs text-red-500 mt-1 font-semibold">
                        {{ form.errors.question_text }}
                    </p>
                </div>

                <!-- Footer Tombol Aksi -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="handleClose"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 border border-gray-300 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-[#005B3C] hover:bg-emerald-800 shadow-md shadow-emerald-900/20 disabled:opacity-50 transition-all cursor-pointer flex items-center gap-2"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>{{ isEdit ? 'Simpan Perubahan' : 'Tambah Pertanyaan' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
