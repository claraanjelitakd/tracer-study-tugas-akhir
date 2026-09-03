<script setup>
defineProps({
    form: Object,
});

// Common input class for Gen Z style
const inputClass = "block w-full border-gray-200 bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm focus:border-[#005B3C] focus:ring focus:ring-[#005B3C]/20 px-4 py-3.5 text-gray-800 font-medium transition-all duration-300 hover:border-gray-300";
const labelClass = "block text-sm font-bold text-gray-700 mb-2 ml-1";
</script>

<template>
    <div class="space-y-8 animate-fade-in-up">
        
        <!-- Bagian: Data Akademik Dasar (Hijau) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>
            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                </div>
                Informasi Akademik
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div>
                    <label :class="labelClass">Angkatan / Tahun Masuk</label>
                    <input type="text" v-model="form.tahun_masuk" :class="inputClass" placeholder="Tahun Masuk (Cth: 2021)" />
                </div>
                <div>
                    <label :class="labelClass">Tahun Lulus</label>
                    <input type="text" v-model="form.tahun_lulus" :class="inputClass" placeholder="Tahun Lulus (Cth: 2025)" />
                </div>
                <div>
                    <label :class="labelClass">Status Mahasiswa Saat Ini</label>
                    <select v-model="form.status_mahasiswa" :class="inputClass">
                        <option value="Aktif">Aktif</option>
                        <option value="Lulus">Lulus (Alumni)</option>
                        <option value="Cuti">Cuti</option>
                        <option value="Keluar">Keluar</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass">Total SKS Tempuh</label>
                    <input type="number" v-model="form.total_sks" :class="inputClass" placeholder="Total SKS (Cth: 144)" />
                </div>
                <div>
                    <label :class="labelClass">Indeks Prestasi Kumulatif (IPK)</label>
                    <input type="number" step="0.01" v-model="form.ipk" :class="inputClass" placeholder="Contoh: 3.85" />
                </div>
                <div>
                    <label :class="labelClass">Total Angka Kualitas</label>
                    <input type="number" step="0.01" v-model="form.total_angka_kualitas" :class="inputClass" placeholder="Contoh: 540.5" />
                </div>
            </div>
        </div>

        <!-- Bagian: Skripsi & Publikasi (Putih) -->
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                Skripsi & Publikasi
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label :class="labelClass">Judul Tugas Akhir / Skripsi</label>
                    <textarea v-model="form.judul_ta" rows="2" :class="inputClass" placeholder="Masukkan judul TA Bahasa Indonesia..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label :class="labelClass">Judul Tugas Akhir (Bahasa Inggris)</label>
                    <textarea v-model="form.judul_ta_inggris" rows="2" :class="inputClass" placeholder="Enter English Title..."></textarea>
                </div>
                
                <div>
                    <label :class="labelClass">URL Publikasi Ilmiah</label>
                    <input type="url" v-model="form.url_publikasi" :class="inputClass" placeholder="https://..." />
                </div>
                <div>
                    <label :class="labelClass">Jenis & Status Publikasi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select v-model="form.jenis_publikasi" :class="inputClass">
                            <option value="">-- Jenis --</option>
                            <option value="Jurnal">Jurnal</option>
                            <option value="Prosiding">Prosiding</option>
                            <option value="Buku">Buku</option>
                        </select>
                        <select v-model="form.status_publikasi" :class="inputClass">
                            <option value="">-- Status --</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Published">Published</option>
                            <option value="Under Review">Under Review</option>
                        </select>
                    </div>
                </div>
                
                <div class="bg-gray-50/50 p-6 rounded-[1.5rem] border border-gray-100 md:col-span-2">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Dosen Pembimbing</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label :class="labelClass">Pembimbing 1</label>
                            <input type="text" v-model="form.dospem_1" :class="inputClass" placeholder="Nama Dospem 1" />
                        </div>
                        <div>
                            <label :class="labelClass">Pembimbing 2 (Bila ada)</label>
                            <input type="text" v-model="form.dospem_2" :class="inputClass" placeholder="Nama Dospem 2" />
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50/50 p-6 rounded-[1.5rem] border border-gray-100 md:col-span-2">
                    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Dosen Penguji</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label :class="labelClass">Penguji 1</label>
                            <input type="text" v-model="form.penguji_1" :class="inputClass" placeholder="Nama Penguji 1" />
                        </div>
                        <div>
                            <label :class="labelClass">Penguji 2</label>
                            <input type="text" v-model="form.penguji_2" :class="inputClass" placeholder="Nama Penguji 2" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian: Data Yudisium & Kelulusan (Hijau) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>
            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                Data Yudisium & Kelulusan
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div>
                    <label :class="labelClass">Status Kelulusan / Yudisium</label>
                    <select v-model="form.keterangan_yudisium" :class="inputClass">
                        <option value="">-- Pilih Keterangan --</option>
                        <option value="Lulus">Lulus</option>
                        <option value="Cumlaude">Cumlaude</option>
                        <option value="Belum Lulus">Belum Lulus / Belum Yudisium</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass">Proses Yudisium</label>
                    <input type="text" v-model="form.proses_yudisium" :class="inputClass" placeholder="Periode Yudisium (Cth: Gasal 2026/2027)" />
                </div>
            </div>
        </div>
        
    </div>
</template>
