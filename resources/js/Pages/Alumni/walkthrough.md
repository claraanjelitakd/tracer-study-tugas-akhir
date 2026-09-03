# Ringkasan Efek dan Perubahan Visual (Tracer Study)

Berikut adalah daftar lengkap efek, animasi, dan modifikasi logika yang telah diterapkan pada antarmuka Kuesioner dan Dashboard Alumni:

## 1. Animasi Masuk (GSAP)
- **Stagger Animation pada Kuesioner:** Saat alumni berpindah dari satu tahap ke tahap lain, pertanyaan tidak muncul secara kaku sekaligus. Menggunakan pustaka animasi **GSAP (`gsap.fromTo`)**, kotak pertanyaan muncul secara bergiliran (*stagger* = 0.1 detik) dari bawah ke atas dengan efek `back.out(1.5)` yang memberikan sensasi pantulan (pegas) ringan, sehingga transisi halaman terasa hidup dan *smooth*.
- **ScrollTrigger di Dashboard:** Kartu ringkasan dan lowongan kerja di Dashboard akan otomatis muncul dengan animasi pantulan (*bounce-in*) saat alumni men-scroll layar ke bawah. Ini membantu menjaga antarmuka tetap bersih dan menarik perhatian tepat pada elemen yang sedang dilihat.

## 2. Sistem Navigasi Kuesioner (*Bubbly Stepper*)
- **Desain Layar Penuh (*Full-Width*):** Stepper (navigasi angka 1-9 di bagian atas kuesioner) sekarang melintang penuh menyesuaikan lebar layar, sama persis seperti header *navbar*, menghilangkan kesan sempit.
- **Transisi Horisontal Stabil:** Efek *scale* dan perpindahan koordinat Y yang sebelumnya membuat lingkaran bergeser ke atas/bawah sudah **dihapus**. Sekarang seluruh bulatan navigasi sejajar rata dalam satu garis lurus yang rapi.
- **Warna Indikator:** 
  - **Tahap Aktif:** Diwarnai **Emas/Kuning (UKDW)** agar langsung terlihat.
  - **Tahap Selesai:** Diwarnai **Hijau Tua** dan angka berubah menjadi **tanda centang (v)** yang melambangkan tahapan tersebut telah dijawab.

## 3. Efek Tombol Navigasi Layar (Kiri dan Kanan)
- **Floating Navigation:** Tombol "Kembali" (`<`) dan "Lanjutkan" (`>`) dilepaskan dari posisi aslinya di bawah *form* dan diubah menjadi *floating buttons* (tombol melayang) di **pojok kiri bawah** dan **pojok kanan bawah** layar. Hal ini memberikan ruang baca kuesioner yang maksimal (ala aplikasi *game* atau *quizizz*).
- **Efek Arcade (Tekan-Bawah):** Memanfaatkan CSS Tailwind, saat kursor diarahkan ke tombol (*hover*), tombol akan sedikit terangkat ke atas. Saat tombol benar-benar **diklik/ditekan** (*active*), tombol akan bergeser ke bawah (`active:translate-y-1.5`) dan bayangannya hilang (`active:shadow-none`), menciptakan simulasi tombol fisik pada mesin konsol yang ditekan.

## 4. Toast Gamifikasi (Pop-up Pujian)
- **Mengganti Poin XP dengan Teks Pujian:** Karena sistem Tracer Study tidak memiliki *database* ranking skor, pop-up "+ XP" diubah menjadi pop-up motivasi acak (seperti "HEBAT!", "MANTAP!", "TERSIMPAN!").
- **Animasi Vue Transition:** Menggunakan tag bawaan `<Transition name="bounce">` dari Vue.js untuk memunculkan kotak kuning melayang di atas layar dengan sedikit kemiringan (*rotate 3deg*) beserta ikon bintang yang memantul-mantul (`animate-bounce`). Pop-up ini memudar secara otomatis setelah 2 detik.

## 5. Dokumentasi Baris Kode (Bahasa Indonesia)
Sesuai instruksi, **seluruh baris kode pada file kuesioner (`kuesioner/index.vue`) telah dilengkapi dengan komentar berbahasa Indonesia** pada blok `<script>` maupun `<template>`, mulai dari logika lompatan kuesioner (*Jump Logic*), persiapan inisialisasi state, penanganan CSS animasi, hingga struktur form matriks skala 1-5. Ini akan memastikan bahwa *developer* berikutnya bisa membaca dan mengelola struktur antarmuka gamifikasi ini dengan mudah di masa mendatang.
