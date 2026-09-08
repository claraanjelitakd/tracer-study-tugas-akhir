<script setup>
defineProps({
    form: Object,
});

// Input styling: abu-abu bersih untuk data terkunci/paten
const lockedInputClass = "block w-full border border-gray-200 bg-gray-100 text-gray-700 rounded-xl px-4 py-3 text-sm font-medium cursor-not-allowed select-none";
const labelClass = "block text-sm font-semibold text-gray-700 mb-1.5";
</script>

<template>
    <div class="space-y-8">
        
        <!-- Bagian 1: Informasi Akademik -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">
                Informasi Akademik
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label :class="labelClass">Nomor Induk Mahasiswa (NIM)</label>
                    <input type="text" :value="form.nim" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Angkatan Masuk</label>
                    <input type="text" :value="form.angkatan_masuk" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Status Mahasiswa</label>
                    <input type="text" :value="form.status_mahasiswa || 'Lulus'" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Semester Kelulusan</label>
                    <input type="text" :value="form.tahun_akademik_lulus" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Tahun Lulus</label>
                    <input type="text" :value="form.tahun_lulus" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Indeks Prestasi Kumulatif (IPK)</label>
                    <input type="text" :value="form.ipk" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Total SKS Tempuh</label>
                    <input type="text" :value="form.total_sks" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Total Angka Kualitas</label>
                    <input type="text" :value="form.total_angka_kualitas" disabled :class="lockedInputClass" />
                </div>
            </div>
        </div>

        <!-- Bagian 2: Skripsi & Publikasi Ilmiah (Data Resmi Kampus) -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">
                Skripsi & Publikasi Ilmiah
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label :class="labelClass">Judul Tugas Akhir / Skripsi (Bahasa Indonesia)</label>
                    <textarea :value="form.judul_ta" rows="2" disabled :class="lockedInputClass"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label :class="labelClass">Judul Tugas Akhir (Bahasa Inggris)</label>
                    <textarea :value="form.judul_ta_inggris" rows="2" disabled :class="lockedInputClass"></textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label :class="labelClass">URL Publikasi Ilmiah / Repositori</label>
                    <input type="text" :value="form.url_publikasi" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Jenis Publikasi</label>
                    <input type="text" :value="form.jenis_publikasi" disabled :class="lockedInputClass" />
                </div>

                <div>
                    <label :class="labelClass">Status Publikasi</label>
                    <input type="text" :value="form.status_publikasi" disabled :class="lockedInputClass" />
                </div>
                
                <!-- Dosen Pembimbing -->
                <div>
                    <label :class="labelClass">Dosen Pembimbing 1</label>
                    <input type="text" :value="form.dosen_pembimbing_1" disabled :class="lockedInputClass" />
                </div>
                <div>
                    <label :class="labelClass">Dosen Pembimbing 2</label>
                    <input type="text" :value="form.dosen_pembimbing_2" disabled :class="lockedInputClass" />
                </div>

                <!-- Dosen Penguji -->
                <div>
                    <label :class="labelClass">Dosen Penguji 1</label>
                    <input type="text" :value="form.dosen_penguji_1" disabled :class="lockedInputClass" />
                </div>
                <div>
                    <label :class="labelClass">Dosen Penguji 2</label>
                    <input type="text" :value="form.dosen_penguji_2" disabled :class="lockedInputClass" />
                </div>
            </div>
        </div>

        <!-- Bagian 3: Data Yudisium -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">
                Data Yudisium & Predikat Kelulusan
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label :class="labelClass">Keterangan Hasil Yudisium / Predikat</label>
                    <input type="text" :value="form.keterangan_hasil_yudisium" disabled :class="lockedInputClass" />
                </div>
                <div>
                    <label :class="labelClass">Status Yudisium</label>
                    <input type="text" :value="form.proses_yudisium" disabled :class="lockedInputClass" />
                </div>
            </div>
        </div>
        
    </div>
</template>
