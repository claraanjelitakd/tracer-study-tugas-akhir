<script setup>
// Mock data untuk Berita
const beritaData = [
    {
        id: 1,
        title: 'UKDW Gelar Tracer Study 2026, Targetkan 95% Responden',
        category: 'Kampus',
        date: '15 September 2026',
        image: 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=600',
        excerpt: 'Universitas Kristen Duta Wacana kembali menggelar Tracer Study tahunan untuk melacak jejak karier para alumninya...'
    },
    {
        id: 2,
        title: 'Kisah Sukses Alumni FK UKDW di Pelosok Negeri',
        category: 'Alumni',
        date: '10 September 2026',
        image: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&q=80&w=600',
        excerpt: 'Dedikasi tinggi ditunjukkan oleh dr. Budi Santoso, alumnus Fakultas Kedokteran UKDW yang kini mengabdi di Puskesmas perbatasan...'
    },
    {
        id: 3,
        title: 'Lowongan Pekerjaan: IT Development Program Bank BCA',
        category: 'Karir',
        date: '5 September 2026',
        image: 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?auto=format&fit=crop&q=80&w=600',
        excerpt: 'Bank BCA kembali membuka kesempatan bagi fresh graduate program studi Teknik Informatika dan Sistem Informasi UKDW...'
    }
];
</script>

<template>
    <section class="py-20 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 gsap-fade-up">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div class="max-w-2xl">
                    <h2 class="text-3xl font-black text-gray-900 sm:text-4xl uppercase tracking-tight">Berita Terbaru</h2>
                    <p class="mt-4 text-lg text-gray-600">
                        Kumpulan berita seputar kampus, profil alumni sukses, dan informasi lowongan kerja terkini.
                    </p>
                </div>
                <div class="mt-6 md:mt-0">
                    <a href="#" class="inline-flex items-center px-6 py-2 border-2 border-green-600 text-green-700 font-bold rounded hover:bg-green-600 hover:text-white transition-colors duration-300">
                        Lihat Semua Berita
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card Berita -->
                <div 
                    v-for="berita in beritaData" 
                    :key="berita.id"
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-2 hover:border-green-200 transition-all duration-300 group flex flex-col cursor-pointer"
                >
                    <div class="relative h-48 overflow-hidden bg-gray-200">
                        <img :src="berita.image" :alt="berita.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 left-4 bg-yellow-400 text-green-900 text-xs font-bold px-3 py-1 rounded shadow-md uppercase">
                            {{ berita.category }}
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="text-sm text-gray-500 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ berita.date }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-700 transition-colors leading-tight">
                            {{ berita.title }}
                        </h3>
                        <p class="text-gray-600 text-sm flex-1">
                            {{ berita.excerpt }}
                        </p>
                        <div class="mt-6 text-green-600 font-semibold group-hover:text-green-800 flex items-center transition-colors">
                            Baca Selengkapnya
                            <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
