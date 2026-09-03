<!--
  Halaman Kuesioner Tracer Study
  Fungsi: Menampilkan seluruh pertanyaan kuesioner dari Backend (Controller).
  Aturan Ketat: File ini HANYA BERTUGAS SEBAGAI TAMPILAN (HTML murni).
  TIDAK ADA logika perhitungan, animasi kompleks, atau manipulasi array di sini.
  Semua proses penyimpanan form langsung dilempar ke Backend (SimpanJawabanController).
-->
<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import SearchableSelect from '@/components/form/searchable-select.vue';

// Menerima data kuesioner dari Backend yang sudah diproses
const props = defineProps({
    questionnaire: Object,
    initialAnswers: Object,
    error: String,
});

// Inisialisasi data form menggunakan useForm bawaan Inertia
const form = useForm({ answers: props.initialAnswers || {} });

// Mencegah karakter aneh pada input angka
const filterNumberInput = (event) => {
    if (['e', 'E', '+', '-', '.'].includes(event.key)) {
        event.preventDefault();
    }
}

// Fungsi submit sederhana
const simpanKuesioner = () => {
    form.post('/alumni/kuesioner', { preserveScroll: true });
};
</script>

<template>
    <Head title="Kuesioner Tracer Study" />

    <div class="min-h-screen bg-[#E8F5E9] font-sans text-gray-900 antialiased flex flex-col relative pb-20">
        
        <!-- Top Navigation -->
        <nav class="bg-[#005B3C] shadow-md sticky top-0 z-50 py-3">
            <div class="max-w-4xl mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-white p-1.5 rounded-xl shadow-sm">
                        <img src="/uploads/logo/logo-ukdw.png" onerror="this.src='https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png'" alt="UKDW Logo" class="h-10 w-10 object-contain">
                    </div>
                    <span class="text-white font-black text-2xl tracking-wide">Tracer Study</span>
                </div>
                <Link href="/alumni/dashboard" class="px-5 py-2.5 bg-white/10 text-white border-2 border-white/20 hover:bg-white/20 font-bold rounded-2xl transition-colors shadow-sm">
                    Kembali
                </Link>
            </div>
        </nav>

        <main class="flex-1 p-4 w-full max-w-4xl mx-auto mt-6">
            
            <div v-if="error" class="bg-red-50 text-red-700 p-6 border-2 border-red-200 rounded-3xl font-bold mb-6 shadow-sm">
                {{ error }}
            </div>

            <div v-else-if="questionnaire">
                
                <form @submit.prevent="simpanKuesioner" class="space-y-12">
                    
                    <!-- Looping setiap bagian kuesioner dari Controller -->
                    <div v-for="section in questionnaire.sections" :key="section.id" class="space-y-8">
                        
                        <!-- Header Bagian -->
                        <div class="text-center bg-[#005B3C] p-8 rounded-[2rem] shadow-sm border-4 border-white text-white">
                            <h1 class="text-3xl md:text-4xl font-black tracking-tight mb-2">{{ section.title }}</h1>
                            <p class="text-green-100 font-medium text-lg">Pilih jawaban yang paling sesuai.</p>
                        </div>
                        
                        <!-- Looping pertanyaan dalam bagian tersebut -->
                        <template v-for="q in section.questions" :key="q.id">
                            
                            <!-- Kartu Pertanyaan -->
                            <div class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border-2 border-gray-200">
                                
                                <h2 class="text-xl md:text-2xl font-black text-gray-800 mb-6 leading-relaxed flex items-start gap-4">
                                    <span class="shrink-0 bg-[#FFD700] text-[#005B3C] px-3 py-1 rounded-xl text-base shadow-sm border-2 border-[#D4AF37]">{{ q.code }}</span>
                                    <span>{{ q.question_text }} <span v-if="q.is_required" class="text-red-500 font-black">*</span></span>
                                </h2>

                                <!-- Tipe Text -->
                                <div v-if="q.type === 'text'">
                                    <input 
                                        type="text" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required"
                                        class="w-full rounded-2xl border-4 border-gray-100 bg-gray-50 p-5 text-gray-900 font-bold focus:bg-white focus:border-[#005B3C] focus:ring-0 transition-colors text-lg shadow-inner"
                                        placeholder="Ketik jawabanmu di sini..."
                                    >
                                </div>

                                <!-- Tipe Number -->
                                <div v-else-if="q.type === 'number'">
                                    <input 
                                        type="number" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required"
                                        @keydown="filterNumberInput"
                                        min="0"
                                        class="w-full max-w-xs rounded-2xl border-4 border-gray-100 bg-gray-50 p-5 text-gray-900 font-black font-mono focus:bg-white focus:border-[#005B3C] focus:ring-0 transition-colors text-xl shadow-inner text-center"
                                        placeholder="0"
                                    >
                                </div>

                                <!-- Tipe Searchable Select -->
                                <div v-else-if="q.type === 'searchable_select'" class="relative">
                                    <SearchableSelect 
                                        v-model="form.answers[q.id]" 
                                        :options="q.options" 
                                        :required="q.is_required" 
                                        placeholder="Cari bidang pekerjaan..." 
                                    />
                                </div>

                                <!-- Tipe Radio -->
                                <div v-else-if="q.type === 'radio'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group">
                                        <input 
                                            type="radio" 
                                            :name="'question_'+q.id"
                                            :value="opt.option_text"
                                            v-model="form.answers[q.id]"
                                            :required="q.is_required"
                                            class="peer sr-only"
                                        >
                                        <div class="h-full p-5 rounded-2xl border-4 border-gray-100 bg-white font-bold text-gray-600 text-lg shadow-sm transition-all duration-200 peer-checked:bg-[#005B3C] peer-checked:text-white peer-checked:border-[#00422c] peer-checked:shadow-[0_6px_0_0_#00422c] hover:border-[#005B3C]/50 hover:bg-green-50 peer-checked:hover:bg-[#005B3C] peer-checked:-translate-y-1 flex items-center justify-center text-center">
                                            {{ opt.option_text }}
                                        </div>
                                    </label>
                                </div>

                                <!-- Tipe Checkbox -->
                                <div v-else-if="q.type === 'checkbox'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer group">
                                        <input 
                                            type="checkbox"
                                            :value="opt.option_text"
                                            v-model="form.answers[q.id]"
                                            class="peer sr-only"
                                        >
                                        <div class="h-full p-5 rounded-2xl border-4 border-gray-100 bg-white font-bold text-gray-600 text-lg shadow-sm transition-all duration-200 peer-checked:bg-[#FFD700] peer-checked:text-[#005B3C] peer-checked:border-[#D4AF37] peer-checked:shadow-[0_6px_0_0_#D4AF37] hover:border-[#FFD700]/50 hover:bg-yellow-50 peer-checked:hover:bg-[#FFD700] peer-checked:-translate-y-1 flex items-center justify-center text-center">
                                            {{ opt.option_text }}
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </template>
                    </div>
                    
                    <!-- Tombol Simpan -->
                    <div class="text-center pt-8">
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-10 py-5 rounded-[2rem] bg-[#005B3C] border-4 border-[#00422c] shadow-[0_6px_0_0_#002b1c] text-white hover:-translate-y-1 active:translate-y-1 active:shadow-none transition-all disabled:opacity-50 font-black text-2xl inline-flex items-center gap-3"
                        >
                            <span v-if="form.processing">Menyimpan...</span>
                            <span v-else>Simpan Seluruh Kuesioner</span>
                        </button>
                    </div>

                </form>
            </div>

        </main>
    </div>
</template>
