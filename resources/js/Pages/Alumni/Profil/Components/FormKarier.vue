<script setup>
import { ref } from 'vue';

const props = defineProps({
    form: Object,
    provinces: Array,
    kabupatens: Array
});

// Common input class for Gen Z style
const inputClass = "block w-full border-gray-200 bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm focus:border-[#005B3C] focus:ring focus:ring-[#005B3C]/20 px-4 py-3.5 text-gray-800 font-medium transition-all duration-300 hover:border-gray-300";
const labelClass = "block text-sm font-bold text-gray-700 mb-2 ml-1";

// ============================================================================
// LOGIKA AUTOCOMPLETE PERUSAHAAN
// ============================================================================
const searchResults = ref([]);      // Menyimpan hasil pencarian dari API
const showDropdown = ref(false);    // Mengontrol visibilitas UI dropdown list
const isSearching = ref(false);     // State untuk menampilkan spinner loading
const companyStatus = ref(props.form.company_status_verifikasi || null); // State lencana verifikasi (Terverifikasi/Menunggu Verifikasi/Ditolak)

let searchTimeout;                  // Variabel untuk debouncing (mencegah spam API)

const onSearchCompany = () => {
    clearTimeout(searchTimeout);
    companyStatus.value = null; // Reset status when typing
    
    if (!props.form.nama_perusahaan || props.form.nama_perusahaan.length < 2) {
        showDropdown.value = false;
        searchResults.value = [];
        return;
    }
    
    searchTimeout = setTimeout(async () => {
        isSearching.value = true;
        try {
            let url = `/api/companies?q=${encodeURIComponent(props.form.nama_perusahaan)}`;
            if (props.form.company_province_id) url += `&province_id=${props.form.company_province_id}`;
            if (props.form.company_kabupaten_id) url += `&kabupaten_id=${props.form.company_kabupaten_id}`;
            
            const response = await fetch(url);
            const data = await response.json();
            searchResults.value = data;
            showDropdown.value = data.length > 0;
        } catch (error) {
            console.error("Error fetching companies:", error);
        } finally {
            isSearching.value = false;
        }
    }, 400);
};

const selectCompany = (company) => {
    props.form.nama_perusahaan = company.nama_perusahaan;
    if (company.province_id) props.form.company_province_id = company.province_id;
    if (company.kabupaten_id) props.form.company_kabupaten_id = company.kabupaten_id;
    
    companyStatus.value = company.status_verifikasi;
    showDropdown.value = false;
};
</script>

