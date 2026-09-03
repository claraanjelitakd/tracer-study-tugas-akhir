<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '../Components/Landing/Navbar.vue';
import HeroSection from '../Components/Landing/HeroSection.vue';
import AlurPengisianSection from '../Components/Landing/AlurPengisianSection.vue';
import TracerStudySection from '../Components/Landing/TracerStudySection.vue';
import AlumniSection from '../Components/Landing/AlumniSection.vue';
import BeritaTerbaruSection from '../Components/Landing/BeritaTerbaruSection.vue';
import Footer from '../Components/Landing/Footer.vue';
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
