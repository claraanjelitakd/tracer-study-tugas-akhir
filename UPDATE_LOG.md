# UPDATE LOG - SERU (Sistem Ekosistem Rekam Jejak Alumni)

## [2026-09-07] Penyempurnaan Direktori Alumni Biro 3: Dropdown Tahun Yudisium Kelulusan & Filter Terisolasi per Tahun
- **Penggantian Navigasi Tab Menjadi Dropdown**:
  - Menghilangkan navigasi horizontal tab per-semester di bagian atas tabel, menggantikannya dengan komponen **Dropdown Tahun / Semester Yudisium** yang terintegrasi di panel filter bersama Program Studi dan Pencarian.
- **Penyajian Data Terisolasi per Tahun Kelulusan (Tanpa Tumpukan Semua Semester)**:
  - Mengubah sistem kueri di `DaftarAlumniController.php` agar secara default langsung memilih tahun/semester kelulusan terbaru (`$daftarSemester->first()`), dan tabel HANYA memuat data alumni pada periode yang dipilih tersebut.
  - Mencegah kelebihan beban data (*information overload*) dengan meniadakan mode tampil semua semester sekaligus.
- **Filter Ketat Status Yudisium Lulus (`proses_yudisium = 'Lulus'`)**: 
  - Backend controller `DaftarAlumniController.php` memfilter data alumni secara eksklusif hanya bagi alumni yang status yudisiumnya telah dinyatakan `'Lulus'` pada relasi `yudisium` (`yudisiums.proses_yudisium = 'Lulus'`) atau `dataAkademik.status_yudisium`.
  - Alumni yang berstatus Belum Yudisium, Proses, atau Tidak Lulus tidak akan ditampilkan dalam direktori kelulusan Biro 3.
- **Standarisasi Menyeluruh Navigasi & Identitas Visual Biro 3**:
  - Menyeragamkan seluruh tata letak bilah navigasi atas (Navbar) di seluruh portal Biro 3 (`/biro3/dashboard`, `/biro3/alumni`, `/biro3/alumni/{id}`, `/biro3/pertanyaan`).
  - Menggunakan navbar putih bersih dengan efek `backdrop-blur`, logo resmi UKDW, teks resmi biro, menu tab konsisten (`Dashboard`, `Data Alumni`, `Kelola Pertanyaan`), dan tombol Keluar abu-abu bersih.
  - Menggunakan tema warna institusi formal: Hijau resmi UKDW (`#005B3C`), Kuning Landing Page (`#FACC15` / `yellow-400`), dan Putih.
  - Membasmi seluruh unsur warna orange (`#D97706`, `#F59E0B`), merah mencolok, dan icon berlebihan (anti AI-slop).
- **Penambahan Informasi Akademik Lengkap**:
  - Menampilkan NIM, Nama, Program Studi, IPK Kelulusan, Judul Tugas Akhir / Skripsi, Status Yudisium (Badge: Lulus), dan aksi detail alumni.
- **Pembaruan Detail Alumni (`AlumniShow.vue`)**:
  - Menyelaraskan navbar atas dan header banner dengan breadcrumb navigasi yang elegan.
  - Menampilkan informasi status kelulusan yudisium dan semester kelulusan akademik secara terintegrasi.

