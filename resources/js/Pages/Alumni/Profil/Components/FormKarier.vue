<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    form: Object,
    provinces: Array,
    kabupatens: Array,
    companies: Array,
    alumniData: Object,
});

const inputClass = "block w-full border border-gray-200 bg-white rounded-xl shadow-xs focus:border-[#005B3C] focus:ring focus:ring-[#005B3C]/10 px-4 py-3 text-sm text-gray-800 font-medium transition-colors";
const labelClass = "block text-sm font-semibold text-gray-700 mb-1.5";

// Container refs untuk click-outside
const provinsiContainerRef = ref(null);
const kabupatenContainerRef = ref(null);
const companyContainerRef = ref(null);

// Salinan lokal daftar perusahaan agar instan ter-update saat tambah perusahaan baru
const localCompanies = ref([...(props.companies || [])]);
watch(() => props.companies, (newVal) => {
    if (newVal) localCompanies.value = [...newVal];
}, { deep: true });

// ============================================================================
// DROPDOWN PROVINSI (SEARCHABLE SEPERTI GAMBAR 2)
// ============================================================================
const showProvinsiDropdown = ref(false);
const searchProvinsiQuery = ref('');

const selectedProvinceName = computed(() => {
    if (!props.form.company_province_id || !props.provinces) return '';
    const prov = props.provinces.find(p => p.id == props.form.company_province_id);
    return prov ? prov.nama_provinsi : '';
});

const filteredProvinces = computed(() => {
    const list = props.provinces || [];
    if (!searchProvinsiQuery.value.trim()) return list;
    const q = searchProvinsiQuery.value.toLowerCase();
    return list.filter(p => p.nama_provinsi.toLowerCase().includes(q));
});

const selectProvinsi = (prov) => {
    props.form.company_province_id = prov.id;
    // Reset kabupaten saat provinsi berganti
    props.form.company_kabupaten_id = '';
    showProvinsiDropdown.value = false;
    searchProvinsiQuery.value = '';
};

// ============================================================================
// DROPDOWN KABUPATEN (SEARCHABLE SEPERTI GAMBAR 2)
// ============================================================================
const showKabupatenDropdown = ref(false);
const searchKabupatenQuery = ref('');

const selectedKabupatenName = computed(() => {
    if (!props.form.company_kabupaten_id || !props.kabupatens) return '';
    const kab = props.kabupatens.find(k => k.id == props.form.company_kabupaten_id);
    return kab ? kab.nama_kabupaten : '';
});

const filteredKabupatens = computed(() => {
    let list = props.kabupatens || [];
    if (props.form.company_province_id) {
        list = list.filter(k => k.province_id == props.form.company_province_id);
    }
    if (!searchKabupatenQuery.value.trim()) return list;
    const q = searchKabupatenQuery.value.toLowerCase();
    return list.filter(k => k.nama_kabupaten.toLowerCase().includes(q));
});

const selectKabupaten = (kab) => {
    props.form.company_kabupaten_id = kab.id;
    showKabupatenDropdown.value = false;
    searchKabupatenQuery.value = '';
};

// ============================================================================
// DROPDOWN PERUSAHAAN (SEARCHABLE SEPERTI GAMBAR 2)
// ============================================================================
const showCompanyDropdown = ref(false);
const searchCompanyQuery = ref('');

const filteredCompanies = computed(() => {
    let list = localCompanies.value || [];

    // Jika provinsi sudah dipilih, saring atau prioritaskan perusahaan di provinsi tersebut
    if (props.form.company_province_id) {
        const inProv = list.filter(c => c.province_id == props.form.company_province_id);
        if (inProv.length > 0) {
            list = inProv;
        }
    }
    // Jika kabupaten sudah dipilih
    if (props.form.company_kabupaten_id) {
        const inKab = list.filter(c => c.kabupaten_id == props.form.company_kabupaten_id);
        if (inKab.length > 0) {
            list = inKab;
        }
    }

    if (!searchCompanyQuery.value.trim()) return list;
    const q = searchCompanyQuery.value.toLowerCase();
    return list.filter(c => c.nama_perusahaan.toLowerCase().includes(q));
});

