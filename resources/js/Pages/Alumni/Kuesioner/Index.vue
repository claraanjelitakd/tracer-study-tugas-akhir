<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import SearchableSelect from '@/components/form/searchable-select.vue';
// GSAP digunakan untuk animasi masuk elemen (stagger animation) agar tidak kaku
import gsap from 'gsap';

// Mendefinisikan properti yang diterima dari controller (Inertia)
const props = defineProps({
    questionnaire: Object,   // Data kuesioner aktif (judul, seksi, pertanyaan)
    initialAnswers: Object,  // Jawaban yang sudah dirakit 100% oleh backend
    error: String,           // Pesan error jika kuesioner tidak valid
});

// Menyimpan index seksi/tahap kuesioner yang sedang aktif (dimulai dari 0)
const activeSectionIndex = ref(0);

// State untuk mengatur kapan animasi Pop-up "Pujian/XP" muncul
const showPointsAnimation = ref(false);
const earnedPoints = ref(''); // Berisi teks pujian seperti "MANTAP!", "HEBAT!"

// Inisialisasi useForm Inertia dengan data murni dari backend
const form = useForm({ answers: props.initialAnswers || {} });

// Computed property untuk mendapatkan objek seksi (tahap) yang sedang aktif
const currentSection = computed(() => {
    return props.questionnaire?.sections[activeSectionIndex.value];
});

// LOGIKA LOMPATAN (JUMP LOGIC)
// Digunakan untuk menyembunyikan pertanyaan/seksi tertentu berdasarkan jawaban pengguna (Contoh: "Jika belum bekerja, lewati seksi Pekerjaan")
const hiddenQuestions = ref(new Set()); // Menyimpan ID pertanyaan yang harus disembunyikan
const currentTargetSectionIndex = ref(null); // Menyimpan index seksi tujuan jika terjadi lompatan seksi

const evaluateJumpLogic = () => {
    let hideSet = new Set();
    currentTargetSectionIndex.value = null; // Reset tujuan lompatan
    
    props.questionnaire?.sections.forEach((section, sIndex) => {
        section.questions.forEach((q, qIndex) => {
            if (q.jump_logic) {
                const rules = q.jump_logic; // Contoh: { "Tidak": "F8" } (Jika jawab Tidak, lompat ke pertanyaan kode F8)
                const answer = form.answers[q.id];
                let targetCodeToJump = null;
                
                // Mencari apakah jawaban pengguna memicu aturan lompatan
                if (typeof answer === 'string' && rules[answer]) {
                    targetCodeToJump = rules[answer];
                } else if (typeof answer === 'object' && answer.selected && rules[answer.selected]) {
                    targetCodeToJump = rules[answer.selected];
                }

                // Jika ada lompatan, cari posisi pertanyaan asal dan tujuan
                if (targetCodeToJump) {
                    let startIndex = -1;
                    let targetIndex = -1;
                    let allQ = [];
                    
                    // Kumpulkan semua pertanyaan ke dalam satu array datar untuk menghitung posisi
                    props.questionnaire.sections.forEach(s => {
                        s.questions.forEach(sq => {
                            allQ.push({ q: sq, sectionIdx: s.order - 1 });
                        });
                    });

                    allQ.forEach((sq, i) => {
                        if (sq.q.code === q.code) startIndex = i; // Posisi pertanyaan pemicu
                        if (sq.q.code === targetCodeToJump) {
                            targetIndex = i; // Posisi pertanyaan tujuan
                            // Jika kita berada di seksi yang sama dengan pemicu, catat seksi tujuan
                            if (activeSectionIndex.value === section.order - 1) {
                                currentTargetSectionIndex.value = sq.sectionIdx;
                            }
                        }
                    });

                    // Menyembunyikan semua pertanyaan yang berada di antara pemicu dan tujuan
                    if (startIndex !== -1 && targetIndex !== -1 && targetIndex > startIndex) {
                        for (let i = startIndex + 1; i < targetIndex; i++) {
                            hideSet.add(allQ[i].q.id);
                        }
                    }
                }
            }
        });
    });
    
    // Perbarui daftar pertanyaan yang disembunyikan
    hiddenQuestions.value = hideSet;
};

// Pantau setiap perubahan jawaban di form, lalu evaluasi ulang logika lompatan
watch(form.answers, () => {
    evaluateJumpLogic();
}, { deep: true });

