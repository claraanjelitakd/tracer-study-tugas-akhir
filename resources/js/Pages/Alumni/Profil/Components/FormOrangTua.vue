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
        
        <!-- Bagian: Identitas Orang Tua (Hijau) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>
            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                Identitas Orang Tua / Wali
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div>
                    <label :class="labelClass">Nama Ayah</label>
                    <input type="text" v-model="form.nama_ayah" :class="inputClass" placeholder="Nama Lengkap Ayah" />
                </div>
                <div>
                    <label :class="labelClass">Pekerjaan Ayah</label>
                    <input type="text" v-model="form.pekerjaan_ayah" :class="inputClass" placeholder="Contoh: Wiraswasta" />
                </div>
                
                <div>
                    <label :class="labelClass">Nama Ibu</label>
                    <input type="text" v-model="form.nama_ibu" :class="inputClass" placeholder="Nama Lengkap Ibu" />
                </div>
                <div>
                    <label :class="labelClass">Pekerjaan Ibu</label>
                    <input type="text" v-model="form.pekerjaan_ibu" :class="inputClass" placeholder="Contoh: Ibu Rumah Tangga" />
                </div>
            </div>
        </div>

        <!-- Bagian: Kontak Darurat Orang Tua (Putih) -->
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                Kontak Darurat Orang Tua
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label :class="labelClass">Nomor Telepon / HP Orang Tua</label>
                    <input type="text" v-model="form.telepon_ortu" :class="inputClass" placeholder="081..." />
                </div>
                <div>
                    <label :class="labelClass">Email Orang Tua</label>
                    <input type="email" v-model="form.email_ortu" :class="inputClass" placeholder="email@contoh.com" />
                </div>
            </div>
        </div>

        <!-- Bagian: Alamat Orang Tua (Hijau) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>
            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                Alamat Orang Tua / Wali
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div class="md:col-span-2">
                    <label :class="labelClass">Alamat Lengkap</label>
                    <textarea v-model="form.alamat_ortu" rows="3" :class="inputClass" placeholder="Nama Jalan, RT/RW..."></textarea>
                </div>

                <div>
                    <label :class="labelClass">Provinsi</label>
                    <select v-model="form.ortu_provinsi_id" :class="inputClass">
                        <option value="">-- Pilih Provinsi --</option>
                        <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.nama_provinsi }}</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass">Kabupaten/Kota</label>
                    <select v-model="form.ortu_kabupaten_id" :class="inputClass">
                        <option value="">-- Pilih Kabupaten --</option>
                        <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id" v-show="!form.ortu_provinsi_id || kab.province_id == form.ortu_provinsi_id">
                            {{ kab.nama_kabupaten }}
                        </option>
                    </select>
                </div>

                <div>
                    <label :class="labelClass">Kecamatan</label>
                    <input type="text" v-model="form.ortu_kecamatan" :class="inputClass" placeholder="Kecamatan" />
                </div>
                <div>
                    <label :class="labelClass">Kode Pos</label>
                    <input type="text" v-model="form.ortu_kode_pos" :class="inputClass" placeholder="Kode Pos" />
                </div>
            </div>
        </div>
        
    </div>
</template>