## [2026-09-07] Refinement Kelola Pertanyaan & Panel Biro 3: Desain Ala Profil Alumni, Navigasi Tab Per-Section, & Warna Kuning Landing Page
- **Desain Profesional Ala Profil Alumni**: Merombak antarmuka `Index.vue` kelola pertanyaan dengan estetika formal profesional yang bersih (banner gradient hijau UKDW `#005B3C` ke `#007b55`, kartu kontainer terangkat `rounded-2xl`, tipografi proporsional, dan tombol aksi kuning landing page `#FACC15` / `yellow-400`).
- **Navigasi Section di Atas (Horizontal Tabs)**: Menyediakan tab navigasi bagian/section kuesioner horizontal di bagian atas (seperti navigasi tab di Profil Alumni). Admin dapat memilih bagian (Bagian 1, Bagian 2, dst) secara cepat.
- **Pengelompokan & Filter Soal per Section**: Halaman hanya menampilkan butir pertanyaan dari section yang sedang aktif (tidak menumpuk 20+ soal sekaligus sehingga tidak membuat pusing).
- **Pemindahan Pertanyaan Terisolasi per Section**: Tombol pemindah urutan (▲ / ▼) pada kartu bubble kini dibatasi hanya menukar urutan butir pertanyaan di dalam section yang sama, baik di frontend maupun di kueri backend (`KelolaPertanyaanController.php`).
- **Pembersihan Total Warna Orange & Merah Mencolok**: Menghapus seluruh warna orange (`#D97706`, `#F59E0B`, amber) dan merah mencolok di seluruh panel Biro 3 (`Index.vue`, `Dashboard.vue`, `AlumniIndex.vue`, `AlumniShow.vue`). Menggantinya dengan warna kuning resmi landing page (`#FACC15` / `yellow-400`) dan hijau resmi UKDW.
- **Pembersihan Icon Berlebihan (Anti AI-Slop)**: Menghilangkan emoji berlebihan dan ikon-ikon dekoratif yang tidak perlu, menyajikan tampilan tabel dan formulir yang bersih, elegan, dan berstandar aplikasi perguruan tinggi.

## [2026-09-07] Implementasi Dashboard Resmi & Portal Kendali Biro 3 (Formal, Tegas, Hijau-Kuning-Putih)
- **Halaman Dashboard Utama Biro 3 (`/biro3/dashboard`)**: Membangun portal landing resmi untuk Administrator Biro 3 agar saat login tidak langsung dilempar masuk ke submenu alumni, melainkan disambut dengan ringkasan indikator kinerja utama.
- **Konsep Desain Formal & Tegas**: Menerapkan tema institusional resmi universitas yang tegas dan berwibawa menggunakan warna resmi UKDW (Hijau Tua `#005B3C` / `#004D32`, Kuning Emas `#D97706` / `#F59E0B`, dan Putih Bersih). Menghindari desain kasual/playful bergelombang ala alumni dengan sudut tegas (`rounded`), pembagian panel modular, dan tipografi administratif rapi.
- **Kartu Metrik Eksekutif (KPI)**: Menampilkan 4 kartu indikator utama: Total Basis Data Alumni (beserta jumlah data LinkedIn), Partisipasi Kuesioner (responden terverifikasi & progress bar tingkat respons), Total Instrumen Pertanyaan, serta Cakupan Program Studi Terintegrasi.
- **Dua Modul Gerbang Navigasi Utama**:
  1. *Modul 01 - Basis Data & Direktori Alumni*: Akses langsung ke manajemen alumni, pencarian prodi/NIM, dan sinkronisasi LinkedIn (`/biro3/alumni`).
  2. *Modul 02 - Konfigurasi Instrumen & Jump Logic*: Akses langsung ke pengelolaan pertanyaan kuesioner, opsi jawaban, dan scoping prodi (`/biro3/pertanyaan`).
- **Tabel Pemantauan Partisipasi per Program Studi**: Menyajikan rekapitulasi data kelulusan, jumlah responden kuesioner, tingkat partisipasi persentase dengan progress bar, dan badge status evaluasi capaian per prodi.
- **Alur Login & Rute Terpadu**:
  - Memperbarui `LoginController.php` agar peran `admin_biro3` otomatis dialihkan ke `/biro3/dashboard`.
  - Mendaftarkan rute `Route::get('/biro3/dashboard', ...)->name('biro3.dashboard')` pada `routes/web.php`.
- **Penyeragaman Navigasi Atas (*Header Bar*)**:
  - Menyeragamkan header institusional di seluruh halaman Biro 3 (`Dashboard.vue`, `AlumniIndex.vue`, `Pertanyaan/Index.vue`, dan `AlumniShow.vue`) dengan navigasi 3 tab: **Dashboard**, **Data Alumni**, dan **Kelola Pertanyaan**.
