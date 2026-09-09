<!--
  Komponen Modal Tambah / Edit Opsi Jawaban (OptionModal.vue)
  
  Fungsi:
  Modal dialog untuk menambah pilihan opsi baru atau mengedit opsi pada butir pertanyaan kuesioner.
  Mendukung pengaturan teks jawaban, kode opsi, dan logika percabangan (jump_to ke pertanyaan lain).
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
    option: {
        type: Object,
        default: null,
    },
    availableJumpTargets: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close', 'saved']);

// Formulir reaktif Inertia untuk opsi
const form = useForm({
    id: null,
    code: '',
    option_text: '',
    jump_to: '',
});

// Pantau pembukaan modal dan inisialisasi data
watch(() => props.show, (isOpen) => {
    if (isOpen) {
        form.clearErrors();
        if (props.isEdit && props.option) {
            form.id = props.option.id;
            form.code = props.option.code || '';
            form.option_text = props.option.option_text;
            form.jump_to = props.option.jump_to || '';
        } else {
            form.reset();
            form.id = null;
            form.code = '';
            form.option_text = '';
            form.jump_to = '';
        }
    }
});

const handleClose = () => {
    emit('close');
};

const handleSubmit = () => {
    if (props.isEdit) {
        form.put(`/superadmin/pertanyaan/options/${form.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Pilihan opsi jawaban berhasil diperbarui.',
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
                    html: '<p class="text-sm">Periksa kembali data opsi:</p>' + errorList,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Perbaiki',
                });
            },
        });
    } else {
        form.post(`/superadmin/pertanyaan/${props.question.id}/options`, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Pilihan opsi jawaban berhasil ditambahkan.',
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
                    html: '<p class="text-sm">Ada kesalahan pada input:</p>' + errorList,
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

        <!-- Modal Card -->
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-gray-100 overflow-hidden z-10 transform transition-all">
            
            <!-- Header Modal -->
            <div class="p-5 bg-gradient-to-r from-emerald-800 to-[#005B3C] text-white flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black tracking-tight">
                        {{ isEdit ? 'Edit Pilihan Opsi' : 'Tambah Opsi Jawaban' }}
                    </h3>
                    <p class="text-xs text-emerald-100/90 font-medium mt-0.5">
                        Pertanyaan: <strong class="text-yellow-300 font-mono">{{ question?.code }}</strong>
                    </p>
                </div>
                <button 
                    type="button" 
                    @click="handleClose"
                    class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer"
                >
                    ✕
                </button>
            </div>

            <!-- Form Body -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                <!-- Teks Opsi Jawaban -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Teks Pilihan Jawaban <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        v-model="form.option_text"
                        required
                        placeholder="Contoh: Ya, Tidak, Sangat Sesuai, dsb."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm bg-gray-50/50"
                    />
                    <p v-if="form.errors.option_text" class="text-xs text-red-500 mt-1 font-semibold">
                        {{ form.errors.option_text }}
                    </p>
                </div>

                <!-- Kode Opsi Jawaban (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Kode Opsi (Opsional)
                    </label>
                    <input
                        type="text"
                        v-model="form.code"
                        placeholder="Kosongkan untuk otomatis (misal: F3-01)"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm font-mono bg-gray-50/50"
                    />
                    <p class="text-[11px] text-gray-400 mt-1">
                        * Jika dikosongkan, sistem otomatis memberikan kode urutan sesuai kode pertanyaan.
                    </p>
                </div>

                <!-- Logika Lompatan Alur (Jump Logic / Branching) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Alur Lompatan / Percabangan (Jump Logic)
                    </label>
                    <select
                        v-model="form.jump_to"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#005B3C] focus:border-transparent text-sm bg-gray-50/50"
                    >
                        <option value="">Lanjut ke pertanyaan berikutnya (Default)</option>
                        <option 
                            v-for="target in availableJumpTargets" 
                            :key="target.code" 
                            :value="target.code"
                        >
                            {{ target.label }}
                        </option>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">
                        * Jika alumni memilih opsi ini, kuesioner akan melompat langsung ke pertanyaan target yang dipilih.
                    </p>
                </div>

                <!-- Footer Tombol Aksi -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button
                        type="button"
                        @click="handleClose"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 border border-gray-300 transition-colors cursor-pointer"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#005B3C] hover:bg-emerald-800 shadow-md shadow-emerald-900/20 disabled:opacity-50 transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>{{ isEdit ? 'Simpan Opsi' : 'Tambah Opsi' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
