<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import gsap from 'gsap';

const props = defineProps({
    alumniData: Object,
    formData: Object,
    provinces: Array,
    kabupatens: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Menerima form data yang sudah dirakit 100% oleh backend
const form = useForm(props.formData);

const submit = () => {
    form.post(route('alumni.profile'), {
        preserveScroll: true,
    });
};

onMounted(() => {
    gsap.fromTo('.gsap-fade-up', 
        { opacity: 0, y: 30 },
        { opacity: 1, y: 0, duration: 0.8, stagger: 0.1, ease: 'power3.out' }
    );
});
</script>

<template>
    <Head title="Profil Alumni - Tracer Study" />

    <div class="min-h-screen bg-gray-50 pb-20">
        <!-- Navbar Minimal -->
        <nav class="bg-[#005B3C] shadow-lg border-b border-[#00422c] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center space-x-4">
                        <Link href="/alumni/dashboard" class="text-white hover:text-yellow-400 p-2 rounded-full transition-colors flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Dashboard
                        </Link>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="hidden sm:flex flex-col items-end">
                            <span class="text-white font-semibold text-sm">{{ user.name }}</span>
                            <span class="text-green-200 text-xs">Alumni</span>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="mb-8 gsap-fade-up">
                <h1 class="text-3xl font-black text-gray-900 mb-2">Profil & Biodata</h1>
                <p class="text-gray-600">Lengkapi data akademik dan profesional Anda untuk memudahkan mapping di sistem Tracer Study.</p>
            </div>

            <!-- Pesan Sukses -->
            <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded gsap-fade-up">
                <p class="font-bold">Berhasil</p>
                <p>{{ $page.props.flash.success }}</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                
                <!-- Data Pribadi (Readonly) -->
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 gsap-fade-up relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-50 rounded-bl-full -z-0"></div>
                    <h2 class="text-xl font-bold text-[#005B3C] mb-6 flex items-center border-b pb-4 relative z-10">
                        <svg class="w-6 h-6 mr-3 text-[#FFD700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Data Pribadi (Bawaan Sistem)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" :value="user.name" disabled class="block w-full bg-gray-100 border-gray-200 rounded-xl shadow-sm text-gray-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                            <input type="text" :value="user.username" disabled class="block w-full bg-gray-100 border-gray-200 rounded-xl shadow-sm text-gray-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                            <input type="text" :value="alumniData?.prodi?.nama_prodi" disabled class="block w-full bg-gray-100 border-gray-200 rounded-xl shadow-sm text-gray-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Utama</label>
                            <input type="text" :value="user.email" disabled class="block w-full bg-gray-100 border-gray-200 rounded-xl shadow-sm text-gray-500" />
                        </div>
                    </div>
                </div>

                <!-- Data Akademik -->
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 gsap-fade-up relative overflow-hidden">
                    <h2 class="text-xl font-bold text-[#005B3C] mb-6 flex items-center border-b pb-4 relative z-10">
                        <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Data Akademik Tambahan
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas Akhir / Skripsi</label>
                            <textarea v-model="form.judul_ta" rows="2" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="Masukkan judul TA Anda..."></textarea>
                            <div v-if="form.errors.judul_ta" class="text-red-500 text-xs mt-1">{{ form.errors.judul_ta }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Mahasiswa Saat Ini</label>
                            <select v-model="form.status_mahasiswa" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors">
                                <option value="Lulus">Lulus</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                            <div v-if="form.errors.status_mahasiswa" class="text-red-500 text-xs mt-1">{{ form.errors.status_mahasiswa }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus (Berdasarkan Yudisium)</label>
                            <input type="number" v-model="form.tahun_lulus" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="Contoh: 2024" />
                            <div v-if="form.errors.tahun_lulus" class="text-red-500 text-xs mt-1">{{ form.errors.tahun_lulus }}</div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Saat Ini</label>
                            <textarea v-model="form.alamat_saat_ini" rows="2" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="Masukkan alamat domisili Anda saat ini..."></textarea>
                            <div v-if="form.errors.alamat_saat_ini" class="text-red-500 text-xs mt-1">{{ form.errors.alamat_saat_ini }}</div>
                        </div>
                    </div>
                </div>

                <!-- Data Profesional -->
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 gsap-fade-up relative overflow-hidden">
                    <h2 class="text-xl font-bold text-[#005B3C] mb-6 flex items-center border-b pb-4 relative z-10">
                        <svg class="w-6 h-6 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Karier & Pekerjaan Saat Ini
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan (Bila Sudah Bekerja)</label>
                            <input type="text" v-model="form.nama_perusahaan" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="PT. Contoh Nama Perusahaan" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah Kerja: Provinsi</label>
                            <select v-model="form.company_province_id" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors">
                                <option value="">-- Pilih Provinsi --</option>
                                <option v-for="prov in provinces" :key="prov.id" :value="prov.id">{{ prov.nama_provinsi }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah Kerja: Kabupaten/Kota</label>
                            <select v-model="form.company_kabupaten_id" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors">
                                <option value="">-- Pilih Kabupaten --</option>
                                <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id" v-show="!form.company_province_id || kab.province_id == form.company_province_id">
                                    {{ kab.nama_kabupaten }}
                                </option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos (Zipcode) Wilayah Kerja</label>
                            <input type="text" v-model="form.zipcode" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keahlian (Expertise)</label>
                            <input type="text" v-model="form.expert" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="Contoh: Frontend Developer, Akuntan Publik" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Minat (Interest)</label>
                            <input type="text" v-model="form.minat" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="Contoh: AI, UI/UX, Data Science" />
                        </div>
                    </div>
                </div>

                <!-- Sosial Media -->
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 gsap-fade-up relative overflow-hidden">
                    <h2 class="text-xl font-bold text-[#005B3C] mb-6 flex items-center border-b pb-4 relative z-10">
                        <svg class="w-6 h-6 mr-3 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        Jejaring & Media Sosial
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL LinkedIn</label>
                            <input type="url" v-model="form.linkedin_url" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="https://linkedin.com/in/..." />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username LinkedIn</label>
                            <input type="text" v-model="form.linkedin_username" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="johndoe" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Instagram</label>
                            <input type="url" v-model="form.instagram_url" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="https://instagram.com/..." />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Facebook</label>
                            <input type="url" v-model="form.facebook_url" class="block w-full border-gray-300 rounded-xl shadow-sm focus:border-green-500 focus:ring-green-500 transition-colors" placeholder="https://facebook.com/..." />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 gsap-fade-up">
                    <button type="submit" :disabled="form.processing" class="px-8 py-3 bg-[#005B3C] text-white font-bold rounded-xl shadow-lg hover:bg-[#00422c] hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="form.processing">Menyimpan...</span>
                        <span v-else>Simpan Profil</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
