# Tracer Study UKDW (SERU - Sistem Ekosistem Rekam Jejak Alumni)

Sistem Informasi Tracer Study Alumni Universitas Kristen Duta Wacana (UKDW). Dibangun menggunakan **Laravel 11** (Backend), **Vue 3** & **Inertia.js** (Frontend SPA), serta **Tailwind CSS**.

---

## Daftar Isi
1. [Instalasi & Menjalankan Aplikasi](#instalasi--menjalankan-aplikasi)
2. [Peta Struktur File & Direktori Proyek](#peta-struktur-file--direktori-proyek)
3. [Arsitektur Kuesioner & Alur Data](#arsitektur-kuesioner--alur-data)
   - [A. Kuesioner Utama Universitas](#a-kuesioner-utama-universitas)
   - [B. Kuesioner Khusus Program Studi](#b-kuesioner-khusus-program-studi)
   - [C. Sinkronisasi Otomatis Data Akademik](#c-sinkronisasi-otomatis-data-akademik)
4. [Modularisasi Komponen Vue Alumni](#modularisasi-komponen-vue-alumni)
5. [Manajemen Modul & Peran Pengguna (Roles)](#manajemen-modul--peran-pengguna-roles)
6. [Panduan Pencarian Cepat Kode (Quick Navigation)](#panduan-pencarian-cepat-kode-quick-navigation)

---

## Instalasi & Menjalankan Aplikasi

1. Clone repository
2. Jalankan `composer install`
3. Jalankan `npm install`
4. Copy `.env.example` ke `.env`
5. Generate app key: `php artisan key:generate`
6. Jalankan migrasi dan seeder: `php artisan migrate --seed`
7. Jalankan local development server:
   ```bash
   # Terminal 1: Backend Laravel
   php artisan serve

   # Terminal 2: Frontend Vite
   npm run dev
   ```

---

## Peta Struktur File & Direktori Proyek

```text
tracerstudy/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminProdi/                  # Modul Administrator Program Studi
│   │   │   ├── Dashboard/DashboardController.php
│   │   │   ├── KelolaAlumni/DaftarAlumniProdiController.php
│   │   │   └── KelolaPertanyaan/
│   │   │       ├── DaftarPertanyaanProdiController.php
│   │   │       ├── KelolaOpsiProdiController.php
│   │   │       ├── KelolaSectionProdiController.php
│   │   │       └── SimpanPertanyaanProdiController.php
│   │   ├── Alumni/                      # Modul Alumni
│   │   │   ├── Dashboard/DashboardController.php
│   │   │   ├── Kuesioner/
│   │   │   │   ├── KuesionerController.php       # Kuesioner Universitas
│   │   │   │   ├── KuesionerProdiController.php  # Kuesioner Khusus Prodi
│   │   │   │   └── SimpanJawabanController.php
│   │   │   └── Profil/
│   │   │       ├── ProfilController.php
│   │   │       └── SimpanProfilController.php
│   │   └── SuperAdmin/                  # Modul Super Admin
│   │       ├── KelolaAlumni/DetailAlumniSuperAdminController.php
│   │       └── KelolaPertanyaan/
│   │           ├── DaftarPertanyaanController.php
│   │           ├── KelolaOpsiController.php
│   │           └── SimpanPertanyaanController.php
│   ├── Models/
│   │   ├── Alumni.php
│   │   ├── DataAkademik.php
│   │   ├── Prodi.php
│   │   ├── Questionnaire.php, QuestionSection.php, Question.php, QuestionOption.php, Response.php
│   │   └── ProdiQuestionSection.php, ProdiQuestion.php, ProdiQuestionOption.php, ProdiResponse.php
│   └── Services/Kuesioner/
│       ├── KelengkapanTracerService.php  # Evaluasi kelengkapan data & persentase
│       └── KuesionerSyncService.php      # Sinkronisasi otomatis data akademik ke responses & prodi_responses
├── database/
│   ├── migrations/
│   │   ├── 2026_09_13_090000_add_prodi_id_to_users_table.php
│   │   ├── 2026_09_13_092000_create_prodi_questionnaire_tables.php
│   │   └── 2026_09_13_093000_remove_prodi_id_from_questions_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── ProdiQuestionnaireSeeder.php  # Seeder instrumen prodi (Sistem Informasi & Filsafat)
│       ├── QuestionSeeder.php, QuestionOptionSeeder.php
│       └── UserSeeder.php
└── resources/js/Pages/
    ├── AdminProdi/                      # Antarmuka Admin Program Studi
    │   ├── Alumni/
    │   │   ├── Index.vue                 # Direktori Mahasiswa & Alumni Prodi
    │   │   └── Show.vue                  # Detail Audit Kuesioner & Profil Alumni Prodi
    │   ├── Components/Navbar.vue         # Navbar resmi Admin Prodi
    │   ├── Dashboard.vue                 # Dashboard KPI & Aktivitas Alumni Prodi
    │   ├── Pertanyaan/Index.vue          # Kelola Pertanyaan & Opsi Kuesioner Prodi
    │   └── Section/Index.vue             # Kelola Bagian / Section Kuesioner Prodi
    ├── Alumni/                          # Antarmuka Alumni
    │   ├── Components/Kuesioner/         # [MODULAR] Komponen Anak Kuesioner Tracer Universitas
    │   │   ├── Banner.vue                # Kartu banner hijau judul bagian
    │   │   ├── KartuPertanyaan.vue       # Input renderer (rating, radio, checkbox, multiple_number, dll)
    │   │   ├── Navbar.vue                # Header atas & tombol kembali
    │   │   ├── Navigasi.vue              # Tombol melayang desktop & fixed bottom bar mobile
    │   │   ├── Stepper.vue               # Navigasi tahapan bulatan 1 s/d N & auto-scroll
    │   │   └── TabelF17.vue              # Tabel perbandingan kompetensi dual-matrix (A vs B)
    │   ├── Dashboard.vue                 # Dashboard Alumni (progress card & profil)
    │   ├── Kuesioner.vue                 # [INDUK] Kuesioner Tracer Study Universitas
    │   └── KuesionerProdi.vue            # Kuesioner Khusus Program Studi
    └── SuperAdmin/                      # Antarmuka Super Admin
        ├── Alumni/Show.vue               # Detail Alumni (Audit Kuesioner Univ, Prodi & Profil)
        ├── Dashboard.vue
        └── Pertanyaan/Index.vue          # Kelola Kuesioner Universitas
```

---

## Arsitektur Kuesioner & Alur Data

### A. Kuesioner Utama Universitas
- **Database**: `questionnaires` $\rightarrow$ `question_sections` $\rightarrow$ `questions` $\rightarrow$ `question_options` $\rightarrow$ `responses`.
- Murni berlaku universal untuk seluruh program studi. Kolom `prodi_id` pada tabel `questions` telah dihapus secara bersih.
- Menggunakan branching logic otomatis berbasis `jump_to` pada tabel `question_options`.
- Butir **F17**: Evaluasi kompetensi berpasangan (A: Kemampuan Diri vs B: Kontribusi Kampus).

### B. Kuesioner Khusus Program Studi
- **Database**: Terpisah secara independen agar setiap program studi dapat mengelola instrumen evaluasinya sendiri:
  1. `prodi_question_sections`: Bagian/seksi kuesioner berbasis `prodi_id`.
  2. `prodi_questions`: Butir pertanyaan khusus prodi dengan tipe input dinamis.
  3. `prodi_question_options`: Pilihan opsi jawaban butir prodi.
  4. `prodi_responses`: Jawaban tersimpan milik alumni untuk kuesioner program studinya.
- Telah diisi instrumen lengkap untuk **Program Studi Sistem Informasi** (9 Bagian, 52 Pertanyaan) dan **Filsafat Keilahian**.

### C. Sinkronisasi Otomatis Data Akademik
- Pertanyaan identitas alumni pada Kuesioner Prodi:
  - **`PSI-1-01` (Nama)** $\rightarrow$ Diambil otomatis dari `data_akademiks.nama` (fallback `users.name`).
  - **`PSI-1-02` (NIM)** $\rightarrow$ Diambil otomatis dari `alumnis.nim` (atau `data_akademiks.nim`).
  - **`PSI-1-03` (Tahun Kelulusan)** $\rightarrow$ Diambil otomatis dari `data_akademiks.tahun_akademik_lulus` / `tahun_lulus`.
- Ditangani secara terpusat oleh [`KuesionerSyncService::syncProdiResponses($alumni)`](file:///c:/study/tracerstudy/app/Services/Kuesioner/KuesionerSyncService.php):
  - Terpicu saat halaman kuesioner alumni dibuka.
  - Terpicu saat detail alumni dibuka oleh Admin Prodi atau Super Admin.
  - Di sisi frontend alumni ([`KuesionerProdi.vue`](file:///c:/study/tracerstudy/resources/js/Pages/Alumni/KuesionerProdi.vue)), field bertipe `readonly` dengan badge hijau resmi *"✓ Data Akademik"*, sehingga alumni tidak perlu mengetik ulang data diri.

---

## Modularisasi Komponen Vue Alumni

Sebelumnya file [`resources/js/Pages/Alumni/Kuesioner.vue`](file:///c:/study/tracerstudy/resources/js/Pages/Alumni/Kuesioner.vue) berukuran **1.621 baris kode** dalam 1 file monolitik. Kini telah didekomposisi menjadi arsitektur modular:

| Nama Komponen | Lokasi File | Peran & Tanggung Jawab |
|---|---|---|
| **Kuesioner (Induk)** | `Pages/Alumni/Kuesioner.vue` | Mengorkestrasi state form Inertia, alur *jump logic*, validasi per seksi, dan persistensi sesi `localStorage` (~490 baris). |
| **Navbar** | `Pages/Alumni/Components/Kuesioner/Navbar.vue` | Header atas dengan logo resmi UKDW dan navigasi tombol Kembali ke Dashboard. |
| **Stepper** | `Pages/Alumni/Components/Kuesioner/Stepper.vue` | Navigasi tahapan bulatan angka 1 s/d N, judul seksi, indikator centang selesai (`✓`), dan *auto-scroll*. |
| **Banner** | `Pages/Alumni/Components/Kuesioner/Banner.vue` | Banner hijau judul seksi aktif (*"Bagian X dari Y"*) dan petunjuk pengisian. |
| **TabelF17** | `Pages/Alumni/Components/Kuesioner/TabelF17.vue` | Tabel komparasi dua sisi instrumen F17 (*Kemampuan Diri* vs *Kontribusi Kampus*), badge komparasi otomatis, dan counter progres aspek. |
| **KartuPertanyaan** | `Pages/Alumni/Components/Kuesioner/KartuPertanyaan.vue` | Renderer butir pertanyaan beserta seluruh variasi input (`rating_5`, `searchable_select`, `radio`, `checkbox`, `radio_input`, `multiple_number`, `number`, `text`, layout 2 kolom). |
| **Navigasi** | `Pages/Alumni/Components/Kuesioner/Navigasi.vue` | Tombol navigasi desktop melayang (`< Kembali` dan `> Lanjut/Selesai`) serta *fixed bottom bar* di smartphone/mobile. |

---

## Manajemen Modul & Peran Pengguna (Roles)

1. **`superadmin`**:
   - Dashboard analitik seluruh universitas.
   - Kelola Pertanyaan Kuesioner Universitas (`/superadmin/pertanyaan`).
   - Kelola Section Kuesioner Universitas (`/superadmin/sections`).
   - Direktori Seluruh Alumni & Audit Detail Hasil Kuesioner (Kuesioner Univ, Kuesioner Prodi, Profil Mahasiswa) (`/superadmin/alumni/{id}`).
2. **`admin_prodi`**:
   - Terikat pada `users.prodi_id`.
   - Dashboard KPI khusus program studi (`/prodi/dashboard`).
   - Kelola Butir Pertanyaan Kuesioner Program Studi (`/prodi/pertanyaan`).
   - Kelola Bagian (Section) Kuesioner Program Studi (`/prodi/sections`).
   - Direktori Mahasiswa & Alumni khusus prodi (`/prodi/alumni`).
   - Audit Detail Jawaban & Profil Alumni Program Studi (`/prodi/alumni/{id}`).
3. **`alumni`**:
   - Dashboard kelengkapan data & progres kuesioner (`/alumni/dashboard`).
   - Pengisian Kuesioner Tracer Study Universitas (`/alumni/kuesioner`).
   - Pengisian Kuesioner Khusus Program Studi (`/alumni/kuesioner-prodi`).
   - Pembaruan Biodata, Data Akademik, Data Orang Tua, dan Karier/Perusahaan (`/alumni/profile`).

---

## Panduan Pencarian Cepat Kode (Quick Navigation)

- Ingin mengubah tampilan/input kuesioner tracer alumni? Buka [`resources/js/Pages/Alumni/Components/Kuesioner/KartuPertanyaan.vue`](file:///c:/study/tracerstudy/resources/js/Pages/Alumni/Components/Kuesioner/KartuPertanyaan.vue).
- Ingin mengubah tabel perbandingan F17? Buka [`resources/js/Pages/Alumni/Components/Kuesioner/TabelF17.vue`](file:///c:/study/tracerstudy/resources/js/Pages/Alumni/Components/Kuesioner/TabelF17.vue).
- Ingin melihat logika sinkronisasi data akademik? Buka [`app/Services/Kuesioner/KuesionerSyncService.php`](file:///c:/study/tracerstudy/app/Services/Kuesioner/KuesionerSyncService.php).
- Ingin melihat controller kuesioner prodi alumni? Buka [`app/Http/Controllers/Alumni/Kuesioner/KuesionerProdiController.php`](file:///c:/study/tracerstudy/app/Http/Controllers/Alumni/Kuesioner/KuesionerProdiController.php).
- Ingin mengedit tampilan Admin Prodi? Buka folder [`resources/js/Pages/AdminProdi/`](file:///c:/study/tracerstudy/resources/js/Pages/AdminProdi/).
- Ingin mengedit seeder kuesioner program studi? Buka [`database/seeders/ProdiQuestionnaireSeeder.php`](file:///c:/study/tracerstudy/database/seeders/ProdiQuestionnaireSeeder.php).
