<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <section id="tracer-study" class="py-20 bg-green-800 text-white relative overflow-hidden">
        <!-- Abstract decorative shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-yellow-400 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full bg-green-500 blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl font-extrabold sm:text-4xl mb-6">Perjalananmu adalah bagian dari cerita UKDW.</h2>
            <p class="text-xl text-green-100 mb-10 leading-relaxed">
                Ayo perbarui informasi Anda dan berpartisipasi dalam Tracer Study. Setiap data yang Anda berikan sangat berarti bagi evaluasi dan pengembangan pendidikan di Universitas Kristen Duta Wacana.
            </p>
            
            <Link
                href="/alumni/kuesioner"
                class="inline-flex justify-center items-center px-8 py-4 border border-transparent text-lg font-bold rounded-xl text-green-900 bg-yellow-400 hover:bg-yellow-300 hover:scale-105 active:scale-95 shadow-[0_0_20px_rgba(250,204,21,0.4)] transition-all duration-300"
            >
                Mulai Tracer Study Sekarang
            </Link>
        </div>
    </section>
</template>
