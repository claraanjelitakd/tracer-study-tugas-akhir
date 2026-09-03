<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { Head } from '@inertiajs/vue3';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Navbar from '../components/landing/navbar.vue';
import HeroSection from '../components/landing/hero-section.vue';
import AlurPengisianSection from '../components/landing/alur-pengisian-section.vue';
import TracerStudySection from '../components/landing/tracer-study-section.vue';
import AlumniSection from '../components/landing/alumni-section.vue';
import BeritaTerbaruSection from '../components/landing/berita-terbaru-section.vue';
import Footer from '../components/landing/footer.vue';

gsap.registerPlugin(ScrollTrigger);

const initAnimations = () => {
    nextTick(() => {
        // Animasi teks Hero dengan fromTo memastikan state akhirnya benar (opacity 1, y 0)
        gsap.fromTo('.hero-title', 
            { y: -50, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 1, ease: "bounce.out" }
        );
        
        gsap.fromTo('.hero-subtitle', 
            { y: 30, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 1, delay: 0.5, ease: "power2.out" }
        );

        // Animasi pergantian gambar Pigo 1 ke Pigo 2 saat di-scroll
        gsap.to('.pigo-img-1', {
            scrollTrigger: {
                trigger: '.hero-title',
                start: "top top",
                end: "bottom top",
                scrub: true
            },
            opacity: 0,
            scale: 0.8
        });

        gsap.to('.pigo-img-2', {
            scrollTrigger: {
                trigger: '.hero-title',
                start: "top top",
                end: "bottom top",
                scrub: true
            },
            opacity: 1,
            scale: 1.1
        });

        const fadeUpElements = document.querySelectorAll('.gsap-fade-up');
        fadeUpElements.forEach((el) => {
            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                },
                y: 50,
                opacity: 0,
                duration: 0.8,
                ease: "back.out(1.7)"
            });
        });

        const fadeDownElements = document.querySelectorAll('.gsap-fade-down');
        fadeDownElements.forEach((el) => {
            gsap.from(el, {
                scrollTrigger: {
                    trigger: el,
                    start: "top 85%",
                    toggleActions: "play none none reverse"
                },
                y: -50,
                opacity: 0,
                duration: 0.8,
                ease: "back.out(1.7)",
                delay: 0.1
            });
        });
    });
};

onMounted(() => {
    // Jalankan inisialisasi GSAP setelah global loader selesai (1.5 detik)
    setTimeout(() => {
        initAnimations();
    }, 1500);
});
</script>

<template>
    <Head title="Beranda - SERU UKDW" />

    <div class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
        <Navbar />
        
        <main>
            <!-- 1. Top Section dengan Video Background & Search Bar -->
            <HeroSection />
            
            <!-- 2. Alur Pengisian & Informasi Kuesioner -->
            <AlurPengisianSection />
            
            <!-- 3. Manfaat Tracer Study -->
            <TracerStudySection />
            
            <!-- 4. Sebaran Alumni dengan Filter -->
            <AlumniSection />
            
            <!-- 5. Berita Terbaru -->
            <BeritaTerbaruSection />
        </main>

        <Footer />
    </div>
</template>
