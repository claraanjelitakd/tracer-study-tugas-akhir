<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import SearchableSelect from '@/Components/Form/SearchableSelect.vue';

const props = defineProps({
    questionnaire: Object,
    responses: Object,
    alumniData: Object,
    mappings: Object,
    error: String,
});

const activeSectionIndex = ref(0);

// Initialize form data
const initialFormState = { answers: {} };

if (props.questionnaire) {
    props.questionnaire.sections.forEach(section => {
        section.questions.forEach(q => {
            if (props.responses[q.id]) {
                if (q.type === 'checkbox' || q.type === 'matrix_dual' || q.type === 'matrix' || q.type === 'multiple_number') {
                    initialFormState.answers[q.id] = props.responses[q.id].answer_json || [];
                } else if (q.type === 'radio_input') {
                    initialFormState.answers[q.id] = props.responses[q.id].answer_json || { selected: '', input: '' };
                } else if (q.type === 'radio_text') {
                     initialFormState.answers[q.id] = props.responses[q.id].answer_json || { selected: '', input: '' };
                } else {
                    initialFormState.answers[q.id] = props.responses[q.id].answer_text || '';
                }
            } else {
                if (q.type === 'checkbox') {
                    initialFormState.answers[q.id] = [];
                } else if (q.type === 'matrix_dual') {
                    let obj = {};
                    q.options.forEach(o => { obj[o.id] = { A: null, B: null }; });
                    initialFormState.answers[q.id] = obj;
                } else if (q.type === 'matrix') {
                    let obj = {};
                    q.options.forEach(o => { obj[o.id] = null; });
                    initialFormState.answers[q.id] = obj;
                } else if (q.type === 'multiple_number') {
                    initialFormState.answers[q.id] = {}; // Format: { 'label': 'value' }
                } else if (q.type === 'radio_input' || q.type === 'radio_text') {
                    initialFormState.answers[q.id] = { selected: '', input: '' };
                } else {
                    initialFormState.answers[q.id] = '';
                    // Pre-fill Identitas dinamis dari database (QuestionMapping)
                    if (props.mappings && props.mappings[q.id]) {
                        const colName = props.mappings[q.id].column_name;
                        initialFormState.answers[q.id] = props.alumniData?.[colName] || '';
                    }
                }
            }
        });
    });
}

const form = useForm(initialFormState);

const currentSection = computed(() => {
    return props.questionnaire?.sections[activeSectionIndex.value];
});

// Jump Logic
const hiddenQuestions = ref(new Set());
const currentTargetSectionIndex = ref(null);

const evaluateJumpLogic = () => {
    let hideSet = new Set();
    currentTargetSectionIndex.value = null; // reset
    
    props.questionnaire?.sections.forEach((section, sIndex) => {
        section.questions.forEach((q, qIndex) => {
            if (q.jump_logic) {
                const rules = q.jump_logic;
                const answer = form.answers[q.id];
                
                let targetCodeToJump = null;
                
                if (typeof answer === 'string' && rules[answer]) {
                    targetCodeToJump = rules[answer];
                } else if (typeof answer === 'object' && answer.selected && rules[answer.selected]) {
                    targetCodeToJump = rules[answer.selected];
                }

                if (targetCodeToJump) {
                    let startIndex = -1;
                    let targetIndex = -1;
                    let allQ = [];
                    
                    props.questionnaire.sections.forEach(s => {
                        s.questions.forEach(sq => {
                            allQ.push({ q: sq, sectionIdx: s.order - 1 });
                        });
                    });

                    allQ.forEach((sq, i) => {
                        if (sq.q.code === q.code) startIndex = i;
                        if (sq.q.code === targetCodeToJump) {
                            targetIndex = i;
                            // Set target section
                            if (activeSectionIndex.value === section.order - 1) {
                                currentTargetSectionIndex.value = sq.sectionIdx;
                            }
                        }
                    });

                    if (startIndex !== -1 && targetIndex !== -1 && targetIndex > startIndex) {
                        for (let i = startIndex + 1; i < targetIndex; i++) {
                            hideSet.add(allQ[i].q.id);
                        }
                    }
                }
            }
        });
    });
    
    hiddenQuestions.value = hideSet;
};