// Saat halaman pertama kali dimuat, evaluasi logika lompatan dan mainkan animasi masuk
onMounted(() => {
    evaluateJumpLogic();
    animateFormEntry();
});

// Fungsi memunculkan pop-up pujian (Gamifikasi)
const triggerPointsAnimation = () => {
    const compliments = ["HEBAT!", "MANTAP!", "KEREN!", "TERSIMPAN!", "LUAR BIASA!", "LANJUTKAN!"];
    // Pilih pujian acak dari array
    earnedPoints.value = compliments[Math.floor(Math.random() * compliments.length)]; 
    showPointsAnimation.value = true;
    
    // Sembunyikan pop-up setelah 2 detik
    setTimeout(() => { showPointsAnimation.value = false; }, 2000);
};

// Fungsi GSAP: Membuat animasi bergelombang (stagger) saat form/pertanyaan muncul
const animateFormEntry = () => {
    nextTick(() => {
        // Menganimasikan elemen dengan class 'q-card' dari bawah (y:30) dan transparan (opacity: 0) ke atas (y:0)
        gsap.fromTo('.q-card', 
            { opacity: 0, scale: 0.95, y: 30 },
            { opacity: 1, scale: 1, y: 0, duration: 0.6, stagger: 0.1, ease: 'back.out(1.5)' } // ease back memberikan efek pantulan
        );
    });
};

// Pindah ke seksi/halaman selanjutnya
const nextSection = () => {
    // Kirim data ke backend untuk disimpan sementara
    form.post('/alumni/kuesioner', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            triggerPointsAnimation(); // Tampilkan pujian

            // Jika ada logika lompatan, pindah ke seksi tujuan, jika tidak, pindah ke seksi berikutnya
            if (currentTargetSectionIndex.value !== null) {
                activeSectionIndex.value = currentTargetSectionIndex.value;
            } else if (activeSectionIndex.value < props.questionnaire.sections.length - 1) {
                activeSectionIndex.value++;
            }
            
            // Kembalikan scroll layar ke atas
            window.scrollTo(0,0);
            animateFormEntry(); // Jalankan ulang animasi pertanyaan masuk
        }
    });
};

// Kembali ke seksi sebelumnya
const prevSection = () => {
    if (activeSectionIndex.value > 0) {
        activeSectionIndex.value--;
        window.scrollTo(0,0);
        animateFormEntry();
    }
};

// Berpindah seksi melalui Stepper navigasi di atas
const setSection = (index) => {
    activeSectionIndex.value = index;
    window.scrollTo(0,0);
    animateFormEntry();
};

// Fungsi pembantu untuk tipe checkbox (menambah atau menghapus dari array)
const toggleCheckbox = (qId, val) => {
    const arr = form.answers[qId];
    if (arr.includes(val)) {
        form.answers[qId] = arr.filter(item => item !== val); // hapus jika sudah ada
    } else {
        form.answers[qId].push(val); // tambah jika belum ada
    }
};

