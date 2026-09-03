<script setup>
defineProps({
    form: Object,
    provinces: Array,
    kabupatens: Array
});

// Common input class for Gen Z style
const inputClass = "block w-full border-gray-200 bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm focus:border-[#005B3C] focus:ring focus:ring-[#005B3C]/20 px-4 py-3.5 text-gray-800 font-medium transition-all duration-300 hover:border-gray-300";
const labelClass = "block text-sm font-bold text-gray-700 mb-2 ml-1";
</script>

<template>
    <div class="space-y-8 animate-fade-in-up">
        
        <!-- Bagian: Identitas Diri (Hijau) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <!-- Dekorasi latar belakang -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>
            
            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                Identitas Diri
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div>
                    <label :class="labelClass">Nama Lengkap (Sesuai Ijazah)</label>
                    <input type="text" v-model="form.nama" :class="inputClass" placeholder="Nama Lengkap" />
                </div>
                <div>
                    <label :class="labelClass">Nomor Induk Kependudukan (NIK)</label>
                    <input type="text" v-model="form.nik" :class="[inputClass, 'bg-yellow-50/80']" placeholder="16 Digit NIK" />
                </div>
                
                <div>
                    <label :class="labelClass">Tempat Lahir</label>
                    <input type="text" v-model="form.tempat_lahir" :class="inputClass" placeholder="Kota Kelahiran" />
                </div>
                <div>
                    <label :class="labelClass">Tanggal Lahir</label>
                    <input type="date" v-model="form.tanggal_lahir" :class="inputClass" />
                </div>
                
                <div>
                    <label :class="labelClass">Jenis Kelamin</label>
                    <select v-model="form.jenis_kelamin" :class="inputClass">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass">Agama</label>
                    <select v-model="form.agama" :class="inputClass">
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
                    <label :class="labelClass">Golongan Darah</label>
                    <select v-model="form.golongan_darah" :class="inputClass">
                        <option value="">-- Pilih --</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass">Warga Negara</label>
                    <input type="text" v-model="form.warga_negara" :class="inputClass" placeholder="WNI / WNA" />
                </div>
            </div>
        </div>

        <!-- Bagian: Dokumen Kenegaraan (Putih) -->
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                Dokumen Pendukung
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label :class="labelClass">Nomor Kartu Keluarga (KK)</label>
                    <input type="text" v-model="form.no_kk" :class="inputClass" placeholder="Nomor KK" />
                </div>
                <div>
                    <label :class="labelClass">NISN</label>
                    <input type="text" v-model="form.nisn" :class="inputClass" placeholder="NISN" />
                </div>
                <div>
                    <label :class="labelClass">Nomor BPJS</label>
                    <input type="text" v-model="form.no_bpjs" :class="inputClass" placeholder="No BPJS (Bila ada)" />
                </div>
            </div>
        </div>

        <!-- Bagian: Kontak & Alamat (Hijau) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>

            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                Kontak & Alamat Domisili
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                
                <div>
                    <label :class="labelClass">Nomor Telepon / WhatsApp</label>
                    <input type="text" v-model="form.nomor_telepon" :class="inputClass" placeholder="081234567890" />
                </div>
                <div>
                    <label :class="labelClass">Email Pribadi Utama</label>
                    <input type="email" v-model="form.email_pribadi" :class="inputClass" placeholder="email@contoh.com" />
                </div>
                
                <div class="md:col-span-2">
                    <label :class="labelClass">Email Students (Opsional)</label>
                    <input type="email" v-model="form.email_students" :class="[inputClass, 'bg-gray-50/50']" placeholder="nim@students.ukdw.ac.id" />
                </div>

                <div class="md:col-span-2">
                    <label :class="labelClass">Alamat Domisili Saat Ini</label>
                    <textarea v-model="form.alamat_saat_ini" rows="3" :class="inputClass" placeholder="Nama Jalan, RT/RW, dsb..."></textarea>
                </div>

                <div>
                    <label :class="labelClass">Provinsi</label>
                    <select v-model="form.provinsi_id" :class="inputClass">
                        <option value="">-- Pilih Provinsi --</option>
                        <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.nama_provinsi }}</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass">Kabupaten/Kota</label>
                    <select v-model="form.kabupaten_id" :class="inputClass">
                        <option value="">-- Pilih Kabupaten --</option>
                        <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id" v-show="!form.provinsi_id || kab.province_id == form.provinsi_id">
                            {{ kab.nama_kabupaten }}
                        </option>
                    </select>
                </div>

                <div>
                    <label :class="labelClass">Kecamatan</label>
                    <input type="text" v-model="form.kecamatan" :class="inputClass" placeholder="Kecamatan" />
                </div>
                <div>
                    <label :class="labelClass">Kelurahan / Desa</label>
                    <input type="text" v-model="form.kelurahan" :class="inputClass" placeholder="Kelurahan/Desa" />
                </div>
                <div class="md:col-span-2">
                    <label :class="labelClass">Kode Pos</label>
                    <input type="text" v-model="form.kode_pos" :class="[inputClass, 'md:w-1/2']" placeholder="Kode Pos" />
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
