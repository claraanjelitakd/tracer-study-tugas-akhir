<!--
  Komponen: FormKarier
  Fungsi: Mengisi data Karier, Perusahaan, Keahlian, dan Jejaring Media Sosial
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
        
        <!-- Bagian: Pekerjaan & Perusahaan (Prioritas) -->
        <div>
            <h2 class="text-xl font-bold text-[#005B3C] mb-4 border-b pb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                Pekerjaan & Keahlian
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-green-50/50 p-5 rounded-2xl border border-green-100 shadow-inner">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Perusahaan Tempat Bekerja (Bila Ada)</label>
                    <input type="text" v-model="form.nama_perusahaan" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 font-medium" placeholder="PT. Contoh Nama Perusahaan" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keahlian (Expertise)</label>
                    <input type="text" v-model="form.expert" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="Frontend Developer, Akuntan, dll." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minat / Ketertarikan (Interest)</label>
                    <input type="text" v-model="form.minat" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="AI, Bisnis, Desain, dll." />
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 border-t border-green-200 mt-2 pt-4">Lokasi Perusahaan (Domisili Kerja)</h3>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi Lokasi Kerja</label>
                    <select v-model="form.company_province_id" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih Provinsi --</option>
                        <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.nama_provinsi }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota Lokasi Kerja</label>
                    <select v-model="form.company_kabupaten_id" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">-- Pilih Kabupaten --</option>
                        <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id" v-show="!form.company_province_id || kab.province_id == form.company_province_id">
                            {{ kab.nama_kabupaten }}
                        </option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos Perusahaan</label>
                    <input type="text" v-model="form.zipcode" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 md:w-1/2" />
                </div>
            </div>
        </div>

        <!-- Bagian: Jejaring Sosial -->
        <div>
            <h2 class="text-lg font-bold text-gray-700 mb-4 border-b pb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                Media Sosial & Jejaring (Network)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-5 rounded-2xl border border-gray-200">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn Username</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">in/</span>
                        </div>
                        <input type="text" v-model="form.linkedin_username" class="focus:ring-green-500 focus:border-green-500 block w-full pl-8 sm:text-sm border-gray-300 rounded-xl" placeholder="johndoe" />
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL Full</label>
                    <input type="url" v-model="form.linkedin_url" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="https://linkedin.com/in/..." />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                    <input type="url" v-model="form.instagram_url" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="https://instagram.com/..." />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                    <input type="url" v-model="form.facebook_url" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500" placeholder="https://facebook.com/..." />
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