watch(form.answers, () => {
    evaluateJumpLogic();
}, { deep: true });

onMounted(() => {
    evaluateJumpLogic();
});

const nextSection = () => {
    form.post('/alumni/kuesioner', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            if (currentTargetSectionIndex.value !== null) {
                // If there's a jump target in another section, go there
                activeSectionIndex.value = currentTargetSectionIndex.value;
            } else if (activeSectionIndex.value < props.questionnaire.sections.length - 1) {
                activeSectionIndex.value++;
            }
            window.scrollTo(0,0);
        }
    });
};

const prevSection = () => {
    if (activeSectionIndex.value > 0) {
        activeSectionIndex.value--;
        window.scrollTo(0,0);
    }
};

const setSection = (index) => {
    activeSectionIndex.value = index;
    window.scrollTo(0,0);
};

// Toggle for checkbox
const toggleCheckbox = (qId, val) => {
    const arr = form.answers[qId];
    if (arr.includes(val)) {
        form.answers[qId] = arr.filter(item => item !== val);
    } else {
        form.answers[qId].push(val);
    }
};

const filterNumberInput = (event) => {
    // Only allow numbers
    if (['e', 'E', '+', '-', '.'].includes(event.key)) {
        event.preventDefault();
    }
}
</script>

<template>
    <Head title="Kuesioner Tracer Study" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased flex flex-col">
        
        <!-- Navbar -->
        <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center space-x-4">
                        <!-- UKDW Logo -->
                        <img src="https://www.ukdw.ac.id/wp-content/uploads/2017/10/logo-ukdw.png" alt="UKDW Logo" class="h-12 object-contain">
                        <div class="flex-shrink-0 flex items-center border-l-2 border-gray-200 pl-4 ml-4">
                            <span class="text-[#005B3C] font-bold text-xl tracking-wide uppercase">Tracer Study</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/alumni/dashboard" class="px-6 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-sm hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Step Progress Bar (Clean Corporate Style) -->
        <div class="bg-white border-b border-gray-200 sticky top-20 z-40 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
            <div class="max-w-5xl mx-auto px-4 py-4 flex flex-col">
                <div class="flex justify-between text-sm font-bold text-[#005B3C] mb-3 uppercase tracking-wide">
                    <span>Tahap {{ activeSectionIndex + 1 }} dari {{ questionnaire?.sections.length }}</span>
                    <span>{{ Math.round(((activeSectionIndex + 1) / questionnaire?.sections.length) * 100) }}% Selesai</span>
                </div>
                <div class="flex space-x-1">
                    <button 
                        v-for="(section, index) in questionnaire?.sections" 
                        :key="section.id"
                        @click="setSection(index)"
                        class="flex-1 h-1.5 transition-colors group relative"
                        :class="activeSectionIndex >= index ? 'bg-[#005B3C] cursor-pointer' : 'bg-gray-200'"
                    >
                        <div class="opacity-0 group-hover:opacity-100 absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-3 py-1.5 rounded-sm font-medium whitespace-nowrap pointer-events-none shadow-sm">
                            {{ section.title }}
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 p-4 md:p-8 w-full max-w-5xl mx-auto mt-2 pb-48">
            
            <div v-if="error" class="bg-red-50 text-red-700 p-4 border border-red-200 rounded-sm font-medium mb-6">
                {{ error }}
            </div>

            <div v-else-if="questionnaire && currentSection">
                
                <div class="mb-8 bg-white p-8 md:p-10 border border-gray-200 border-l-4 border-l-[#005B3C] shadow-sm rounded-sm">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">{{ currentSection.title }}</h1>
                    <p class="text-gray-600 mt-3 font-medium text-sm">Mohon isi pertanyaan di bawah ini dengan sebenar-benarnya. Kolom bertanda bintang <span class="text-red-500 font-bold">*</span> wajib diisi.</p>
                </div>

                <form @submit.prevent="nextSection" class="space-y-6">
                    
                    <TransitionGroup name="fade-slide" tag="div" class="space-y-6">
                        <template v-for="q in currentSection.questions" :key="q.id">
                            
                            <div v-if="!hiddenQuestions.has(q.id)" class="bg-white p-6 md:p-8 border border-gray-200 shadow-sm rounded-sm relative z-0" :style="{ zIndex: q.type === 'searchable_select' ? 10 : 1 }">
                                
                                <label class="block text-lg font-bold text-gray-900 mb-6">
                                    <span class="inline-flex items-center justify-center bg-[#005B3C] text-white px-3 py-1 rounded-sm mr-3 text-sm font-bold">{{ q.code }}</span>
                                    <span class="align-middle">{{ q.question_text }}</span>
                                    <span v-if="q.is_required" class="text-red-500 ml-1 font-bold">*</span>
                                </label>

                                <!-- TextInput -->
                                <div v-if="q.type === 'text'">
                                    <input 
                                        type="text" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required && !hiddenQuestions.has(q.id)"
                                        class="w-full rounded-sm border border-gray-300 bg-white p-3.5 text-gray-900 focus:border-[#005B3C] focus:ring-1 focus:ring-[#005B3C] transition-colors"
                                        placeholder="Ketik jawaban Anda..."
                                    >
                                </div>

                                <!-- Number -->
                                <div v-else-if="q.type === 'number'">
                                    <input 
                                        type="number" 
                                        v-model="form.answers[q.id]" 
                                        :required="q.is_required && !hiddenQuestions.has(q.id)"
                                        @keydown="filterNumberInput"
                                        min="0"
                                        class="w-full max-w-sm rounded-sm border border-gray-300 bg-white p-3.5 text-gray-900 focus:border-[#005B3C] focus:ring-1 focus:ring-[#005B3C] transition-colors"
                                        placeholder="0"
                                    >
                                </div>

                                <!-- Searchable Select (Dropdown) -->
                                <div v-else-if="q.type === 'searchable_select'" class="relative">
                                    <SearchableSelect 
                                        v-model="form.answers[q.id]" 
                                        :options="q.options" 
                                        :required="q.is_required && !hiddenQuestions.has(q.id)" 
                                        placeholder="Ketik untuk mencari bidang pekerjaan..." 
                                    />
                                </div>

                                <!-- Custom Professional Radio (Block Layout) -->
                                <div v-else-if="q.type === 'radio'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer relative flex items-center p-4 border border-gray-300 rounded-sm hover:bg-gray-50 transition-colors">
                                        <input 
                                            type="radio" 
                                            :name="'question_'+q.id"
                                            :value="opt.option_text"
                                            v-model="form.answers[q.id]"
                                            :required="q.is_required && !hiddenQuestions.has(q.id)"
                                            class="w-4 h-4 text-[#005B3C] border-gray-300 focus:ring-[#005B3C]"
                                        >
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ opt.option_text }}</span>
                                    </label>
                                </div>

                                <!-- Custom Professional Checkbox -->
                                <div v-else-if="q.type === 'checkbox'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer relative flex items-center p-4 border border-gray-300 rounded-sm hover:bg-gray-50 transition-colors">
                                        <input 
                                            type="checkbox"
                                            :value="opt.option_text"
                                            :checked="form.answers[q.id].includes(opt.option_text)"
                                            @change="toggleCheckbox(q.id, opt.option_text)"
                                            class="w-4 h-4 text-[#005B3C] border-gray-300 rounded-sm focus:ring-[#005B3C]"
                                        >
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ opt.option_text }}</span>
                                    </label>
                                </div>

                                <!-- Radio + Input -->
                                <div v-else-if="q.type === 'radio_input' || q.type === 'radio_text'" class="space-y-3">
                                    <label v-for="opt in q.options" :key="opt.id" class="cursor-pointer block border border-gray-300 rounded-sm transition-colors" :class="form.answers[q.id].selected === opt.option_text ? 'bg-gray-50 border-[#005B3C]' : 'hover:bg-gray-50'">
                                        <div class="flex items-center p-4">
                                            <input 
                                                type="radio" 
                                                :name="'question_'+q.id"
                                                :value="opt.option_text"
                                                v-model="form.answers[q.id].selected"
                                                class="w-4 h-4 text-[#005B3C] border-gray-300 focus:ring-[#005B3C]"
                                            >
                                            <span class="ml-3 text-sm font-medium text-gray-700">{{ opt.option_text }}</span>
                                        </div>
                                        <div v-if="form.answers[q.id].selected === opt.option_text && (opt.option_text.includes('...') || opt.option_text.includes('Lainnya') || q.type === 'radio_text')" class="px-4 pb-4 pl-11">
                                            <input 
                                                :type="q.type === 'radio_input' ? 'number' : 'text'"
                                                v-model="form.answers[q.id].input"
                                                :placeholder="q.type === 'radio_input' ? 'Masukkan angka' : 'Ketik detail di sini...'"
                                                @keydown="q.type === 'radio_input' ? filterNumberInput($event) : null"
                                                class="w-full max-w-md rounded-sm border border-gray-300 bg-white p-2.5 text-sm focus:border-[#005B3C] focus:ring-1 focus:ring-[#005B3C]"
                                                required
                                            >
                                        </div>
                                    </label>
                                </div>

                                <!-- Multiple Number -->
                                <div v-else-if="q.type === 'multiple_number'" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="border-b border-gray-200 pb-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Dari Pekerjaan Utama</label>
                                            <div class="relative">
                                                <span class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                                <input type="number" v-model="form.answers[q.id]['utama']" min="0" @keydown="filterNumberInput" class="w-full border-0 border-b-2 border-gray-300 bg-transparent pl-8 focus:ring-0 focus:border-[#005B3C] font-mono text-gray-900" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="border-b border-gray-200 pb-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Dari Lembur dan Tips</label>
                                            <div class="relative">
                                                <span class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                                <input type="number" v-model="form.answers[q.id]['lembur']" min="0" @keydown="filterNumberInput" class="w-full border-0 border-b-2 border-gray-300 bg-transparent pl-8 focus:ring-0 focus:border-[#005B3C] font-mono text-gray-900" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="border-b border-gray-200 pb-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-2 uppercase tracking-wide">Dari Pekerjaan Lainnya</label>
                                            <div class="relative">
                                                <span class="absolute left-0 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                                <input type="number" v-model="form.answers[q.id]['lainnya']" min="0" @keydown="filterNumberInput" class="w-full border-0 border-b-2 border-gray-300 bg-transparent pl-8 focus:ring-0 focus:border-[#005B3C] font-mono text-gray-900" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Single Matrix -->
                                <div v-else-if="q.type === 'matrix'" class="overflow-x-auto border border-gray-200 rounded-sm">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="w-1/2 px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Aspek Penilaian</th>
                                                <th v-for="i in 5" :key="i" scope="col" class="px-2 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-20">{{ i }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(opt, idx) in q.options" :key="opt.id" class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 text-sm text-gray-900">{{ opt.option_text }}</td>
                                                <td v-for="i in 5" :key="i" class="px-2 py-4 text-center whitespace-nowrap">
                                                    <input 
                                                        type="radio" 
                                                        :name="'q_'+q.id+'_opt_'+opt.id"
                                                        :value="i"
                                                        v-model="form.answers[q.id][opt.id]"
                                                        :required="q.is_required"
                                                        class="h-4 w-4 text-[#005B3C] border-gray-300 focus:ring-[#005B3C] cursor-pointer"
                                                    >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Dual Matrix -->
                                <div v-else-if="q.type === 'matrix_dual'" class="overflow-x-auto border border-gray-200 rounded-sm">
                                    <table class="min-w-full divide-y divide-gray-200 table-fixed text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th rowspan="2" class="w-1/3 px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-white sticky left-0 z-10 border-r border-gray-200">Kompetensi</th>
                                                <th colspan="5" class="px-2 py-3 text-center text-xs font-bold text-white bg-[#005B3C] uppercase tracking-wider border-r border-[#005B3C]">(A) Penguasaan Saat Lulus</th>
                                                <th colspan="5" class="px-2 py-3 text-center text-xs font-bold text-gray-900 bg-[#FFD700] uppercase tracking-wider">(B) Kontribusi PT</th>
                                            </tr>
                                            <tr class="divide-x divide-gray-200">
                                                <!-- A (1-5) -->
                                                <th v-for="i in 5" :key="'a'+i" class="bg-green-50 py-2 text-[#005B3C] w-10 text-center font-bold">{{ i }}</th>
                                                <!-- B (1-5) -->
                                                <th v-for="i in 5" :key="'b'+i" class="bg-yellow-50 py-2 text-gray-900 w-10 text-center font-bold border-l border-gray-200">{{ i }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(opt, idx) in q.options" :key="opt.id" class="hover:bg-gray-50 divide-x divide-gray-200">
                                                <td class="px-4 py-3 text-gray-900 bg-white sticky left-0 z-10 border-r border-gray-200">{{ opt.option_text }}</td>
                                                <!-- Radio A -->
                                                <td v-for="i in 5" :key="'a_opt'+i" class="text-center hover:bg-gray-100 transition-colors">
                                                    <input 
                                                        type="radio" 
                                                        :name="'q_'+q.id+'_opt_'+opt.id+'_A'"
                                                        :value="i"
                                                        v-model="form.answers[q.id][opt.id].A"
                                                        :required="q.is_required"
                                                        class="h-4 w-4 text-[#005B3C] border-gray-300 focus:ring-[#005B3C] cursor-pointer"
                                                    >
                                                </td>
                                                <!-- Radio B -->
                                                <td v-for="i in 5" :key="'b_opt'+i" class="text-center hover:bg-gray-100 transition-colors">
                                                    <input 
                                                        type="radio" 
                                                        :name="'q_'+q.id+'_opt_'+opt.id+'_B'"
                                                        :value="i"
                                                        v-model="form.answers[q.id][opt.id].B"
                                                        :required="q.is_required"
                                                        class="h-4 w-4 text-[#FFD700] border-gray-300 focus:ring-[#FFD700] cursor-pointer"
                                                    >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </template>
                    </TransitionGroup>
                    
                    <!-- Form Actions / Footer -->
                    <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 p-4 px-6 flex justify-between items-center shadow-[0_-4px_10px_rgba(0,0,0,0.05)] z-30">
                        <div class="max-w-5xl mx-auto w-full flex justify-between items-center">
                            <button 
                                type="button"
                                @click="prevSection"
                                :disabled="activeSectionIndex === 0"
                                class="px-6 py-2 border border-gray-300 text-sm font-medium rounded-sm text-gray-700 bg-white hover:bg-gray-50 transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-sm"
                            >
                                &larr; Kembali
                            </button>
                            
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center items-center py-2 px-8 border border-transparent text-sm font-medium rounded-sm text-white bg-[#005B3C] hover:bg-green-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#005B3C]"
                            >
                                {{ activeSectionIndex === questionnaire.sections.length - 1 ? 'Selesai & Simpan' : 'Simpan & Lanjut' }}
                                <svg v-if="activeSectionIndex !== questionnaire.sections.length - 1" class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </main>
    </div>
</template>

<style scoped>
/* Vue Transitions for Jumping Logic */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(0, 0, 0.2, 1);
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
