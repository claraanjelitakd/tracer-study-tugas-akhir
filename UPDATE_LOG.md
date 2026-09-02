# UPDATE LOG - SERU (Sistem Ekosistem Rekam Jejak Alumni)

## [2026-09-02] Step 5: Kuesioner UI/UX & Google Forms Logic
- **UI/UX Matriks**: Mengubah soal tipe Matriks F17 (Dual) dan F19-F22 (Single) menjadi komponen tabel yang responsif, *sticky column*, dan pewarnaan kontras.
- **Pill Buttons & Checkbox**: Mengganti *radio button* bawaan browser menjadi tombol bergaya *Pill* interaktif.
- **Validasi Input Murni**: Menambahkan event `@keydown` pada semua tipe *number* dan *multiple_number* untuk memblokir input non-numerik murni (misal: memblokir huruf 'e', tanda plus/minus, titik, koma).
- **Google Forms Jumping Logic**: Merombak total arsitektur `QuestionSectionSeeder` dan `QuestionSeeder` untuk mengisolasi pertanyaan dengan efek *jump* (misal: F3) ke dalam 1 halaman *section* sendiri.
- **Fix JSON Bug**: Menghilangkan `json_encode` di Seeder yang menyebabkan *double-encoding* pada atribut *jump_logic*.

## [2026-09-02] Step 4: Core Engine Kuesioner Dinamis
- **Arsitektur DB**: Membuat tabel hierarkis: `questionnaires`, `question_sections`, `questions`, `question_options`, dan `responses`.
- **Relasi Pemetaan Ekstra**: Membuat tabel `question_mappings` untuk mencatat relasi secara eksplisit (beserta *foreign key*) antara pertanyaan di kuesioner dengan kolom di tabel target (seperti kolom identitas di tabel `alumnis`).
- **Seeder Lengkap**: Memetakan seluruh instrumen Kuesioner Tracer Study dari F1 sampai F22 ke dalam format data relasional (termasuk *jump_logic* JSON).
- **Controller**: Membangun `QuestionnaireController` untuk me-*render* kuesioner dinamis secara *nested* ke Vue dan menyimpan respons (baik teks murni maupun JSON untuk *checkbox* / matriks).
- **Middleware Update**: Menambahkan `auth.user` data ke `HandleInertiaRequests` untuk mencegah *white screen crash* pada komponen Vue yang bergantung pada identitas pengguna.

## [2026-09-02] Step 3: Database Refactoring, Models, & UI/UX Enhancements
- **Migrasi**: Menghapus kolom spesifik alumni (`phone`, `address`, `ipk`, `sac_points`) dari tabel `users`.
- **Migrasi Baru**: Membuat tabel `alumnis` yang memuat F1 (NIM), F2A (Nama), F2B (Telepon), F2C (Email), F2D (Alamat Sekarang), `ipk`, dan `sac_points` yang berelasi `One-to-One` dengan `users`.
- **Model**: Menambahkan relasi `hasOne(Alumni::class)` di model `User` dan `belongsTo(User::class)` di model `Alumni`.
- **Seeder**: Memperbarui `DatabaseSeeder` dengan akun alumni percobaan (NIM: 72230607, Password: 01012001) yang wajib mengganti password. Akun admin diset agar tidak wajib ganti password.
- **UI/UX**: Menambahkan transisi, efek *hover*, dan *micro-animations* pada seluruh komponen *Landing Page* (`HeroSection`, `TracerStudySection`, `AlumniCard`, `CTASection`) agar lebih hidup dan interaktif.
- **Komentar Kode**: Memperbarui dokumentasi di setiap fungsi, *routes*, dan *controller*.

## [2026-09-01] Step 2: Foundation & Authentication
- Membuat skema tabel `users` untuk memuat *role* dan *must_change_password*.
- Membuat `DatabaseSeeder` untuk *roles* (admin_biro3, admin_prodi, superadmin).
- Membangun `AuthController` untuk manajemen Login, Logout, dan Penggantian Password (paksa).
- Membangun *Middlewares*: `RoleMiddleware` dan `CheckMustChangePassword`.
- Membuat seluruh kerangka Vue (Landing Page dan Dashboard).
- Memecah *Landing Page* menjadi modular (Navbar, Hero, TracerStudy, Statistics, Alumni, dll).

## [2026-09-01] Step 1: Inisiasi Proyek
- Membuat proyek Laravel 11.
- Mengonfigurasi Tailwind CSS v4, Vue 3, dan Inertia.js.
- Menyusun panduan arsitektur (Backend-Only Logic, UI Components).
