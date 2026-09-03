<script setup>
import { ref, computed } from 'vue';
import AlumniCard from './alumni-card.vue';

// Dummy data wilayah (Provinsi)
const provinces = ['Semua Wilayah', 'DKI Jakarta', 'DI Yogyakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'Bali'];
const activeProvince = ref('Semua Wilayah');

// Dummy data alumni yang sudah direstrukturisasi untuk profile profesional
const alumniData = [
    {
        id: 1,
        name: 'Sarah Jennifer, S.Kom',
        prodi: 'Sistem Informasi',
        angkatan: 2018,
        job: 'Software Engineer',
        company: 'Gojek Indonesia',
        province: 'DKI Jakarta',
        linkedin: 'https://linkedin.com',
        image: 'https://ui-avatars.com/api/?name=Sarah+Jennifer&background=random'
    },
    {
        id: 2,
        name: 'Budi Santoso, S.T.',
        prodi: 'Teknik Informatika',
        angkatan: 2017,
        job: 'Data Analyst',
        company: 'Tokopedia',
        province: 'DKI Jakarta',
        linkedin: 'https://linkedin.com',
        image: 'https://ui-avatars.com/api/?name=Budi+Santoso&background=random'
    },
    {
        id: 3,
        name: 'Maria Kristi, S.Des',
        prodi: 'Desain Produk',
        angkatan: 2019,
        job: 'UX Designer',
        company: 'Traveloka',
        province: 'Banten',
        linkedin: 'https://linkedin.com',
        image: 'https://ui-avatars.com/api/?name=Maria+Kristi&background=random'
    },
    {
        id: 4,
        name: 'Antonius Wijaya, S.E.',
        prodi: 'Manajemen',
        angkatan: 2016,
        job: 'Marketing Manager',
        company: 'PT. Gudang Garam Tbk',
        province: 'Jawa Timur',
        linkedin: 'https://linkedin.com',
        image: 'https://ui-avatars.com/api/?name=Antonius+Wijaya&background=random'
    },
    {
        id: 5,
        name: 'Clara Putri, S.Ars',
        prodi: 'Arsitektur',
        angkatan: 2020,
        job: 'Junior Architect',
        company: 'Urbane Indonesia',
        province: 'Jawa Barat',
        linkedin: 'https://linkedin.com',
        image: 'https://ui-avatars.com/api/?name=Clara+Putri&background=random'
    },
    {
        id: 6,
        name: 'Yohanes Peter, S.Kom',
        prodi: 'Informatika',
        angkatan: 2018,
        job: 'Backend Developer',
        company: 'Gamatechno',
        province: 'DI Yogyakarta',
        linkedin: 'https://linkedin.com',
        image: 'https://ui-avatars.com/api/?name=Yohanes+Peter&background=random'
    }
];

// Computed property untuk memfilter alumni berdasarkan provinsi yang dipilih
const filteredAlumni = computed(() => {
    if (activeProvince.value === 'Semua Wilayah') {
        return alumniData;
    }
    return alumniData.filter(alumni => alumni.province === activeProvince.value);
});

const setProvince = (prov) => {
    activeProvince.value = prov;
};
</script>

<template>
    <section id="alumni" class="py-20 bg-gray-50 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 gsap-fade-up">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-black text-gray-900 sm:text-4xl uppercase tracking-tight">Sebaran Alumni</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Jejak karier profesional lulusan Universitas Kristen Duta Wacana di berbagai wilayah dan perusahaan terkemuka.
                </p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button 
                    v-for="prov in provinces" 
                    :key="prov"
                    @click="setProvince(prov)"
                    :class="[
                        'px-5 py-2 rounded-full text-sm font-semibold transition-all duration-300 shadow-sm border',
                        activeProvince === prov 
                            ? 'bg-green-600 text-white border-green-600 shadow-md transform scale-105' 
                            : 'bg-white text-gray-600 border-gray-200 hover:border-green-400 hover:text-green-600 hover:bg-green-50'
                    ]"
                >
                    {{ prov }}
                </button>
            </div>

            <!-- Alumni Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Transition Group bisa ditambahkan nanti jika menggunakan Vue Transition, untuk saat ini tampilkan langsung -->
                <AlumniCard 
                    v-for="alumni in filteredAlumni" 
                    :key="alumni.id" 
                    :alumni="alumni" 
                />
            </div>
            
            <!-- Empty State -->
            <div v-if="filteredAlumni.length === 0" class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                <h3 class="text-lg font-medium text-gray-900">Belum ada data</h3>
                <p class="mt-1 text-gray-500">Belum ada alumni yang terdata di wilayah {{ activeProvince }}.</p>
            </div>

            <div class="mt-16 text-center">
                <a href="#" class="inline-flex items-center text-green-600 font-bold hover:text-green-700 transition-colors">
                    Lihat Direktori Alumni Selengkapnya
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>
</template>
