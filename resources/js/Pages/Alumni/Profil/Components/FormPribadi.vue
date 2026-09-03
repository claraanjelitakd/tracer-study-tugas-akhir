<!--
  Komponen: FormPribadi
  Fungsi: Mengisi data diri pribadi (Identitas & Alamat)
-->
<script setup>
defineProps({
    form: Object,
    provinces: Array,
    kabupatens: Array
});
</script>

<template>
    <div class="space-y-8 animate-fade-in-up">
        
        <!-- Bagian: Identitas Diri (Prioritas) -->
        <div>
            <h2 class="text-xl font-bold text-[#005B3C] mb-4 border-b pb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Identitas Diri
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-green-50/50 p-5 rounded-2xl border border-green-100 shadow-inner">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap (Sesuai Ijazah)</label>
                    <input type="text" v-model="form.nama" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium" placeholder="Nama Lengkap" />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" v-model="form.nik" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium bg-yellow-50" placeholder="16 Digit NIK" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" v-model="form.tempat_lahir" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Kota Kelahiran" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" v-model="form.tanggal_lahir" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select v-model="form.jenis_kelamin" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                    <select v-model="form.agama" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih --</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen Protestan">Kristen Protestan</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                    <select v-model="form.golongan_darah" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warga Negara</label>
                    <input type="text" v-model="form.warga_negara" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="WNI / WNA" />
                </div>
            </div>
        </div>

        <!-- Bagian: Dokumen Kenegaraan (Tidak di-highlight) -->
        <div>
            <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2">Dokumen Pendukung</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu Keluarga (KK)</label>
                    <input type="text" v-model="form.no_kk" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                    <input type="text" v-model="form.nisn" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor BPJS</label>
                    <input type="text" v-model="form.no_bpjs" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 text-sm" />
                </div>
            </div>
        </div>

        <!-- Bagian: Kontak & Alamat (Prioritas) -->
        <div>
            <h2 class="text-xl font-bold text-[#005B3C] mb-4 border-b pb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Kontak & Alamat Domisili
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-green-50/50 p-5 rounded-2xl border border-green-100 shadow-inner">
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" v-model="form.nomor_telepon" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium" placeholder="081234567890" />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email Pribadi Utama</label>
                    <input type="email" v-model="form.email_pribadi" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium" placeholder="email@contoh.com" />
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Students (Opsional)</label>
                    <input type="email" v-model="form.email_students" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 bg-gray-50 text-sm" placeholder="nim@students.ukdw.ac.id" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Domisili Saat Ini</label>
                    <textarea v-model="form.alamat_saat_ini" rows="2" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Nama Jalan, RT/RW, dsb..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <select v-model="form.provinsi_id" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih Provinsi --</option>
                        <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.nama_provinsi }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                    <select v-model="form.kabupaten_id" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih Kabupaten --</option>
                        <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id" v-show="!form.provinsi_id || kab.province_id == form.provinsi_id">
                            {{ kab.nama_kabupaten }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" v-model="form.kecamatan" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan / Desa</label>
                    <input type="text" v-model="form.kelurahan" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" v-model="form.kode_pos" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" />
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
