<!--
  Komponen: FormAkademik
  Fungsi: Mengisi data akademik dasar dan Yudisium (Skripsi & Kelulusan)
-->
<script setup>
defineProps({
    form: Object
});
</script>

<template>
    <div class="space-y-8 animate-fade-in-up">
        
        <!-- Bagian: Status Akademik (Prioritas) -->
        <div>
            <h2 class="text-xl font-bold text-[#005B3C] mb-4 border-b pb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                Status Mahasiswa & Nilai
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-green-50/50 p-5 rounded-2xl border border-green-100 shadow-inner">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Status Mahasiswa Saat Ini</label>
                    <select v-model="form.status_mahasiswa" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium bg-white">
                        <option value="Lulus">Lulus</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tahun Lulus / Yudisium</label>
                    <input type="number" v-model="form.tahun_lulus" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium" placeholder="Contoh: 2024" />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">IPK Terakhir</label>
                    <input type="number" step="0.01" v-model="form.ipk" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium text-green-700" placeholder="Contoh: 3.85" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Angkatan Masuk</label>
                    <input type="number" v-model="form.angkatan_masuk" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Contoh: 2020" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik Kelulusan</label>
                    <input type="text" v-model="form.tahun_akademik_lulus" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Contoh: Gasal 2023/2024" />
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total SKS Tempuh</label>
                    <input type="number" v-model="form.total_sks" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 bg-gray-50 text-sm" placeholder="Contoh: 144" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Angka Kualitas</label>
                    <input type="number" step="0.01" v-model="form.total_angka_kualitas" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 bg-gray-50 text-sm" placeholder="Contoh: 540.5" />
                </div>
            </div>
        </div>

        <!-- Bagian: Skripsi & Publikasi -->
        <div>
            <h2 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2 flex items-center">
                Skripsi & Publikasi (Yudisium)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas Akhir / Skripsi</label>
                    <textarea v-model="form.judul_ta" rows="2" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="Masukkan judul TA Bahasa Indonesia..."></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas Akhir (Bahasa Inggris)</label>
                    <textarea v-model="form.judul_ta_inggris" rows="2" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors italic" placeholder="Enter English Title..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Publikasi Ilmiah</label>
                    <input type="url" v-model="form.url_publikasi" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="https://..." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis & Status Publikasi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" v-model="form.jenis_publikasi" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Jurnal / Prosiding" />
                        <select v-model="form.status_publikasi" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            <option value="">-Status-</option>
                            <option value="Published">Published</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Under Review">Under Review</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian: Dosen Pembimbing & Penguji (Tidak Highlight) -->
        <div>
            <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Dosen Pembimbing & Penguji</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-5 rounded-2xl border border-gray-200">
                <div class="space-y-4">
                    <h3 class="font-semibold text-sm text-gray-600">Dosen Pembimbing</h3>
                    <div>
                        <input type="text" v-model="form.dosen_pembimbing_1" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Pembimbing Utama" />
                    </div>
                    <div>
                        <input type="text" v-model="form.dosen_pembimbing_2" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Pembimbing 2 (Opsional)" />
                    </div>
                    <div>
                        <input type="text" v-model="form.dosen_pembimbing_3" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Pembimbing 3 (Opsional)" />
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="font-semibold text-sm text-gray-600">Dosen Penguji</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" v-model="form.dosen_penguji_1" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Penguji 1" />
                        <input type="text" v-model="form.dosen_penguji_2" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Penguji 2" />
                        <input type="text" v-model="form.dosen_penguji_3" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Penguji 3 (Opsional)" />
                        <input type="text" v-model="form.dosen_penguji_4" class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" placeholder="Penguji 4 (Opsional)" />
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.5s ease-out forwards;
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