const selectCompany = (company) => {
    props.form.nama_perusahaan = company.nama_perusahaan;
    
    // Otomatis isi detail provinsi, kabupaten, alamat, skala jika ada di database
    if (company.province_id) props.form.company_province_id = company.province_id;
    if (company.kabupaten_id) props.form.company_kabupaten_id = company.kabupaten_id;
    if (company.alamat) props.form.company_alamat = company.alamat;
    if (company.skala) props.form.company_skala = company.skala;
    if (company.kode_pos) props.form.zipcode = company.kode_pos;
    
    showCompanyDropdown.value = false;
    searchCompanyQuery.value = '';
};

// ============================================================================
// MODAL POP-UP TAMBAH PERUSAHAAN BARU (MENGGUNAKAN SWEETALERT2)
// ============================================================================
const openAddCompanyModal = () => {
    showCompanyDropdown.value = false;

    // Persiapkan daftar option provinsi
    const provinceOptions = (props.provinces || [])
        .map(p => `<option value="${p.id}" ${p.id == props.form.company_province_id ? 'selected' : ''}>${p.nama_provinsi}</option>`)
        .join('');

    const initialProvId = props.form.company_province_id || '';
    const initialKabId = props.form.company_kabupaten_id || '';
    const initialKodePos = props.form.zipcode || '';

    const htmlContent = `
        <div style="text-align: left; font-size: 13px; color: #374151;" class="space-y-3.5 pt-1">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">
                    Nama Perusahaan / Instansi <span style="color: #ef4444;">*</span>
                </label>
                <input id="swal-company-name" type="text" style="width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 9px 12px; font-size: 14px; box-sizing: border-box; outline: none;" placeholder="Contoh: PT Teknologi Nusantara" />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">
                        Provinsi <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="swal-company-province" style="width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 9px 12px; font-size: 14px; box-sizing: border-box; outline: none; background: #fff;">
                        <option value="">-- Pilih Provinsi --</option>
                        ${provinceOptions}
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">
                        Kabupaten / Kota <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="swal-company-kabupaten" style="width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 9px 12px; font-size: 14px; box-sizing: border-box; outline: none; background: #fff;">
                        <option value="">-- Pilih Kabupaten --</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">
                        Kode Pos Perusahaan <span style="color: #ef4444;">*</span>
                    </label>
                    <input id="swal-company-kodepos" type="text" value="${initialKodePos}" style="width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 9px 12px; font-size: 14px; box-sizing: border-box; outline: none;" placeholder="Contoh: 55281" />
                </div>
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">
                        Skala Perusahaan
                    </label>
                    <select id="swal-company-skala" style="width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 9px 12px; font-size: 14px; box-sizing: border-box; outline: none; background: #fff;">
                        <option value="Lokal" selected>Lokal</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">
                    Alamat Jalan / Gedung Perusahaan
                </label>
                <textarea id="swal-company-alamat" rows="2" style="width: 100%; border: 1px solid #d1d5db; border-radius: 10px; padding: 9px 12px; font-size: 14px; box-sizing: border-box; outline: none; resize: vertical;" placeholder="Nama Jalan, Gedung, Nomor..."></textarea>
            </div>

            <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 12px; font-size: 12px; color: #92400e; display: flex; align-items: center; gap: 8px;">
                <span>ℹ️</span>
                <span>Status verifikasi awal: <strong>Menunggu Verifikasi</strong>.</span>
            </div>
        </div>
    `;

    Swal.fire({
        title: '<div style="font-size: 18px; font-weight: 700; color: #111827;">Tambah Perusahaan Baru</div>',
        html: htmlContent,
        showCancelButton: true,
        confirmButtonText: 'Simpan Perusahaan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#005B3C',
        cancelButtonColor: '#9CA3AF',
        focusConfirm: false,
        width: '32rem',
        customClass: {
            popup: 'rounded-2xl shadow-xl'
        },
        didOpen: () => {
            const provSelect = document.getElementById('swal-company-province');
            const kabSelect = document.getElementById('swal-company-kabupaten');

            const updateKabupatenOptions = (provId, selectedKabId = '') => {
                if (!provId) {
                    kabSelect.innerHTML = '<option value="">-- Pilih Kabupaten --</option>';
                    kabSelect.disabled = true;
                    kabSelect.style.backgroundColor = '#f3f4f6';
                    return;
                }
                const filtered = (props.kabupatens || []).filter(k => k.province_id == provId);
                let options = '<option value="">-- Pilih Kabupaten --</option>';
                filtered.forEach(k => {
                    const sel = k.id == selectedKabId ? 'selected' : '';
                    options += `<option value="${k.id}" ${sel}>${k.nama_kabupaten}</option>`;
                });
                kabSelect.innerHTML = options;
                kabSelect.disabled = false;
                kabSelect.style.backgroundColor = '#fff';
            };

            // Inisialisasi kabupaten jika sudah ada provinsi terpilih
            if (initialProvId) {
                updateKabupatenOptions(initialProvId, initialKabId);
            } else {
                kabSelect.disabled = true;
                kabSelect.style.backgroundColor = '#f3f4f6';
            }

            // Event listener change provinsi
            provSelect.addEventListener('change', (e) => {
                updateKabupatenOptions(e.target.value);
            });

            // Fokus ke nama perusahaan
            const nameInput = document.getElementById('swal-company-name');
            if (nameInput) nameInput.focus();
        },
        preConfirm: async () => {
            const nama = document.getElementById('swal-company-name')?.value?.trim();
            const provId = document.getElementById('swal-company-province')?.value;
            const kabId = document.getElementById('swal-company-kabupaten')?.value;
            const kodepos = document.getElementById('swal-company-kodepos')?.value?.trim();
            const skala = document.getElementById('swal-company-skala')?.value || 'Lokal';
            const alamat = document.getElementById('swal-company-alamat')?.value?.trim() || '';

            if (!nama) {
                Swal.showValidationMessage('Nama Perusahaan / Instansi wajib diisi.');
                return false;
            }
            if (!provId) {
                Swal.showValidationMessage('Provinsi Perusahaan wajib dipilih.');
                return false;
            }
            if (!kabId) {
                Swal.showValidationMessage('Kabupaten / Kota Perusahaan wajib dipilih.');
                return false;
            }
            if (!kodepos) {
                Swal.showValidationMessage('Kode Pos Perusahaan wajib diisi.');
                return false;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch('/alumni/company', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        nama_perusahaan: nama,
                        province_id: provId,
                        kabupaten_id: kabId,
                        kode_pos: kodepos,
                        skala: skala,
                        alamat: alamat
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.success || !data.company) {
                    Swal.showValidationMessage(data.message || 'Gagal menyimpan data perusahaan.');
                    return false;
                }
                return data.company;
            } catch (error) {
                Swal.showValidationMessage('Gagal menghubungi server. Silakan coba lagi.');
                return false;
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const newCompany = result.value;
            // Masukkan ke daftar options
            localCompanies.value.unshift(newCompany);

            // Set ke form profil
            props.form.nama_perusahaan = newCompany.nama_perusahaan;
            props.form.company_province_id = newCompany.province_id;
            props.form.company_kabupaten_id = newCompany.kabupaten_id;
            props.form.company_alamat = newCompany.alamat || '';
            props.form.company_skala = newCompany.skala || 'Lokal';
            props.form.zipcode = newCompany.kode_pos || '';
            props.form.company_status_verifikasi = 'Menunggu Verifikasi';

            Swal.fire({
                icon: 'success',
                title: 'Berhasil Ditambahkan!',
                text: `Perusahaan "${newCompany.nama_perusahaan}" berhasil ditambahkan dan dipilih.`,
                confirmButtonColor: '#005B3C',
                timer: 2500,
                timerProgressBar: true
            });
        }
    });
};

// ============================================================================
// CLICK OUTSIDE HANDLER
// ============================================================================
const handleClickOutside = (event) => {
    if (provinsiContainerRef.value && !provinsiContainerRef.value.contains(event.target)) {
        showProvinsiDropdown.value = false;
    }
    if (kabupatenContainerRef.value && !kabupatenContainerRef.value.contains(event.target)) {
        showKabupatenDropdown.value = false;
    }
    if (companyContainerRef.value && !companyContainerRef.value.contains(event.target)) {
        showCompanyDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="space-y-8">
        
        <!-- Bagian: Sosial Media & Profesional -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">
                Media Sosial & Profesional
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                    <label :class="labelClass">Bidang Keahlian (Expertise)</label>
                    <input type="text" v-model="form.expert" :class="inputClass" placeholder="Contoh: Software Engineering, Data Science..." />
                </div>
                <div class="md:col-span-2">
                    <label :class="labelClass">Minat & Ketertarikan</label>
                    <input type="text" v-model="form.minat" :class="inputClass" placeholder="Contoh: Artificial Intelligence, Cloud Computing..." />
                </div>

                <div class="md:col-span-2">
                    <label :class="labelClass">Posisi / Jabatan Saat Ini</label>
                    <select v-model="form.posisi_jabatan" :class="inputClass">
                        <option value="">-- Pilih Posisi Jabatan --</option>
                        <option value="Direksi / Top Manager">Direksi / Top Manager</option>
                        <option value="Midle Manager">Midle Manager</option>
                        <option value="Low manager">Low manager</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>

                <!-- Khusus Alumni Teologi / Filsafat Keilahian (Kode 31) -->
                <div v-if="alumniData?.prodi?.kode_prodi === '31' || alumniData?.nim?.startsWith('31')" class="md:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <label :class="labelClass">Jenis Pekerjaan (Khusus Alumni Filsafat Keilahian)</label>
                    <div class="flex gap-6 mt-2">
                        <label class="flex items-center space-x-2 cursor-pointer text-sm font-medium text-gray-700">
                            <input type="radio" v-model="form.jenis_pekerjaan" value="Gerejawi" class="text-[#005B3C] focus:ring-[#005B3C]">
                            <span>Gerejawi</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer text-sm font-medium text-gray-700">
                            <input type="radio" v-model="form.jenis_pekerjaan" value="Non Gerejawi" class="text-[#005B3C] focus:ring-[#005B3C]">
                            <span>Non Gerejawi</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian: Data Perusahaan Saat Ini -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">
                Data Perusahaan / Tempat Bekerja
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Searchable Dropdown: Provinsi Perusahaan -->
                <div class="relative" ref="provinsiContainerRef">
                    <label :class="labelClass">Provinsi Perusahaan</label>
                    
                    <div 
                        @click="showProvinsiDropdown = !showProvinsiDropdown; if (showProvinsiDropdown) searchProvinsiQuery = '';"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium cursor-pointer flex justify-between items-center shadow-xs hover:border-gray-300 transition-colors"
                    >
                        <span v-if="selectedProvinceName" class="text-gray-900 font-medium">{{ selectedProvinceName }}</span>
                        <span v-else class="text-gray-400">Pilih Provinsi...</span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transition-transform duration-200" :class="{'rotate-180': showProvinsiDropdown}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <!-- Dropdown Menu (Gaya Gambar 2) -->
                    <div v-if="showProvinsiDropdown" class="absolute z-50 w-full mt-1.5 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                        <div class="p-2.5 border-b border-gray-100 bg-gray-50/80">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input 
                                    type="text" 
                                    v-model="searchProvinsiQuery" 
                                    class="w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-1 focus:ring-[#005B3C] focus:border-[#005B3C] outline-none"
                                    placeholder="Ketik untuk mencari..."
                                    autofocus
                                >
                            </div>
                        </div>
                        <ul class="max-h-52 overflow-y-auto">
                            <li 
                                v-for="prov in filteredProvinces" 
                                :key="prov.id" 
                                @click="selectProvinsi(prov)" 
                                class="px-4 py-2.5 hover:bg-green-50 hover:text-[#005B3C] cursor-pointer text-sm font-medium transition-colors border-b border-gray-50 last:border-b-0"
                                :class="{'bg-green-50 text-[#005B3C] font-semibold': form.company_province_id == prov.id}"
                            >
                                {{ prov.nama_provinsi }}
                            </li>
                            <li v-if="filteredProvinces.length === 0" class="px-4 py-4 text-center text-sm text-gray-400">
                                Tidak ada hasil yang cocok.
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Searchable Dropdown: Kabupaten / Kota Perusahaan -->
                <div class="relative" ref="kabupatenContainerRef">
                    <label :class="labelClass">Kabupaten / Kota Perusahaan</label>
                    
                    <div 
                        @click="if (form.company_province_id) { showKabupatenDropdown = !showKabupatenDropdown; if (showKabupatenDropdown) searchKabupatenQuery = ''; }"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium cursor-pointer flex justify-between items-center shadow-xs hover:border-gray-300 transition-colors"
                        :class="{'opacity-60 cursor-not-allowed bg-gray-50': !form.company_province_id}"
                    >
                        <span v-if="selectedKabupatenName" class="text-gray-900 font-medium">{{ selectedKabupatenName }}</span>
                        <span v-else class="text-gray-400">
                            {{ form.company_province_id ? 'Pilih Kabupaten/Kota...' : 'Pilih Provinsi terlebih dahulu' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transition-transform duration-200" :class="{'rotate-180': showKabupatenDropdown}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>

                    <!-- Dropdown Menu (Gaya Gambar 2) -->
                    <div v-if="showKabupatenDropdown && form.company_province_id" class="absolute z-50 w-full mt-1.5 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                        <div class="p-2.5 border-b border-gray-100 bg-gray-50/80">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input 
                                    type="text" 
                                    v-model="searchKabupatenQuery" 
                                    class="w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-1 focus:ring-[#005B3C] focus:border-[#005B3C] outline-none"
                                    placeholder="Ketik untuk mencari..."
                                    autofocus
                                >
                            </div>
                        </div>
                        <ul class="max-h-52 overflow-y-auto">
                            <li 
                                v-for="kab in filteredKabupatens" 
                                :key="kab.id" 
                                @click="selectKabupaten(kab)" 
                                class="px-4 py-2.5 hover:bg-green-50 hover:text-[#005B3C] cursor-pointer text-sm font-medium transition-colors border-b border-gray-50 last:border-b-0"
                                :class="{'bg-green-50 text-[#005B3C] font-semibold': form.company_kabupaten_id == kab.id}"
                            >
                                {{ kab.nama_kabupaten }}
                            </li>
                            <li v-if="filteredKabupatens.length === 0" class="px-4 py-4 text-center text-sm text-gray-400">
                                Tidak ada hasil yang cocok.
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Searchable Dropdown: Nama Perusahaan / Instansi (PERSIS GAMBAR 2) -->
                <div class="md:col-span-2 relative" ref="companyContainerRef">
                    <label :class="labelClass">Nama Perusahaan / Instansi</label>
                    <div class="flex items-center gap-2">
                        <!-- Trigger Box bergaya Dropdown seperti Gambar 2 -->
                        <div 
                            @click="showCompanyDropdown = !showCompanyDropdown; if (showCompanyDropdown) searchCompanyQuery = '';"
                            class="flex-1 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium cursor-pointer flex justify-between items-center shadow-xs hover:border-gray-300 transition-colors"
                        >
                            <span v-if="form.nama_perusahaan" class="text-gray-900 font-semibold truncate">{{ form.nama_perusahaan }}</span>
                            <span v-else class="text-gray-400">Pilih nama perusahaan / instansi...</span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2 transition-transform duration-200" :class="{'rotate-180': showCompanyDropdown}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>

                        <!-- Tombol Tambah Perusahaan Baru -->
                        <button 
                            type="button" 
                            @click="openAddCompanyModal" 
                            class="px-4 py-3 bg-gray-900 text-white rounded-xl hover:bg-black transition-colors shrink-0 text-xs font-semibold flex items-center gap-1 shadow-xs" 
                            title="Tambah Perusahaan Baru"
                        >
                            <span>+ Tambah</span>
                        </button>
                    </div>

                    <!-- Dropdown Menu (Persis Gambar 2: Search Bar di atas + List Opsi) -->
                    <div v-if="showCompanyDropdown" class="absolute z-50 w-full mt-1.5 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                        <!-- Search Bar -->
                        <div class="p-2.5 border-b border-gray-100 bg-gray-50/80">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <input 
                                    type="text" 
                                    v-model="searchCompanyQuery" 
                                    class="w-full pl-9 pr-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-1 focus:ring-[#005B3C] focus:border-[#005B3C] outline-none"
                                    placeholder="Ketik untuk mencari..."
                                    autofocus
                                >
                            </div>
                        </div>

                        <!-- Options List -->
                        <ul class="max-h-56 overflow-y-auto">
                            <li 
                                v-for="company in filteredCompanies" 
                                :key="company.id" 
                                @click="selectCompany(company)" 
                                class="px-4 py-3 hover:bg-green-50 hover:text-[#005B3C] cursor-pointer text-sm font-medium transition-colors border-b border-gray-50 last:border-b-0 flex justify-between items-center"
                                :class="{'bg-green-50 text-[#005B3C] font-semibold': form.nama_perusahaan === company.nama_perusahaan}"
                            >
                                <div>
                                    <span class="block text-gray-800" :class="{'text-[#005B3C] font-bold': form.nama_perusahaan === company.nama_perusahaan}">
                                        {{ company.nama_perusahaan }}
                                    </span>
                                    <span class="text-xs text-gray-400" v-if="company.province_id">
                                        {{ provinces.find(p => p.id == company.province_id)?.nama_provinsi || '' }}
                                        {{ company.kabupaten_id ? ' - ' + (kabupatens.find(k => k.id == company.kabupaten_id)?.nama_kabupaten || '') : '' }}
                                    </span>
                                </div>
                                <span v-if="company.status_verifikasi === 'Terverifikasi'" class="text-xs text-emerald-600 font-semibold shrink-0 ml-2">
                                    Terverifikasi
                                </span>
                                <span v-else class="text-xs text-amber-600 font-semibold shrink-0 ml-2">
                                    Menunggu Verifikasi
                                </span>
                            </li>
                            <li v-if="filteredCompanies.length === 0" class="px-4 py-5 text-center text-sm text-gray-500">
                                <div>Tidak ada perusahaan yang cocok.</div>
                                <button type="button" @click="openAddCompanyModal" class="mt-2 text-xs font-semibold text-[#005B3C] underline hover:text-green-800">
                                    + Tambah Perusahaan Baru
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label :class="labelClass">Skala Perusahaan / Instansi</label>
                    <select v-model="form.company_skala" :class="inputClass">
                        <option value="">-- Pilih Skala Perusahaan --</option>
                        <option value="Lokal">Lokal</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label :class="labelClass">Alamat Perusahaan</label>
                    <input type="text" v-model="form.company_alamat" :class="inputClass" placeholder="Contoh: Pacific Building, Jl. Laksda Adisucipto No. 157, Sleman" />
                </div>

                <div class="md:col-span-2">
                    <label :class="labelClass">Kode Pos Perusahaan (Zipcode)</label>
                    <input type="text" v-model="form.zipcode" :class="inputClass" placeholder="Kode Pos (Cth: 55281)" />
                </div>
            </div>
        </div>

        <!-- Bagian: Data Atasan -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-2">
                Data Atasan Langsung
            </h2>
            <p class="text-xs text-gray-500 mb-6">
                Data pimpinan/atasan digunakan untuk keperluan survei evaluasi kepuasan pengguna lulusan oleh universitas.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label :class="labelClass">Nama Lengkap Atasan</label>
                    <input type="text" v-model="form.nama_atasan" :class="inputClass" placeholder="Contoh: Ir. Bambang Trihatmojo" />
                </div>
                
                <div>
                    <label :class="labelClass">Email Atasan</label>
                    <input type="email" v-model="form.email_atasan" :class="inputClass" placeholder="atasan@perusahaan.co.id" />
                </div>

                <div>
                    <label :class="labelClass">Nomor Telepon Atasan</label>
                    <input type="text" v-model="form.telepon_atasan" :class="inputClass" placeholder="081234567890" />
                </div>
            </div>
        </div>

    </div>
</template>
