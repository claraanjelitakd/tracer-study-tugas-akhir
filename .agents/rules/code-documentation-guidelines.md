# Standar Dokumentasi Kode & Edukasi (Komentar Baris demi Baris)

Agar kode mudah dipelajari, dipahami, dan dijelaskan saat sidang tugas akhir, setiap perubahan atau pembuatan kode baru WAJIB mematuhi standar dokumentasi dan komentar berikut:

## 1. Backend (Controller Laravel)
- **Header Method**: Cantumkan penjelasan fungsi method, pasangan file Vue yang dirender (`Inertia::render('...')`), dan rute URL yang mengarah ke method tersebut.
- **Komentar per Variabel**: Setiap variabel penting (terutama query Eloquent, perhitungan KPI/metrik, dan penyiapan data) WAJIB diberikan komentar di atas barisnya yang menjelaskan:
  - Apa isi variabel tersebut.
  - Tabel database apa yang diakses (`alumnis`, `responses`, `prodis`, dll).
  - Mengapa perhitungan tersebut dilakukan.
- **Inertia Props**: Pada bagian `return Inertia::render('Folder/NamaHalaman', [...])`, jelaskan setiap *key-value* props yang dikirimkan ke Vue beserta tipe data dan tujuannya di antarmuka.

## 2. Frontend (Komponen Halaman Vue - Parent)
- **Header File**: Cantumkan nama controller backend yang merender halaman tersebut dan URL rutenya.
- **defineProps**: Jelaskan setiap properti yang diterima dari controller backend (asal usul datanya dari baris controller mana).
- **Aksi & Fungsi (Method)**: Setiap fungsi JavaScript (seperti `logout`, `submit`, `form.post(...)`, `router.post(...)`) harus memiliki komentar yang menjelaskan:
  - Kapan fungsi itu dipanggil (misal: saat tombol diklik).
  - URL endpoint tujuan request.
  - Controller backend mana yang menangani dan memproses data tersebut.

## 3. Frontend (Komponen Anak / Child Components)
- **Silsilah Props**: Untuk komponen pecahan (misal: `FormAkademik.vue`, `FormPribadi.vue`):
  - Jelaskan siapa komponen induk (Parent) yang memanggil dan mengoper props (misal: `<FormAkademik :form="form" />` dari `Index.vue`).
  - Jelaskan asal usul asli data tersebut dari backend (misal: controller `ProfilController.php`).
  - Rincikan kolom-kolom data penting yang ditampung oleh objek `form` tersebut.
