<template>
    <transition name="fade">
        <div v-if="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/20 transition-opacity">
            <div class="bg-white py-4 px-6 rounded-xl shadow-2xl flex items-center space-x-5 animate-bounce-short">
                
                <!-- Pigo Mascot dengan Spinner mengelilinginya -->
                <div class="relative flex items-center justify-center w-12 h-12 shrink-0">
                    <svg class="absolute inset-0 w-full h-full text-gray-100 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-100" cx="12" cy="12" r="11" stroke="currentColor" stroke-width="2"></circle>
                        <path class="opacity-100 text-[#005B3C]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M12 1A11 11 0 001 12"></path>
                    </svg>
                    <img src="/uploads/landing/2.png" alt="Pigo Mascot" class="w-8 h-8 object-contain z-10 animate-pulse" onerror="this.src='/uploads/landing/3.png'" />
                </div>

                <div class="flex flex-col">
                    <span class="text-gray-900 font-bold text-base">Memproses...</span>
                    <span class="text-gray-500 text-xs font-medium">Harap tunggu sebentar...</span>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
const props = defineProps({
    isLoading: {
        type: Boolean,
        default: true
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.8s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.animate-pulse-slow {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: .8;
        transform: scale(0.95);
    }
}
</style>

<style scoped>
/* Animasi ringan untuk popup */
.animate-bounce-short {
    animation: bounce-short 0.3s ease-out 1;
}
@keyframes bounce-short {
    0% { transform: translateY(20px); opacity: 0; }
    50% { transform: translateY(-5px); }
    100% { transform: translateY(0); opacity: 1; }
}
</style>
