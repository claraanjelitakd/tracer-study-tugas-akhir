<!--
  Komponen: FormOrangTua
  Fungsi: Mengisi data informasi orang tua (Tidak di-highlight / prioritas rendah)
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
        
        <div>
            <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Data Orang Tua / Wali
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-2xl border border-gray-200 bg-white">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua</label>
                    <input type="text" v-model="form.nama_orang_tua" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Nama Lengkap Orang Tua / Wali" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                    <input type="text" v-model="form.pekerjaan_orang_tua" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Wiraswasta, PNS, dsb." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" v-model="form.nomor_telepon_orang_tua" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="081..." />
                </div>
            </div>
            
            <h3 class="text-md font-semibold text-gray-600 mt-6 mb-3">Alamat Orang Tua</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-2xl border border-gray-200 bg-white">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea v-model="form.alamat_orang_tua" rows="2" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Nama Jalan, RT/RW..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <select v-model="form.provinsi_id_orang_tua" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih Provinsi --</option>
                        <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.nama_provinsi }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                    <select v-model="form.kabupaten_id_orang_tua" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih Kabupaten --</option>
                        <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id" v-show="!form.provinsi_id_orang_tua || kab.province_id == form.provinsi_id_orang_tua">
                            {{ kab.nama_kabupaten }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan/Kota (Detail)</label>
                    <input type="text" v-model="form.kota_orang_tua" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" v-model="form.kode_pos_orang_tua" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" />
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