<template>
    <div class="space-y-8 animate-fade-in-up">
        
        <!-- Bagian: Sosial Media (Hijau) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>
            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </div>
                Sosial Media & Profesional
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                <div>
                    <label :class="labelClass">LinkedIn Profil URL</label>
                    <input type="url" v-model="form.linkedin_url" :class="inputClass" placeholder="https://linkedin.com/in/..." />
                </div>
                <div>
                    <label :class="labelClass">LinkedIn Username</label>
                    <input type="text" v-model="form.linkedin_username" :class="inputClass" placeholder="username_linkedin" />
                </div>

                <div>
                    <label :class="labelClass">Instagram Profil URL</label>
                    <input type="url" v-model="form.instagram_url" :class="inputClass" placeholder="https://instagram.com/..." />
                </div>
                <div>
                    <label :class="labelClass">Facebook Profil URL</label>
                    <input type="url" v-model="form.facebook_url" :class="inputClass" placeholder="https://facebook.com/..." />
                </div>
                
                <div class="md:col-span-2">
                    <label :class="labelClass">Bidang Keahlian Spesifik (Expertise)</label>
                    <input type="text" v-model="form.expert" :class="inputClass" placeholder="Contoh: Software Engineering, Digital Marketing..." />
                </div>
                <div class="md:col-span-2">
                    <label :class="labelClass">Minat & Ketertarikan (Interest)</label>
                    <input type="text" v-model="form.minat" :class="inputClass" placeholder="Contoh: UI/UX Design, Open Source, Musik..." />
                </div>
            </div>
        </div>

        <!-- Bagian: Data Perusahaan Saat Ini (Putih) -->
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                Data Perusahaan Saat Ini
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 relative">
                    <label :class="labelClass">Nama Perusahaan / Tempat Bekerja</label>
                    <div class="relative">
                        <input type="text" v-model="form.nama_perusahaan" @input="onSearchCompany" :class="inputClass" placeholder="Ketik nama perusahaan..." autocomplete="off" />
                        
                        <!-- Loading Indicator -->
                        <div v-if="isSearching" class="absolute right-4 top-1/2 -translate-y-1/2">
                            <svg class="animate-spin h-5 w-5 text-[#005B3C]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                    
                    <!-- Autocomplete Dropdown -->
                    <div v-if="showDropdown && searchResults.length > 0" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-xl border border-gray-100 max-h-60 overflow-y-auto overflow-hidden">
                        <ul class="py-2">
                            <li v-for="company in searchResults" :key="company.id" @click="selectCompany(company)" class="px-6 py-3 hover:bg-green-50 cursor-pointer flex items-center justify-between border-b border-gray-50 last:border-0 transition-colors">
                                <div>
                                    <span class="font-bold text-gray-800 block">{{ company.nama_perusahaan }}</span>
                                    <span class="text-xs text-gray-500" v-if="company.province_id">
                                        {{ provinces.find(p => p.id == company.province_id)?.nama_provinsi || 'Provinsi' }} 
                                        {{ company.kabupaten_id ? ' - ' + (kabupatens.find(k => k.id == company.kabupaten_id)?.nama_kabupaten || '') : '' }}
                                    </span>
                                </div>
                                <span v-if="company.status_verifikasi === 'Terverifikasi'" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">✔ Terverifikasi</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Status Verification Badge -->
                    <div class="mt-3" v-if="companyStatus || form.nama_perusahaan">
                        <span v-if="companyStatus === 'Terverifikasi'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="mr-1.5 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            Perusahaan Terverifikasi oleh Kampus
                        </span>
                        <span v-else-if="companyStatus === 'Ditolak'" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="mr-1.5 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            Data Ditolak
                        </span>
                        <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            <svg class="mr-1.5 h-4 w-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Perusahaan Baru / Menunggu Verifikasi
                        </span>
                    </div>
                </div>
                
                <div>
                    <label :class="labelClass">Provinsi Perusahaan</label>
                    <select v-model="form.company_province_id" :class="inputClass" @change="onSearchCompany">
                        <option value="">-- Pilih Provinsi --</option>
                        <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.nama_provinsi }}</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass">Kabupaten/Kota Perusahaan</label>
                    <select v-model="form.company_kabupaten_id" :class="inputClass">
                        <option value="">-- Pilih Kabupaten --</option>
                        <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id" v-show="!form.company_province_id || kab.province_id == form.company_province_id">
                            {{ kab.nama_kabupaten }}
                        </option>
                    </select>
                </div>

                <div>
                    <label :class="labelClass">Kode Pos Perusahaan (Zipcode)</label>
                    <input type="text" v-model="form.zipcode" :class="inputClass" placeholder="Kode Pos" />
                </div>
            </div>
        </div>

        <!-- Bagian: Data Atasan (Hijau - Khusus Atasan) -->
        <div class="bg-[#f0fdf4] rounded-[2rem] p-8 shadow-sm border border-green-100/50 relative overflow-hidden">
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-green-200/40 rounded-full blur-3xl"></div>
            <h2 class="text-2xl font-black text-[#005B3C] mb-6 flex items-center relative z-10">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 text-[#005B3C]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                Data Atasan (Pimpinan Instansi)
            </h2>
            <div class="relative z-10">
                <div class="bg-white/60 p-6 rounded-2xl shadow-sm text-sm text-gray-700 leading-relaxed mb-8 border border-white backdrop-blur-md">
                    <strong class="text-[#005B3C] text-base">Pimpinan tempat anda bekerja dapat mengisi kuesioner evaluasi tingkat kepuasan dan kinerja lulusan.</strong><br>
                    Supaya pimpinan anda dapat login ke dalam Sistem Tracer Studi UKDW, anda harus mengirimkan undangan melalui email.<br>
                    Untuk mengirim undangan melalui email klik tombol <em class="font-bold text-gray-900">"KIRIM FORM EVALUASI PENGGUNA"</em> dibawah.
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label :class="labelClass">Nama Atasan di Perusahaan/Instansi/Institusi tempat anda bekerja</label>
                        <input type="text" v-model="form.nama_atasan" :class="inputClass" placeholder="Nama Lengkap Atasan" />
                    </div>
                    
                    <div>
                        <label :class="labelClass">Email Atasan</label>
                        <input type="email" v-model="form.email_atasan" :class="inputClass" placeholder="email@perusahaan.com" />
                    </div>

                    <div>
                        <label :class="labelClass">Nomor Telepon Atasan</label>
                        <input type="text" v-model="form.telepon_atasan" :class="inputClass" placeholder="081..." />
                    </div>
                </div>

                <div class="mt-8">
                    <button type="button" class="w-full md:w-auto px-8 py-4 bg-gray-900 hover:bg-black text-white text-sm font-bold rounded-2xl shadow-xl hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 flex items-center justify-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>KIRIM FORM EVALUASI PENGGUNA</span>
                    </button>
                    <p class="text-sm font-medium text-gray-500 mt-3 ml-2">* Tombol ini akan mengirimkan email ke atasan Anda setelah Profil disimpan.</p>
                </div>
            </div>
        </div>
        
    </div>
</template>