// Mencegah karakter ilegal diketik pada input angka (huruf 'e', dll)
const filterNumberInput = (event) => {
    if (['e', 'E', '+', '-', '.'].includes(event.key)) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Kuesioner Tracer Study" />

    <!-- Latar Belakang Hijau Muda ala Quizizz -->
    <div class="min-h-screen bg-[#E8F5E9] font-sans text-gray-900 antialiased flex flex-col relative pb-20">
        
        <!-- Gamification Toast (Pop-up Pujian saat klik Lanjut) -->
        <!-- Memakai komponen Vue <Transition> untuk menerapkan animasi CSS khusus (bounce) -->
        <Transition name="bounce">
            <div v-if="showPointsAnimation" class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[100] flex flex-col items-center pointer-events-none">
                <div class="bg-[#FFD700] text-[#005B3C] font-black text-3xl px-8 py-4 rounded-3xl shadow-[0_8px_0_0_#D4AF37] flex items-center gap-3 border-4 border-white transform rotate-3">
                    <!-- Ikon Bintang Berputar (animate-bounce dari Tailwind) -->
                    <svg class="w-10 h-10 text-white animate-bounce" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    {{ earnedPoints }}
                </div>
            </div>
        </Transition>

        <!-- Top Navigation (Clean, Quizizz Style) -->
        <nav class="bg-[#005B3C] shadow-md sticky top-0 z-50 py-3">
            <div class="max-w-4xl mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <!-- Logo dipanggil dari folder public/uploads/logo -->
                    <div class="bg-white p-1.5 rounded-xl shadow-sm">
                        <img src="/uploads/logo/logo-ukdw.png" onerror="this.src='https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png'" alt="UKDW Logo" class="h-10 w-10 object-contain">
                    </div>
                    <span class="text-white font-black text-2xl tracking-wide">Tracer Study</span>
                </div>
                <!-- Tombol kembali ke Dashboard (Style Glassmorphism sederhana) -->
                <a href="/alumni/dashboard" class="px-5 py-2.5 bg-white/10 text-white border-2 border-white/20 hover:bg-white/20 font-bold rounded-2xl transition-colors shadow-sm">
                    Kembali
                </a>
            </div>
        </nav>

        <!-- Playful Clickable Stepper (Navigasi Tahapan - Layar Penuh Kiri-Kanan) -->
        <div class="w-full bg-white shadow-sm border-b-2 border-green-100 mb-8 overflow-x-auto pb-2 pt-4 custom-scrollbar">
            <div class="flex items-center justify-between px-4 md:px-8 min-w-max w-full">
                <template v-for="(section, index) in questionnaire?.sections" :key="section.id">
                    
                    <!-- Lingkaran Tahapan -->
                    <div 
                        class="flex flex-col relative items-center justify-center cursor-pointer group px-2 md:px-4 py-2 transition-all duration-300"
                        @click="setSection(index)"
                    >
                        <div class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-full font-black text-sm md:text-lg transition-all duration-300 z-10 border-4 shadow-sm"
                            :class="[
                                // Jika ini tahap yang sedang dikerjakan: Warnai Emas
                                activeSectionIndex === index ? 'bg-[#FFD700] text-[#005B3C] border-white shadow-[0_4px_0_0_#D4AF37]' : 
                                // Jika tahap ini sudah terlewat (selesai): Warnai Hijau dan muncul tanda centang (v)
                                (index < activeSectionIndex ? 'bg-[#005B3C] text-white border-green-200 shadow-[0_4px_0_0_#00422c]' : 
                                // Jika belum sampai: Warna Abu-abu
                                'bg-gray-100 text-gray-400 border-gray-200 shadow-[0_4px_0_0_#e2e8f0]')
                            ]"
                        >
                            <!-- Jika tahap sudah selesai (index < aktif), tampilkan SVG tanda centang (V) -->
                            <svg v-if="index < activeSectionIndex" class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            <!-- Jika belum, tampilkan nomor tahapannya -->
                            <span v-else>{{ index + 1 }}</span>
                        </div>
                        
                        <!-- Teks Judul Tahapan di bawah Lingkaran -->
                        <span class="text-[10px] md:text-xs font-black mt-3 text-center w-24 md:w-28 leading-tight transition-colors"
                            :class="activeSectionIndex === index ? 'text-[#005B3C]' : 'text-gray-400 group-hover:text-gray-600'"
                        >
                            {{ section.title }}
                        </span>
                    </div>
                    
                    <!-- Garis Penghubung antar Lingkaran (Garisnya panjang karena flex-1) -->
                    <div v-if="index < questionnaire?.sections.length - 1" class="flex-1 h-1 md:h-2 rounded-full transition-colors duration-500 mx-1 md:mx-2 shadow-inner" :class="index < activeSectionIndex ? 'bg-[#005B3C]' : 'bg-gray-200'"></div>
                </template>
            </div>
        </div>

        <!-- Main Form Area (Kotak Form Utama) -->
        <main class="flex-1 p-4 w-full max-w-4xl mx-auto">
            
            <!-- Jika terjadi error saat pengiriman form -->
            <div v-if="error" class="bg-red-50 text-red-700 p-6 border-2 border-red-200 rounded-3xl font-bold mb-6 shadow-sm">
                {{ error }}
            </div>

            <div v-else-if="questionnaire && currentSection">
                
                <!-- Section Header Card (Banner Hijau Besar) -->
                <div class="mb-8 text-center bg-[#005B3C] p-8 rounded-[2rem] shadow-[0_8px_0_0_#00422c] border-4 border-white text-white transform transition-transform hover:scale-[1.01]">
                    <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ currentSection.title }}</h1>
                    <p class="text-green-100 font-medium text-lg">Pilih jawaban yang paling sesuai. Kolom <span class="text-[#FFD700] font-black">*</span> wajib diisi.</p>
                </div>

                <form @submit.prevent="nextSection" class="space-y-8">
                    
                    <div class="space-y-8">
                        <template v-for="(q, idx) in currentSection.questions" :key="q.id">
                            
                            <!-- Question Card (Kotak Pertanyaan per item) -->
                            <!-- Diberi class 'q-card' agar dibaca oleh fungsi animasi GSAP -->
                            <div v-if="!hiddenQuestions.has(q.id)" class="q-card bg-white p-6 md:p-8 rounded-[2rem] shadow-[0_6px_0_0_#e2e8f0] border-2 border-gray-200 relative z-0" :style="{ zIndex: q.type === 'searchable_select' ? 10 : 1 }">
                                
                                <!-- Question Text (Teks Pertanyaan dan Kodenya) -->
                                <h2 class="text-xl md:text-2xl font-black text-gray-800 mb-6 leading-relaxed flex items-start gap-4">
                                    <span class="shrink-0 bg-[#FFD700] text-[#005B3C] px-3 py-1 rounded-xl text-base shadow-sm border-2 border-[#D4AF37]">{{ q.code }}</span>
                                    <span>{{ q.question_text }} <span v-if="q.is_required" class="text-red-500 font-black">*</span></span>
                                </h2>

                                <!-- ================== TIPE TEXT BIASA ================== -->
                                <div v-if="q.type === 'text'">
                                    <input 
                                        type="text" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required && !hiddenQuestions.has(q.id)"
                                        class="w-full rounded-2xl border-4 border-gray-100 bg-gray-50 p-5 text-gray-900 font-bold focus:bg-white focus:border-[#005B3C] focus:ring-0 transition-colors text-lg shadow-inner"
                                        placeholder="Ketik jawabanmu di sini..."
                                    >
                                </div>

                                <!-- ================== TIPE ANGKA ================== -->
                                <div v-else-if="q.type === 'number'">
                                    <input 
                                        type="number" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required && !hiddenQuestions.has(q.id)"
                                        @keydown="filterNumberInput"
                                        min="0"
                                        class="w-full max-w-xs rounded-2xl border-4 border-gray-100 bg-gray-50 p-5 text-gray-900 font-black font-mono focus:bg-white focus:border-[#005B3C] focus:ring-0 transition-colors text-xl shadow-inner text-center"
                                        placeholder="0"
                                    >
                                </div>

                                <!-- ================== TIPE PENCARIAN (Select) ================== -->
                                <div v-else-if="q.type === 'searchable_select'" class="relative">
                                    <SearchableSelect 
                                        v-model="form.answers[q.id]" 
                                        :options="q.options" 
                                        :required="q.is_required && !hiddenQuestions.has(q.id)" 
                                        placeholder="Cari bidang pekerjaan..." 
                                    />
                                </div>

                                <!-- ================== TIPE RADIO (Gaya Quizizz) ================== -->
                                <div v-else-if="q.type === 'radio'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group">
                                        <!-- Radio asli disembunyikan pakai sr-only, tapi statusnya dipantau oleh class peer -->
                                        <input 
                                            type="radio" 
                                            :name="'question_'+q.id"
                                            :value="opt.option_text"
                                            v-model="form.answers[q.id]"
                                            :required="q.is_required && !hiddenQuestions.has(q.id)"
                                            class="peer sr-only"
                                        >
                                        <!-- Ini desain Radio palsunya (Kotak besar). Jika Radio asli tercentang (peer-checked), background dan shadow berubah -->
                                        <div class="h-full p-5 rounded-2xl border-4 border-gray-100 bg-white font-bold text-gray-600 text-lg shadow-sm transition-all duration-200 peer-checked:bg-[#005B3C] peer-checked:text-white peer-checked:border-[#00422c] peer-checked:shadow-[0_6px_0_0_#00422c] hover:border-[#005B3C]/50 hover:bg-green-50 peer-checked:hover:bg-[#005B3C] peer-checked:-translate-y-1 flex items-center justify-center text-center">
                                            {{ opt.option_text }}
                                        </div>
                                    </label>
                                </div>

                                <!-- ================== TIPE CHECKBOX (Gaya Quizizz Warna Emas) ================== -->
                                <div v-else-if="q.type === 'checkbox'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group">
                                        <input 
                                            type="checkbox"
                                            :value="opt.option_text"
                                            :checked="form.answers[q.id].includes(opt.option_text)"
                                            @change="toggleCheckbox(q.id, opt.option_text)"
                                            class="peer sr-only"
                                        >
                                        <div class="h-full p-5 rounded-2xl border-4 border-gray-100 bg-white font-bold text-gray-600 text-lg shadow-sm transition-all duration-200 peer-checked:bg-[#FFD700] peer-checked:text-[#005B3C] peer-checked:border-[#D4AF37] peer-checked:shadow-[0_6px_0_0_#D4AF37] hover:border-[#FFD700]/50 hover:bg-yellow-50 peer-checked:hover:bg-[#FFD700] peer-checked:-translate-y-1 flex items-center justify-center text-center">
                                            {{ opt.option_text }}
                                        </div>
                                    </label>
                                </div>

                                <!-- ================== TIPE RADIO DENGAN INPUT TEKS ================== -->
                                <div v-else-if="q.type === 'radio_input' || q.type === 'radio_text'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group block">
                                        <input 
                                            type="radio" 
                                            :name="'question_'+q.id"
                                            :value="opt.option_text"
                                            v-model="form.answers[q.id].selected"
                                            class="peer sr-only"
                                        >
                                        <div class="p-5 rounded-2xl border-4 border-gray-100 bg-white font-bold text-gray-600 text-lg shadow-sm transition-all duration-200 peer-checked:bg-[#005B3C] peer-checked:text-white peer-checked:border-[#00422c] peer-checked:shadow-[0_6px_0_0_#00422c] hover:border-[#005B3C]/50 hover:bg-green-50 peer-checked:hover:bg-[#005B3C] flex flex-col items-center justify-center text-center">
                                            <span>{{ opt.option_text }}</span>
                                            
                                            <!-- Munculkan kotak input Teks jika opsi ini yang dipilih dan opsi mengandung kata "Lainnya" dsb. -->
                                            <div v-if="form.answers[q.id].selected === opt.option_text && (opt.option_text.includes('...') || opt.option_text.includes('Lainnya') || q.type === 'radio_text')" class="w-full mt-4" @click.stop>
                                                <input 
                                                    :type="q.type === 'radio_input' ? 'number' : 'text'"
                                                    v-model="form.answers[q.id].input"
                                                    :placeholder="q.type === 'radio_input' ? 'Masukkan angka...' : 'Tuliskan di sini...'"
                                                    @keydown="q.type === 'radio_input' ? filterNumberInput($event) : null"
                                                    class="w-full rounded-xl border-2 border-white/50 bg-white/10 text-white placeholder-white/70 p-3 font-bold focus:bg-white focus:text-[#005B3C] transition-colors text-center"
                                                    required
                                                >
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- ================== TIPE INPUT ANGKA MAJEMUK (Gaji, Lembur, Lainnya) ================== -->
                                <div v-else-if="q.type === 'multiple_number'" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="bg-[#E8F5E9] p-5 rounded-2xl border-4 border-green-100 shadow-sm text-center">
                                            <label class="block text-xs font-black text-[#005B3C] mb-3 uppercase tracking-widest">Utama</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#005B3C] font-black">Rp</span>
                                                <input type="number" v-model="form.answers[q.id]['utama']" min="0" @keydown="filterNumberInput" class="w-full rounded-xl border-2 border-green-200 bg-white p-3 pl-10 focus:border-[#005B3C] font-mono font-black text-gray-900" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="bg-[#FFF8E1] p-5 rounded-2xl border-4 border-yellow-100 shadow-sm text-center">
                                            <label class="block text-xs font-black text-yellow-700 mb-3 uppercase tracking-widest">Lembur</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-yellow-700 font-black">Rp</span>
                                                <input type="number" v-model="form.answers[q.id]['lembur']" min="0" @keydown="filterNumberInput" class="w-full rounded-xl border-2 border-yellow-200 bg-white p-3 pl-10 focus:border-[#FFD700] font-mono font-black text-gray-900" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="bg-gray-100 p-5 rounded-2xl border-4 border-gray-200 shadow-sm text-center">
                                            <label class="block text-xs font-black text-gray-600 mb-3 uppercase tracking-widest">Lainnya</label>
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-600 font-black">Rp</span>
                                                <input type="number" v-model="form.answers[q.id]['lainnya']" min="0" @keydown="filterNumberInput" class="w-full rounded-xl border-2 border-gray-300 bg-white p-3 pl-10 focus:border-gray-500 font-mono font-black text-gray-900" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ================== TIPE MATRIX BIASA (Penilaian Skala 1-5) ================== -->
                                <div v-else-if="q.type === 'matrix'" class="overflow-x-auto">
                                    <div class="min-w-max space-y-3">
                                        <!-- Header Skala -->
                                        <div class="flex px-4 py-2 bg-gray-100 rounded-xl font-black text-gray-500 text-sm">
                                            <div class="w-1/2 uppercase">Aspek</div>
                                            <div class="flex-1 flex justify-between px-4">
                                                <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Baris Pertanyaan Matrix -->
                                        <div v-for="(opt, idx) in q.options" :key="opt.id" class="flex items-center px-4 py-3 bg-white border-4 border-gray-100 rounded-2xl hover:border-[#005B3C]/30 transition-colors">
                                            <div class="w-1/2 font-bold text-gray-800 pr-4">{{ opt.option_text }}</div>
                                            <div class="flex-1 flex justify-between px-4">
                                                <!-- Opsi Nilai 1 s/d 5 (Didesain ulang seperti tombol tekan bundar) -->
                                                <label v-for="i in 5" :key="i" class="cursor-pointer relative">
                                                    <input 
                                                        type="radio" 
                                                        :name="'q_'+q.id+'_opt_'+opt.id"
                                                        :value="i"
                                                        v-model="form.answers[q.id][opt.id]"
                                                        :required="q.is_required"
                                                        class="peer sr-only"
                                                    >
                                                    <div class="w-10 h-10 rounded-full border-4 border-gray-200 flex items-center justify-center font-black text-gray-400 peer-checked:border-[#005B3C] peer-checked:bg-[#005B3C] peer-checked:text-white transition-all transform peer-checked:scale-110 shadow-sm">
                                                        {{ i }}
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ================== TIPE MATRIX DUAL (Kompetensi Saat Lulus & Tuntutan Pekerjaan) ================== -->
                                <div v-else-if="q.type === 'matrix_dual'" class="overflow-x-auto">
                                    <div class="min-w-max border-4 border-gray-100 rounded-3xl overflow-hidden shadow-sm">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="w-1/3 px-4 py-4 text-left font-black text-gray-600 uppercase bg-gray-100 border-r-4 border-white">Kompetensi</th>
                                                    <th colspan="5" class="px-2 py-3 text-center font-black text-white bg-[#005B3C] uppercase border-r-4 border-white">(A) Saat Lulus</th>
                                                    <th colspan="5" class="px-2 py-3 text-center font-black text-[#005B3C] bg-[#FFD700] uppercase">(B) Kontribusi PT</th>
                                                </tr>
                                                <tr>
                                                    <!-- Bagian Skala A (Hijau) -->
                                                    <th v-for="i in 5" :key="'a'+i" class="bg-green-100 py-2 text-[#005B3C] w-10 text-center font-black">{{ i }}</th>
                                                    <!-- Bagian Skala B (Kuning) -->
                                                    <th v-for="i in 5" :key="'b'+i" class="bg-yellow-100 py-2 text-yellow-800 w-10 text-center font-black">{{ i }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white">
                                                <tr v-for="(opt, idx) in q.options" :key="opt.id" class="border-t-4 border-gray-100 hover:bg-gray-50">
                                                    <td class="px-4 py-4 font-bold text-gray-800 border-r-4 border-gray-100">{{ opt.option_text }}</td>
                                                    
                                                    <!-- Pilihan Radio (Kolom A) -->
                                                    <td v-for="i in 5" :key="'a_opt'+i" class="text-center p-2 border-r border-gray-50">
                                                        <label class="cursor-pointer block">
                                                            <input type="radio" :name="'q_'+q.id+'_opt_'+opt.id+'_A'" :value="i" v-model="form.answers[q.id][opt.id].A" :required="q.is_required" class="peer sr-only">
                                                            <!-- Animasi Bulatan: Transparan saat belum ditekan, menjadi Hijau saat dicentang -->
                                                            <div class="mx-auto w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center peer-checked:bg-[#005B3C] peer-checked:border-[#005B3C] peer-checked:text-white font-bold text-transparent transition-all">✓</div>
                                                        </label>
                                                    </td>
                                                    
                                                    <!-- Pilihan Radio (Kolom B) -->
                                                    <td v-for="i in 5" :key="'b_opt'+i" class="text-center p-2 border-l border-gray-50 bg-yellow-50/30">
                                                        <label class="cursor-pointer block">
                                                            <input type="radio" :name="'q_'+q.id+'_opt_'+opt.id+'_B'" :value="i" v-model="form.answers[q.id][opt.id].B" :required="q.is_required" class="peer sr-only">
                                                            <!-- Animasi Bulatan: Transparan, menjadi Kuning Emas saat dicentang -->
                                                            <div class="mx-auto w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center peer-checked:bg-[#FFD700] peer-checked:border-[#D4AF37] peer-checked:text-[#005B3C] font-bold text-transparent transition-all">✓</div>
                                                        </label>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>
                    
                    <!-- Floating Navigations (Bottom Left and Right) -->
                    <!-- Kelas penting: 
                         - fixed, bottom-8: Membuat tombol melayang di sudut layar bawah
                         - shadow-[0_6px...]: Bayangan tebal 3D yang terlihat seperti pinggiran tombol mesin dingdong (arcade)
                         - active:translate-y-1.5: Saat tombol diklik, posisinya bergeser ke bawah menutupi bayangan (Efek dipencet) 
                    -->
                    
                    <!-- Tombol Kembali (Kiri Bawah) - Warna Kuning Emas -->
                    <button 
                        type="button"
                        @click="prevSection"
                        :disabled="activeSectionIndex === 0"
                        class="fixed left-4 md:left-8 bottom-8 z-40 w-16 h-16 rounded-full flex justify-center items-center bg-[#FFD700] border-4 border-[#D4AF37] shadow-[0_6px_0_0_#B8860B] text-[#005B3C] hover:-translate-y-1 hover:shadow-[0_8px_0_0_#B8860B] active:translate-y-1.5 active:shadow-none transition-all disabled:opacity-0 disabled:pointer-events-none group"
                        title="Kembali"
                    >
                        <svg class="w-8 h-8 pr-1 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    
                    <!-- Tombol Lanjutkan/Simpan (Kanan Bawah) - Warna Hijau Tua -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="fixed right-4 md:right-8 bottom-8 z-40 w-16 h-16 rounded-full flex justify-center items-center bg-[#005B3C] border-4 border-[#00422c] shadow-[0_6px_0_0_#002b1c] text-white hover:-translate-y-1 hover:shadow-[0_8px_0_0_#002b1c] active:translate-y-1.5 active:shadow-none transition-all disabled:opacity-50 disabled:cursor-not-allowed group"
                        :title="activeSectionIndex === questionnaire.sections.length - 1 ? 'Selesai' : 'Lanjutkan'"
                    >
                        <!-- Jika BUKAN halaman terakhir, tampilkan panah ke Kanan (>) -->
                        <svg v-if="activeSectionIndex !== questionnaire.sections.length - 1" class="w-8 h-8 pl-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7"></path></svg>
                        <!-- Jika ini halaman terakhir, tampilkan tanda Centang (✓) -->
                        <svg v-else class="w-8 h-8 pl-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                    </button>

                </form>
            </div>

        </main>
    </div>
</template>

<style scoped>
/* Gamification Toast Bounce (Animasi Masuk dan Keluar untuk Pop-up Pujian) */
.bounce-enter-active {
  animation: bounce-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.bounce-leave-active {
  animation: bounce-out 0.3s;
}
@keyframes bounce-in {
  /* Skala dari kecil (0.5), posisi dari atas (-100%) menukik ke tengah (0) dan sedikit dimiringkan (rotate 3deg) */
  0% { transform: translate(-50%, -100%) scale(0.5); opacity: 0; }
  100% { transform: translate(-50%, 0) scale(1) rotate(3deg); opacity: 1; }
}
@keyframes bounce-out {
  /* Kebalikan dari bounce-in */
  0% { transform: translate(-50%, 0) scale(1) rotate(3deg); opacity: 1; }
  100% { transform: translate(-50%, -100%) scale(0.5); opacity: 0; }
}

/* Transisi Halus (Bezier Curve) jika butuh animasi lebar atau tinggi dengan sangat mulus (seperti karet) */
.ease-bounce {
    transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Menyembunyikan Scrollbar bawaan OS untuk area Stepper, agar terlihat bersih tapi tetap bisa digeser (swipe/scroll) */
.custom-scrollbar::-webkit-scrollbar {
    display: none;
}
.custom-scrollbar {
    -ms-overflow-style: none; /* Untuk IE dan Edge */
    scrollbar-width: none; /* Untuk Firefox */
}
</style>
