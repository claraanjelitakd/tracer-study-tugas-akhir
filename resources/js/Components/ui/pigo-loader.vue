<template>
    <transition name="fade">
        <div v-if="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-white">
            <div class="flex flex-col sm:flex-row items-center gap-6 p-8">
                <!-- Gambar Pigo -->
                <div class="relative w-32 h-32 sm:w-40 sm:h-40 shrink-0">
                    <img src="/uploads/landing/1.png" alt="Pigo Mascot" class="w-full h-full object-contain mix-blend-multiply animate-pulse-slow drop-shadow-xl" />
                </div>
                
                <!-- Spinner & Text -->
                <div class="flex flex-col items-center sm:items-start gap-3">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full border-4 border-gray-200 border-t-purple-600 animate-spin"></div>
                        <h2 class="text-2xl font-bold text-gray-800 tracking-wider">Memuat...</h2>
                    </div>
                    <p class="text-gray-500 text-sm">Menyiapkan Tracer Study UKDW</p>
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
