# Tracer Study

Sistem Informasi Tracer Study Alumni.

## Instalasi

1. Clone repository
2. Jalankan `composer install`
3. Jalankan `npm install`
4. Copy `.env.example` ke `.env`
5. Generate app key `php artisan key:generate`
6. Jalankan migrasi `php artisan migrate`

## Development

Jalankan perintah berikut:

```bash
php artisan serve
npm run dev
```

## Arsitektur Teknis

### Kuesioner Dinamis (Survey Engine)
Sistem menggunakan *Survey Engine* dinamis (Google Forms-style) yang tersimpan di database secara hierarkis:
1. `Questionnaire` -> `QuestionSection` -> `Question` -> `QuestionOption`
2. **Branching & Jump Logic**:
   - Seluruh alur percabangan (*branching flow*) kuesioner kini terpusat dan dikelola secara modular pada kolom `jump_to` di tabel `question_options`.
   - Menghilangkan redundansi kolom `jump_logic` di tabel `questions` serta meniadakan *hardcode* di sisi frontend.
   - Contoh aliran:
     - **F3** ("Kapan mulai mencari kerja?"): Opsi *'Saya tidak mencari kerja'* memiliki `jump_to = 'F8'`, otomatis melewati pertanyaan pelamaran kerja F4-F7.
     - **F8** ("Apakah Anda bekerja saat ini?"): Opsi *'Ya'* melompat ke `F11` (Karakteristik Pekerjaan). Opsi *'Tidak'* melompat ke `F9` (Situasi Saat Ini).
     - **F10** ("Pencarian kerja dalam 4 minggu terakhir"): Seluruh opsi melompat langsung ke `F17-1` (Evaluasi Kompetensi), melewati bagian karakteristik pekerjaan.
3. **Pertanyaan Spesifik Program Studi (Prodi Scoping)**:
   - Tabel `questions` dilengkapi kolom `prodi_id` (`nullable foreign key` ke `prodis.id`).
   - Jika `prodi_id = NULL`, pertanyaan bersifat umum dan wajib diisi oleh seluruh alumni.
   - Jika `prodi_id` diisi, pertanyaan tersebut hanya akan diambil dan ditampilkan bagi alumni yang terdaftar pada program studi tersebut (disaring otomatis di query backend [KuesionerController.php](file:///c:/study/tracerstudy/app/Http/Controllers/Alumni/Kuesioner/KuesionerController.php) tanpa hardcode).
   - **Contoh Real**: Pertanyaan `F2E` ("Jenis Pekerjaan & Nama Perusahaan / Instansi / Institusi - Gerejawi / Non Gerejawi") dikhususkan hanya untuk alumni Program Studi **Filsafat Keilahian** (Kode Prodi `31`).
4. **Evaluasi Kompetensi F17 (Banding Berdampingan)**:
   - Aspek kompetensi F17 (27 aspek, total 54 butir soal) disajikan secara berdampingan (*side-by-side comparison*) dalam kartu yang intuitif:
     - **Kolom Kiri**: Kompetensi yang dikuasai saat lulus (Skala 1 - 5).
     - **Kolom Kanan**: Kontribusi perguruan tinggi dalam kompetensi tersebut (Skala 1 - 5).
   - Pilihan opsi berupa lingkaran interaktif bergradasi hijau/emas yang mudah dipilih di desktop maupun perangkat mobile.
5. **Estetika UI Bersih & Natural**:
   - Menghapus efek bayangan tebal garis bawah (*chunky bottom shadow*) dan popup gamifikasi yang mengganggu.
   - Menggunakan estetika modern, flat-clean dengan border halus, warna hijau khas UKDW (`#005B3C`), dan aksen emas (`#C5A059`).

---

## Log Pembaruan (Changelog)

### Pembaruan Kuesioner & Branching Logic (September 2026)
1. **Unifikasi Jump Logic ke `question_options.jump_to`**:
   - Menghapus kolom lama `jump_logic` dari tabel `questions` dan membersihkan logika *hardcode* di controller maupun komponen Vue.
   - Seluruh logika alur kuesioner kini sepenuhnya dikendalikan oleh nilai `jump_to` pada opsi jawaban di database.
   - Menambahkan seeder `jump_to` lengkap untuk pertanyaan `F3`, `F8`, dan `F10` di [QuestionOptionSeeder.php](file:///c:/study/tracerstudy/database/seeders/QuestionOptionSeeder.php).
2. **Dukungan Pertanyaan Khusus Prodi (`prodi_id`)**:
   - Menambahkan kolom `prodi_id` pada migrasi tabel `questions` serta relasi `prodi()` pada model [Question.php](file:///c:/study/tracerstudy/app/Models/Question.php).
   - Pertanyaan `F2E` dikaitkan secara dinamis ke Prodi Filsafat Keilahian (kode `31`) pada [QuestionSeeder.php](file:///c:/study/tracerstudy/database/seeders/QuestionSeeder.php).
   - [KuesionerController.php](file:///c:/study/tracerstudy/app/Http/Controllers/Alumni/Kuesioner/KuesionerController.php) otomatis memfilter pertanyaan berdasarkan `prodi_id` alumni yang sedang login.
   - Mengintegrasikan kolom pemilihan target program studi dan label badge khusus prodi pada panel admin [KelolaPertanyaanController.php](file:///c:/study/tracerstudy/app/Http/Controllers/AdminBiroTiga/KelolaPertanyaan/KelolaPertanyaanController.php) dan view [Index.vue](file:///c:/study/tracerstudy/resources/js/Pages/AdminBiroTiga/Pertanyaan/Index.vue).
3. **Penyegaran Tampilan Kuesioner**:
   - Komparasi F17 berdampingan (horizontal) untuk membandingkan kompetensi diri vs kontribusi kampus secara langsung.
   - Pembersihan visual: menghilangkan shadow garis bawah kartun dan notifikasi animasi gamifikasi saat berpindah section.

---

## Aturan Penamaan & Konsistensi (Penting untuk Linux/Server)

Karena sistem file di Linux bersifat **case-sensitive** (membedakan huruf besar dan kecil), berlawanan dengan Windows yang *case-insensitive*, berikut adalah aturan wajib untuk proyek ini:

1. **Frontend (Vue & Aset):**
   - Disarankan menggunakan format huruf kecil semua dengan pemisah strip (`kebab-case`) untuk folder dan file baru. Contoh: `components/alumni-card.vue`.
   - **Penting:** Saat melakukan `import` di dalam file Vue/JS, pastikan huruf besar/kecilnya **sama persis** dengan nama file asli di sistem. Contoh: Jika nama file adalah `HeroSection.vue`, maka import-nya wajib `import HeroSection from './HeroSection.vue'`. Jangan sampai file bernama kapital tapi di-import dengan huruf kecil.

2. **Backend (Laravel & PHP):**
   - **TIDAK BOLEH** menggunakan huruf kecil semua untuk Class, Controller, dan Model. Laravel menggunakan standar **PSR-4** dimana nama file dan namespace **harus persis** (biasanya *PascalCase*).
   - Contoh benar: `app/Http/Controllers/UserController.php` (Class `UserController`).
   - Jika ini diubah menjadi `usercontroller.php`, sistem autoloading Composer akan **rusak di Linux**.
   - Pastikan setiap pemanggilan class menggunakan klausa `use` yang case-nya sama persis dengan file aslinya.
