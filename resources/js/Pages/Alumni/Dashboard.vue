<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { computed, onMounted, nextTick } from 'vue';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const props = defineProps({
    alumni: Object,
    responsesCount: Number,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const logout = () => {
    useForm().post('/logout');
};

// Check if today is alumni's birthday
const isBirthday = computed(() => {
    if (!props.alumni?.tanggal_lahir) return false;
    const today = new Date();
    const dob = new Date(props.alumni.tanggal_lahir);
    return today.getMonth() === dob.getMonth() && today.getDate() === dob.getDate();
});

// Gamification: Progress
const TOTAL_QUESTIONS = 32;
const progressPercentage = computed(() => {
    if (!props.responsesCount) return 0;
    const pct = Math.round((props.responsesCount / TOTAL_QUESTIONS) * 100);
    return pct > 100 ? 100 : pct;
});

// Terakhir Diperbarui (Last Update)
const lastUpdateText = computed(() => {
    if (!props.alumni?.updated_at) return 'Belum pernah diperbarui';
    const updated = new Date(props.alumni.updated_at);
    const now = new Date();
    
    const diffMonths = (now.getFullYear() - updated.getFullYear()) * 12 + (now.getMonth() - updated.getMonth());
    
    if (diffMonths === 0) {
        const diffDays = Math.floor((now - updated) / (1000 * 60 * 60 * 24));
        if (diffDays === 0) return 'Hari ini';
        return `${diffDays} hari yang lalu`;
    }
    
    return `${diffMonths} bulan yang lalu`;
});

// Gamification: Level
const alumniLevel = computed(() => {
    if (progressPercentage.value < 50) return { name: 'Alumni Baru', color: 'text-blue-500', bg: 'bg-blue-100' };
    if (progressPercentage.value < 100) return { name: 'Alumni Aktif', color: 'text-purple-500', bg: 'bg-purple-100' };
    return { name: 'Alumni Bintang', color: 'text-yellow-600', bg: 'bg-yellow-100' };
});

// Dummy Lowongan Kerja
const jobs = [
    {
        id: 1,
        title: 'Fullstack Web Developer',
        company: 'PT. Teknologi Masa Depan',
        location: 'Jakarta Selatan (Hybrid)',
        type: 'Full-Time',
        salary: 'Rp 8.000.000 - Rp 12.000.000',
        logo: 'https://ui-avatars.com/api/?name=TM&background=0D8ABC&color=fff',
        posted_at: '2 hari yang lalu'
    },
    {
        id: 2,
        title: 'UI/UX Designer',
        company: 'Kreatif Digital Studio',
        location: 'Yogyakarta (WFO)',
        type: 'Full-Time',
        salary: 'Dirahasiakan',
        logo: 'https://ui-avatars.com/api/?name=KD&background=F59E0B&color=fff',
        posted_at: '5 hari yang lalu'
    },
    {
        id: 3,
        title: 'Data Analyst',
        company: 'Bank Central Nusantara',
        location: 'Remote',
        type: 'Contract',
        salary: 'Rp 7.000.000',
        logo: 'https://ui-avatars.com/api/?name=BC&background=10B981&color=fff',
        posted_at: '1 minggu yang lalu'
    }
];

// GSAP Animations with ScrollTrigger
onMounted(() => {
    nextTick(() => {
        // Animasi Hero Banner (tanpa ScrollTrigger karena di atas)
        gsap.fromTo('.gsap-hero', 
            { opacity: 0, y: 30 },
            { opacity: 1, y: 0, duration: 1, ease: 'power3.out' }
        );

        // Animasi Cards bertahap (pakai ScrollTrigger)
        gsap.utils.toArray('.gsap-card').forEach((card, index) => {
            gsap.fromTo(card,
                { opacity: 0, y: 40 },
                { 
                    opacity: 1, 
                    y: 0, 
                    duration: 0.8, 
                    ease: 'back.out(1.2)', 
                    scrollTrigger: {
                        trigger: card,
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                }
            );
        });

        // Animasi Job Cards (pakai ScrollTrigger)
        gsap.utils.toArray('.gsap-job').forEach((job, index) => {
            gsap.fromTo(job,
                { opacity: 0, x: -20 },
                { 
                    opacity: 1, 
                    x: 0, 
                    duration: 0.6, 
                    delay: index * 0.1, // Stagger manual berdasarkan index
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: '.gsap-job-container',
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                }
            );
        });
    });
});
</script>

<template>
    <Head title="Dashboard Alumni - Tracer Study UKDW" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased overflow-x-hidden">
        
        <!-- Navbar Hijau -->
        <nav class="bg-[#005B3C] shadow-lg border-b border-[#00422c] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center space-x-4">
                        <img src="/uploads/logo/logo-ukdw.png" alt="UKDW Logo" class="h-12 object-contain bg-white rounded p-1 shadow-sm">
                        <div class="flex-shrink-0 flex items-center border-l-2 border-[#007b52] pl-4 ml-4">
                            <span class="text-white font-bold text-xl tracking-wide uppercase">Tracer Study</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="hidden sm:flex flex-col items-end">
                            <span class="text-white font-semibold text-sm">{{ user.name }}</span>
                            <span class="text-green-200 text-xs">Alumni</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-white text-[#005B3C] flex items-center justify-center font-bold text-lg shadow-md">
                            {{ user.name.charAt(0) }}
                        </div>
                        <button @click="logout" class="text-green-100 hover:text-yellow-400 transition-colors flex items-center text-sm font-medium">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Keluar
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
            
            <!-- Birthday Banner -->
            <div v-if="isBirthday" class="gsap-hero bg-gradient-to-r from-[#FFD700] to-yellow-400 p-8 rounded-2xl flex items-center justify-between shadow-md relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-[#005B3C] mb-2">Selamat Ulang Tahun, {{ user.name }}! 🎂</h2>
                    <p class="text-[#005B3C] font-medium opacity-90">Semoga karier Anda semakin bersinar dan sukses selalu bersama almamater tercinta.</p>
                </div>
            </div>

            <!-- Welcome Hero (Gamified & Premium Style) -->
            <div class="gsap-hero bg-white rounded-3xl shadow-lg overflow-hidden flex flex-col md:flex-row border border-gray-100">
                <div class="md:w-1/2 relative min-h-[350px] bg-green-900">
                    <!-- Menggunakan foto yang diupload (3.png) -->
                    <img src="/uploads/landing/3.png" onerror="this.src='https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80'" alt="Dashboard Banner" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-80 filter brightness-110">
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 to-transparent"></div>
                    <div class="absolute inset-0 p-10 flex flex-col justify-center text-white z-10">
                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-yellow-300 text-xs font-bold tracking-widest uppercase mb-4 w-max border border-white/30">
                            EKOSISTEM ALUMNI UKDW
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black leading-tight mb-4 tracking-tight">
                            Kembangkan<br>Kariermu Bersama<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-[#FFD700]">Tracer Study</span>
                        </h1>
                    </div>
                </div>
                <div class="md:w-1/2 bg-[#005B3C] p-10 flex flex-col justify-center text-white relative overflow-hidden">
                    <div class="absolute top-[-20%] right-[-10%] w-64 h-64 rounded-full bg-green-600 opacity-20 blur-3xl"></div>
                    
                    <h2 class="text-3xl font-bold mb-4 text-white relative z-10 flex items-center gap-3">
                        <svg class="w-8 h-8 text-[#FFD700]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        Tujuan Utama
                    </h2>
                    <p class="text-gray-200 text-lg leading-relaxed mb-8 relative z-10 font-light">
                        Pengisian kuesioner ini sangat esensial untuk mengevaluasi relevansi kurikulum dengan dunia kerja masa kini, demi kemajuan almamater dan akreditasi Universitas.
                    </p>
                    <div class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/20 relative z-10 transform transition hover:scale-[1.02] duration-300">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-[#FFD700] rounded-full">
                                <svg class="w-6 h-6 text-[#005B3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#FFD700] text-lg">Partisipasi Aktif Anda</h3>
                                <p class="text-sm text-gray-200">Kontribusi kecil Anda berdampak besar bagi kampus.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Actions & Status Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Kuesioner Card (Utama) -->
                <div class="gsap-card lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-bl-full -z-0 transition-transform group-hover:scale-110 duration-500"></div>
                    
                    <div class="flex items-start mb-8 relative z-10">
                        <div class="p-4 bg-green-100 rounded-2xl mr-6 text-[#005B3C] group-hover:bg-[#005B3C] group-hover:text-white transition-colors duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Kuesioner Tracer Study 2026</h3>
                            <p class="text-gray-500 text-sm leading-relaxed max-w-xl">
                                Rekam jejak karier Anda sangat berharga. Sistem otomatis menyimpan progres Anda sehingga dapat dilanjutkan kapan saja.
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-auto bg-gray-50 rounded-2xl p-6 border border-gray-100 relative z-10">
                        <div class="flex justify-between items-end mb-4">
                            <span class="text-sm font-bold text-gray-700 uppercase tracking-wider">Progres Penyelesaian</span>
                            <span class="text-2xl font-black text-[#005B3C]">{{ progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 h-3 rounded-full overflow-hidden mb-6 shadow-inner">
                            <div class="bg-gradient-to-r from-green-500 to-[#005B3C] h-full rounded-full transition-all duration-1500 ease-out relative" :style="{ width: progressPercentage + '%' }">
                                <div class="absolute top-0 right-0 bottom-0 w-10 bg-white opacity-20 transform rotate-12 translate-x-2"></div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center bg-white px-4 py-2 rounded-full border border-gray-200 shadow-sm">
                                <div class="w-3 h-3 rounded-full mr-3 animate-pulse" :class="progressPercentage >= 100 ? 'bg-green-500' : (progressPercentage > 0 ? 'bg-yellow-500' : 'bg-red-400')"></div>
                                <span class="text-sm text-gray-700 font-bold">
                                    {{ progressPercentage >= 100 ? 'Tugas Selesai!' : (progressPercentage > 0 ? 'Sedang Dikerjakan' : 'Belum Dimulai') }}
                                </span>
                            </div>
                            <Link href="/alumni/kuesioner" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-[#005B3C] hover:bg-[#00422c] hover:shadow-lg hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-4 focus:ring-[#005B3C]/30">
                                {{ progressPercentage > 0 && progressPercentage < 100 ? 'Lanjutkan Kuesioner' : 'Mulai Sekarang' }}
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Profil & Biodata Card -->
                <div class="gsap-card bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-transparent to-blue-50 opacity-50"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Data Profil</h3>
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-6 flex-grow">Lengkapi Data Akademik dan Profil Profesional Anda agar kuesioner terisi lebih cepat (auto-fill).</p>
                        
                        <div class="mt-auto">
                            <Link href="/alumni/profile" class="block w-full text-center px-4 py-3 border-2 border-blue-600 text-blue-600 font-bold rounded-xl hover:bg-blue-600 hover:text-white transition-colors duration-300">
                                Lengkapi Biodata
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Status Update Card -->
                <div class="gsap-card bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-50 opacity-50"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Status Karier</h3>
                            <div :class="`px-3 py-1 rounded-full text-xs font-bold ${alumniLevel.bg} ${alumniLevel.color}`">
                                {{ alumniLevel.name }}
                            </div>
                        </div>

                        <!-- Last Update Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-8 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="bg-[#005B3C] p-3 rounded-full text-white shadow-md">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500 font-medium">Terakhir Diperbarui</div>
                                    <div class="text-lg font-black text-gray-900">{{ lastUpdateText }}</div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <span class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 block">Badges (Pencapaian)</span>
                                <div class="flex gap-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 cursor-help" title="Profil Lengkap">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 cursor-help" title="Responden Aktif">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="w-12 h-12 border-2 border-dashed border-gray-300 rounded-full flex items-center justify-center text-gray-300" title="Kunci Terbuka Jika Kuesioner 100%">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Link href="#" class="w-full inline-flex items-center justify-center px-6 py-3 border-2 border-gray-200 shadow-sm text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all mt-4">
                            Perbarui Profil Saya
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Bagian Lowongan Kerja (Job Board) -->
            <div class="mt-12 gsap-job-container">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 flex items-center gap-3">
                            Peluang Karier Terkini
                            <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full uppercase tracking-wide">Hot</span>
                        </h2>
                        <p class="text-gray-500 mt-2">Daftar lowongan pekerjaan pilihan khusus untuk alumni UKDW.</p>
                    </div>
                    <Link href="#" class="hidden sm:flex text-[#005B3C] font-bold hover:text-green-800 transition-colors items-center gap-1">
                        Lihat Semua 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="job in jobs" :key="job.id" class="gsap-job bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
                        <div class="flex justify-between items-start mb-6">
                            <img :src="job.logo" :alt="job.company" class="w-14 h-14 rounded-2xl shadow-sm border border-gray-100 group-hover:scale-110 transition-transform">
                            <span class="text-xs font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">{{ job.posted_at }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-[#005B3C] transition-colors">{{ job.title }}</h3>
                        <p class="text-gray-500 text-sm font-medium mb-4">{{ job.company }}</p>
                        
                        <div class="flex flex-wrap gap-2 mb-6">
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-lg border border-green-100 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ job.type }}
                            </span>
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ job.location }}
                            </span>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                            <div>
                                <span class="block text-xs text-gray-400 font-medium mb-1">Gaji</span>
                                <span class="text-sm font-bold text-gray-900">{{ job.salary }}</span>
                            </div>
                            <button class="w-10 h-10 rounded-full bg-[#005B3C] text-white flex items-center justify-center transform group-hover:bg-green-700 group-hover:scale-110 transition-all shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</template>

<style scoped>
/* Optional specific overrides if necessary */
</style>
