<!--
  Komponen: Stepper Kuesioner Tracer Study Alumni
  File: resources/js/Pages/Alumni/Components/Kuesioner/Stepper.vue
  Fungsi: Menampilkan navigasi tahapan section kuesioner dengan bulatan angka 1..N, indikator centang selesai, dan auto-scroll.
-->
<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    sections: {
        type: Array,
        default: () => [],
    },
    activeIndex: {
        type: Number,
        default: 0,
    },
    completedIndices: {
        type: Object, // Set
        default: () => new Set(),
    },
    isSectionCompleted: {
        type: Function,
        required: true,
    },
    isLineCompleted: {
        type: Function,
        required: true,
    },
});

const emit = defineEmits(['select-section']);

const stepperContainerRef = ref(null);
const stepRefs = ref({});

const setStepRef = (el, index) => {
    if (el) stepRefs.value[index] = el;
};

const scrollActiveStepIntoView = (index) => {
    if (typeof window === 'undefined') return;
    setTimeout(() => {
        const el = stepRefs.value[index];
        if (el && stepperContainerRef.value) {
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }, 50);
};

watch(() => props.activeIndex, (newIdx) => {
    scrollActiveStepIntoView(newIdx);
});

onMounted(() => {
    scrollActiveStepIntoView(props.activeIndex);
});
</script>

<template>
    <div 
        v-if="sections && sections.length > 0" 
        ref="stepperContainerRef"
        class="w-full bg-white/95 backdrop-blur-md border-b border-gray-200 overflow-x-auto py-2 sm:py-3 custom-scrollbar"
    >
        <div class="flex items-center justify-between px-3 sm:px-4 md:px-8 min-w-max max-w-5xl mx-auto">
            <template v-for="(section, index) in sections" :key="section.id">
                
                <!-- Lingkaran Tahapan Stepper -->
                <div 
                    :ref="el => setStepRef(el, index)"
                    class="flex flex-col relative items-center justify-center cursor-pointer group px-1 sm:px-2 md:px-3 py-0.5 sm:py-1 transition-all duration-200"
                    @click="emit('select-section', index)"
                    :title="'Bagian ' + (index + 1) + ': ' + section.title"
                >
                    <div 
                        class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-full font-black text-xs sm:text-sm md:text-lg transition-all duration-200 z-10 shadow-xs relative"
                        :class="[
                            activeIndex === index 
                                ? 'bg-[#FFD700] text-[#005B3C] shadow-md scale-105 sm:scale-110 ring-2 sm:ring-4 ring-[#005B3C]/20' : 
                            (isSectionCompleted(index) 
                                ? 'bg-[#005B3C] text-white shadow-xs group-hover:bg-[#00482f] group-hover:scale-105' : 
                                'bg-gray-100 text-gray-400 group-hover:bg-gray-200 group-hover:text-gray-600')
                        ]"
                    >
                        <!-- Tanda centang putih jika section sudah selesai dan sedang tidak aktif -->
                        <svg 
                            v-if="isSectionCompleted(index) && activeIndex !== index" 
                            class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 text-white" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                        </svg>
                        
                        <!-- Angka tahapan jika aktif atau belum selesai -->
                        <span v-else>{{ index + 1 }}</span>

                        <!-- Badge mini centang di sudut kanan atas jika tahapan aktif ini sudah terisi lengkap -->
                        <span 
                            v-if="activeIndex === index && isSectionCompleted(index)" 
                            class="absolute -top-1 -right-1 w-3.5 h-3.5 sm:w-4 sm:h-4 bg-[#005B3C] text-white rounded-full flex items-center justify-center text-[8px] sm:text-[10px] font-black shadow-xs ring-1.5 sm:ring-2 ring-white"
                            title="Bagian ini sudah lengkap"
                        >
                            ✓
                        </span>
                    </div>
                    
                    <!-- Judul Tahapan Stepper -->
                    <span 
                        class="text-[9px] sm:text-[11px] md:text-xs mt-1 sm:mt-2 text-center w-20 sm:w-24 md:w-28 leading-tight transition-colors line-clamp-2"
                        :class="[
                            activeIndex === index 
                                ? 'text-[#005B3C] font-black' : 
                            (isSectionCompleted(index) 
                                ? 'text-[#005B3C] font-bold group-hover:text-[#00482f]' : 
                                'text-gray-400 group-hover:text-gray-600 font-medium')
                        ]"
                    >
                        {{ section.title }}
                    </span>
                </div>
                
                <!-- Garis Penghubung antar Lingkaran Stepper -->
                <div 
                    v-if="index < sections.length - 1" 
                    class="flex-1 h-1 sm:h-1.5 md:h-2 rounded-full transition-colors duration-300 mx-1 md:mx-2 min-w-[14px] sm:min-w-[20px]" 
                    :class="isLineCompleted(index) ? 'bg-[#005B3C]' : 'bg-gray-200'"
                ></div>
            </template>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    display: none;
}
.custom-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