- **Automated Feature Testing**: Menambahkan berkas pengujian `tests/Feature/BiroTigaDashboardTest.php` untuk memvalidasi proteksi tamu, pengalihan login `admin_biro3`, dan rendering komponen Inertia.

## [2026-09-07] Scoping Pertanyaan Prodi Dinamis & Reseeding Bersih
- **Kolom `prodi_id` pada Tabel `questions`**: Menambahkan kolom `prodi_id` (nullable foreign key ke `prodis.id` dengan `nullOnDelete`) langsung di migrasi utama `create_questions_table.php` dan model `Question.php`.
- **Dukungan Khusus Pertanyaan F2E**: Mengaitkan pertanyaan `F2E` ("Jenis Pekerjaan & Nama Perusahaan / Instansi / Institusi - Gerejawi / Non Gerejawi") secara dinamis ke Program Studi Filsafat Keilahian (kode prodi `31`) di `QuestionSeeder.php`. Pertanyaan umum lainnya tetap bernilai `prodi_id = null`.
- **Filtering Backend Dinamis (Tanpa Hardcode Frontend)**: Menyesuaikan kueri pada `KuesionerController.php` dengan klausul `whereNull('prodi_id')->orWhere('prodi_id', $alumni->prodi_id)`. Alumni non-31 tidak akan menerima pertanyaan F2E, sedangkan alumni prodi 31 menerimanya secara otomatis tanpa modifikasi kondisional di Vue.
- **Integrasi Admin Biro 3**: Menambahkan dropdown target program studi dan visual badge khusus prodi pada panel kelola pertanyaan (`KelolaPertanyaanController.php` & `Index.vue`).
- **Migrate Fresh & Reseed**: Menjalankan `php artisan migrate:fresh --seed` secara bersih dan terverifikasi di seluruh tingkatan data.
- **Komentar & Dokumentasi**: Melengkapi PHPDoc dan komentar arsitektur di Model, Migrasi, Seeder, Controller, dan dokumen README.

## [2026-09-07] Penyempurnaan Tampilan Kuesioner (Tanpa Garis Shadow Tebal Bawah)
- **Restorasi Layout & Estetika**: Mengembalikan layout dan struktur kuesioner yang disukai (background hijau segar `#E8F5E9`, kartu rounded, stepper tahapan, floating round navigation buttons, dan tabel komparasi F17 berdampingan).
- **Penghapusan Garis Shadow Tebal Bawah**: Menghapus seluruh efek bayangan 3D bergaris tebal di bawah (`shadow-[0_8px_0_0_...]`, `shadow-[0_6px_0_0_...]`, `shadow-[0_4px_0_0_...]`) pada banner, kartu soal, pill options, stepper, dan tombol melayang sehingga tampilan bersih dan rapi.
- **Penghapusan Toast Gamifikasi**: Menghilangkan animasi toast pop-up ("HEBAT!", "MANTAP!", dll) saat pergantian bagian agar pengalaman pengisian kuesioner nyaman dan tidak mengganggu.

## [2026-09-07] Refactoring Jump Logic: Unifikasi ke `question_options.jump_to`
- **Drop Kolom `jump_logic`**: Menghapus kolom legacy `jump_logic` (JSON) dari tabel `questions` via migrasi baru `2026_09_07_000001_remove_jump_logic_from_questions_table.php`.
- **Pembersihan Model & Seeder**: Menghapus atribut `jump_logic` dari model `Question.php` dan `QuestionSeeder.php`.
- **Seeder `QuestionOptionSeeder`**: Memastikan seluruh opsi percabangan memiliki atribut `jump_to` yang lengkap dan presisi (F3-03 -> F8, F8-01 -> F11, F8-02 -> F9, F10-01 s/d F10-05 -> F17-1).
- **Refactoring Vue `Kuesioner.vue`**: Menghapus fallback `jump_logic` dan memastikan sistem percabangan pertanyaan murni membaca `jump_to` dari `QuestionOption` secara dinamis tanpa hardcode.
- **Pembaruan Dokumentasi**: Menyesuaikan dokumen `README.md` dan `documentation.txt` dengan arsitektur baru.

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
