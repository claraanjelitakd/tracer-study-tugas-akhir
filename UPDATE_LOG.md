# UPDATE LOG - SERU (Sistem Ekosistem Rekam Jejak Alumni)

## [2026-09-09] Fitur Total Salary Reaktif, Format Pengisian Ribuan (Akhiran .000 Otomatis) & Perlindungan Kesalahan Data pada Kuesioner F13
- **Kalkulasi & Tampilan Total Salary Reaktif (Pertanyaan `multiple_number` / F13)**:
  - Menambahkan kartu ringkasan visual **Total Pendapatan (Total Salary)** dengan desain gradien hijau emerald yang elegan dan ikon finansial di bawah rincian pendapatan F13.
  - Nilai total dikalkulasi secara reaktif secara *real-time* saat alumni mengetikkan atau mengubah angka pada komponen gaji mana pun (Pekerjaan Utama, Lembur & Tips, Pekerjaan Lainnya).
  - Dilengkapi label `*Otomatis dihitung & tersimpan ke database`.
- **Format Input Satuan Ribuan dengan Suffix `.000` Otomatis**:
  - Mengubah input komponen pendapatan menjadi input group modern dengan prefix `Rp` di sisi kiri, angka rata kanan monospace tebal, dan badge suffix `.000` di sisi kanan.
  - Memberikan petunjuk pengisian yang jelas: `* Nominal diisi dalam satuan ribuan rupiah (akhiran .000 otomatis). Contoh: masukkan 5000 untuk Rp 5.000.000, atau 750 untuk Rp 750.000`.
  - Menambahkan konversi langsung di bawah masing-masing opsi: `Konversi: Rp 5.000.000`.
- **Proteksi Cerdas Pencegahan Kesalahan Data (*Anti Double-Multiplication*)**:
  - Frontend: Jika alumni mengetik angka sangat besar (>= 1.000.000, misal mengetik 5.000.000 karena belum terbiasa dengan akhiran .000), muncul peringatan instan: `⚠️ Nilai terbaca di atas Rp 1 Miliar. Jika maksud Anda Rp 5.000.000, cukup ketik 5000`.
  - Backend: `SimpanJawabanController.php` secara cerdas mendeteksi angka: jika `< 1.000.000` dikalikan 1000 ke Rupiah penuh, namun jika `>= 1.000.000` tetap disimpan apa adanya untuk mencegah data membengkak menjadi miliaran rupiah.
- **Penyimpanan & Pembaruan Otomatis Nilai Total di Database**:
  - Kolom `answer_json` kini menyimpan rincian nominal rupiah penuh per kode opsi plus key akumulasi `'total'` (contoh: `{"F13-01": 6500000, "F13-02": 750000, "F13-03": 0, "total": 7250000}`).
  - Kolom `answer_text` memuat rincian terformat rupiah beserta total pendapatan (`"Dari Pekerjaan Utama: Rp 6.500.000, ..., Total Pendapatan: Rp 7.250.000"`).
  - Jika alumni mengedit salah satu nilai pendapatan dan menyimpan kembali, total salary di database otomatis ter-update dan tersinkronisasi.
- **Normalisasi Nilai saat Memuat Kembali Kuesioner**:
  - `KuesionerController.php` dan `Kuesioner.vue` (`getInitialAnswers`) secara otomatis membagi 1000 nilai tersimpan di database agar saat alumni membuka kembali kuesioner, angka di dalam kotak input berakhiran `.000` tetap konsisten (misal: 6.500.000 tampil sebagai 6500).
- **Pengujian Otomatis (Feature Test)**:
  - Membuat `tests/Feature/AlumniKuesionerMultipleNumberTest.php` dengan 4 skenario uji (penyimpanan ribuan & kalkulasi total, pembaruan total saat data diubah, normalisasi ke ribuan saat kuesioner dimuat, dan proteksi input nominal penuh). Seluruh tes lolos 100% (25 assertions).
- **Penyempurnaan Tampilan Evaluasi Kompetensi F17 (Berdampingan Bersih & Anti-Overlap)**:
  - Menyederhanakan tampilan agar intuitif, profesional, dan bebas dari instruksi berlebihan (*no bloated AI text/emojis*).
  - Menghilangkan *sticky header* yang sebelumnya menyebabkan tombol pilihan baris terpotong/tertutup (*clipping bug*).
  - Struktur tabel komparasi berdampingan:
    - **Kolom (A) Kemampuan Diri Anda**: Taraf penguasaan kompetensi saat lulus dengan aksen hijau emerald dan skala 1 (Rendah) s/d 5 (Tinggi).
    - **Kolom Tengah**: Nama aspek kompetensi yang bersih, dilengkapi *pill indicator* minimalis (`A > B`, `A = B`, `A < B`) jika kedua kolom telah diisi.
    - **Kolom (B) Kontribusi Kampus UKDW**: Peran kurikulum dan perkuliahan almamater dengan aksen biru royal dan skala 1 (Rendah) s/d 5 (Tinggi).
  - Menjaga proporsi visual yang seimbang, *padding* leluasa, dan pengalaman pengisian yang lancar di berbagai ukuran layar.

## [2026-09-08] Standarisasi Dokumentasi Edukatif Kode & Pemetaan Eksplisit Backend-Inertia-Frontend
- **Penambahan Komentar Penjelasan Baris demi Baris pada Backend (Controller)**:
  - `Alumni/Dashboard/DashboardController.php`: Memberikan komentar penjelasan detail pada setiap baris logika pengecekan pengguna login, pencarian data alumni, penghitungan jumlah respon kuesioner dari database, pengecekan status kelengkapan profil, serta pemetaan paket data (props) ke Inertia.
  - `AdminBiroTiga/Dashboard/DashboardController.php`: Memberikan dokumentasi menyeluruh per-variabel pada seluruh metrik KPI utama (`totalAlumni`, `totalResponden`, `persentaseRespon`, `totalPertanyaan`, `totalProdi`, `alumniLinkedIn`), kalkulasi ringkasan partisipasi per-prodi (`prodiSummaries`), dan 5 alumni terbaru (`recentAlumni`).
- **Penambahan Dokumentasi Asal-Usul Data & Aksi pada Frontend (Vue)**:
  - `Alumni/Dashboard.vue`: Menjelaskan asal mula props dari controller, method logout dengan `router.post('/logout')`, serta rute tujuan untuk setiap tombol `<Link>` profil dan kuesioner.
  - `AdminBiroTiga/Dashboard.vue`: Mendokumentasikan setiap struktur objek props yang diterima (`stats`, `prodiSummaries`, `recentAlumni`) dan penggunaannya pada kartu metrik maupun tabel data.
  - **Arsitektur Parent-Child Component Profil Alumni**:
    - `Alumni/Profil/Index.vue` (Parent): Menjelaskan siklus hidup data mulai dari penerimaan props `formData` dari `ProfilController.php`, pembentukan `useForm`, distribusi ke komponen anak, hingga pengiriman data simpan ke `SimpanProfilController.php`.
    - `Components/FormAkademik.vue` (Child): Menjelaskan silsilah asal objek `form` yang dioper dari parent `Index.vue`, daftar field akademik database yang ditampung, serta alasan penguncian input resmi kampus (*read-only*).
    - `Components/FormPribadi.vue`, `Components/FormOrangTua.vue`, & `Components/FormKarier.vue` (Child): Mendokumentasikan peran masing-masing form anak dan operan props form serta master data wilayah dari controller.
- **Pembaruan Aturan Agen & Dokumentasi**:
  - Menambahkan aturan baru `.agents/rules/code-documentation-guidelines.md` yang mewajibkan komentar per-variabel di Controller dan komentar silsilah props/method di file Vue.
  - Memperbarui file `documentation.txt` pada bagian Step 9 mengenai alur arsitektur Backend-Inertia-Frontend dan hierarki Parent-Child.

- **Perbaikan Masalah Tombol Logout di Dashboard Alumni & Seluruh Modul**:
  - Mengidentifikasi akar masalah: pemanggilan composable `useForm().post('/logout')` di dalam callback function click handler Vue 3 menyebabkan error injeksi konteks (`inject() can only be used inside setup()`), sehingga request tidak terkirim.
  - Memperbaiki seluruh method logout di seluruh komponen aplikasi (`Alumni/Dashboard.vue`, `SuperAdmin/Dashboard.vue`, `SuperAdmin/Pertanyaan/Index.vue`, `AdminProdi/Dashboard.vue`, `AdminBiroTiga/Dashboard.vue`, `AdminBiroTiga/Pertanyaan/Index.vue`, `AdminBiroTiga/AlumniIndex.vue`, `AdminBiroTiga/AlumniShow.vue`) dengan menggunakan `router.post('/logout')` dari `@inertiajs/vue3`.
- **Dukungan Rute Logout Aman & Fleksibel**:
  - Mengubah rute logout di `routes/web.php` menjadi `Route::match(['get', 'post'], '/logout', ...)` agar dapat dipanggil baik via POST maupun GET.
  - Memastikan jika sesi pengguna telah kedaluwarsa saat tombol logout ditekan, pengguna tidak akan terkunci di redirect error melainkan langsung terarah kembali dengan mulus ke Beranda (Home / Landing Page `/`).
  - Menambahkan komentar dokumentasi lengkap pada method `LoginController::prosesLogout`.
- **Migrasi Modul Kelola Pertanyaan & Alur Percabangan ke Superadmin**:
  - Memindahkan seluruh fitur manajemen kuesioner dari Biro 3 ke Superadmin sesuai hak wewenang universitas.
  - Membuat controller baru `App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController` dengan komentar lengkap dan dokumentatif di setiap method.
  - Membuat halaman tampilan `resources/js/Pages/SuperAdmin/Pertanyaan/Index.vue` dengan navbar institusional Superadmin, navigasi section per-bagian (horizontal tabs), penyusunan urutan (reorder up/down), CRUD pertanyaan, dan alur percabangan (*Google Forms jump logic*).
  - Memperbarui rute di `routes/web.php` di bawah middleware `role:superadmin` (`/superadmin/pertanyaan`, `/superadmin/pertanyaan/reorder`, `/superadmin/pertanyaan/{id}`, opsi jawaban).
- **Pembaruan Dasbor Superadmin & Biro 3**:
  - Mengembangkan `SuperAdmin/DashboardController.php` dan `resources/js/Pages/SuperAdmin/Dashboard.vue` dengan ringkasan metrik instrumen, total pertanyaan, section, partisipasi responden, serta kartu gerbang langsung menuju modul kelola kuesioner.
  - Memperbarui `AdminBiroTiga/Dashboard.vue` dengan memfokuskan peran Biro 3 pada Manajemen Data Alumni, Sinkronisasi LinkedIn, dan Pemantauan Partisipasi Responden, serta menghapus tautan kelola pertanyaan dari navbar Biro 3.
- **Komentar Kode Menyeluruh**:
  - Menambahkan komentar deskriptif pada setiap rute, controller, dan komponen Vue terkait sesuai pedoman pengembangan.

## [2026-09-08] Fitur Penambahan Perusahaan Baru: SweetAlert2 Modal Popup Bersih, Validasi Wajib Wilayah & Kode Pos Langsung Simpan Database
- **Popup Tambah Perusahaan Menggunakan SweetAlert2 (Clean & Standar)**:
  - Menggantikan modal kustom dengan popup dialog **SweetAlert2** (`Swal.fire`) yang rapi, presisi, dan proporsional.
  - Bebas dari glitch overflow, terpotong, atau efek blur yang tidak diinginkan.
  - Dilengkapi input Nama Perusahaan (*), Dropdown Provinsi (*), Dropdown Kabupaten/Kota dinamis (*), Kode Pos (*), Skala, dan Alamat.
  - Pesan validasi interaktif ditampilkan langsung di dalam popup modal (`Swal.showValidationMessage`) tanpa menutup popup jika ada kolom wajib yang belum terisi.
- **Validasi Ketat Input Perusahaan Baru**:
  - Mewajibkan pengisian:
    1. **Nama Perusahaan / Instansi** (*)
    2. **Provinsi Perusahaan** (*)
    3. **Kabupaten / Kota Perusahaan** (*) (otomatis menyesuaikan provinsi yang dipilih)
    4. **Kode Pos Perusahaan** (*)
    5. Skala dan Alamat Jalan (opsional/pelengkap).
  - Mengimplementasikan validasi frontend dan backend untuk memastikan data lokasi perusahaan baru terisi lengkap meskipun status verifikasi adalah `Menunggu Verifikasi`.
- **Penyimpanan Langsung ke Database & Integrasi Otomatis**:
  - Menambahkan migrasi `2026_09_08_163000_add_kode_pos_to_companies_table.php` dan memperbarui model `Company.php` dengan kolom `kode_pos`.
  - Menambahkan route `POST /alumni/company` dan method controller `SimpanProfilController::tambahPerusahaanBaru(Request $request)` untuk menyimpan perusahaan baru ke database secara instan.
  - Setelah perusahaan berhasil disimpan:
    - Data perusahaan langsung ditambahkan ke daftar opsi dropdown (`localCompanies.unshift`).
    - Input form profil karier otomatis terpilih dan terisi dengan perusahaan baru tersebut (nama, provinsi, kabupaten/kota, alamat, skala, dan kode pos).
    - Menampilkan notifikasi sukses SweetAlert yang rapi dan menutup popup modal.
- **CSRF Token Protection**:
  - Menambahkan `<meta name="csrf-token" content="{{ csrf_token() }}">` pada layout `resources/views/app.blade.php` untuk memastikan seluruh request fetch aman dan terautentikasi.

## [2026-09-08] Penyempurnaan Profil Alumni: Penambahan NPWP, Pemisahan Data Paten Universitas vs Editable, Relasi Company Wilayah, & Kelengkapan Seeder 100%
- **Penambahan Kolom NPWP (Nomor Pokok Wajib Pajak)**:
  - Membuat migrasi baru `2026_09_08_160000_add_npwp_to_data_akademiks_table.php` untuk menambahkan kolom `npwp` (varchar 30, nullable) pada tabel `data_akademiks`.
  - Mendaftarkan `'npwp'` ke properti `$fillable` di model `DataAkademik.php`.
  - Menambahkan input NPWP pada kartu **Identitas Diri** di `FormPribadi.vue` dan mengintegrasikannya ke `ProfilController` serta `SimpanProfilController`.
- **Penghapusan Kolom Ekstra Dosen Pembimbing & Penguji**:
  - Kolom `dosen_pembimbing_3`, `dosen_penguji_3`, dan `dosen_penguji_4` yang tidak digunakan resmi dihapus dari skema database (`yudisiums`) dan migrasi. Dosen kini terstandarisasi menjadi Pembimbing 1, Pembimbing 2, Penguji 1, dan Penguji 2.
- **Seluruh Data Yudisium & Skripsi Resmi Paten (Tidak Dapat Diubah Alumni)**:
  - Data yudisium (Judul TA Indonesia & Inggris, Repositori/URL Publikasi, Jenis/Status Publikasi, Dosen Pembimbing 1-2, Dosen Penguji 1-2, Predikat Yudisium, dan Status Yudisium) ditarik resmi dari pangkalan data kampus dan berstatus paten. Seluruh input disajikan dalam mode terkunci (disabled abu-abu) dan di-bypass dari logika penyimpanan di `SimpanProfilController.php`.
- **Pembersihan Tampilan Non-AI Slop (Minimalis, Profesional, & Bersih)**:
  - Menghapus seluruh badge ikon gembok `🔒 Paten Universitas`, tulisan pengumuman panjang, dan dekorasi blur berlebihan.
  - Seluruh field paten / terkunci disajikan dengan estetika institusional bersih: input abu-abu halus (`bg-gray-100 text-gray-700 border-gray-200 cursor-not-allowed rounded-xl`) tanpa ornamen visual yang berisik.
- **Searchable Select Dropdown Perusahaan, Provinsi, & Kabupaten (Bergaya Gambar 2)**:
  - Mengubah input nama perusahaan menjadi **Searchable Select Dropdown** yang dapat diklik langsung kapan saja untuk membuka daftar perusahaan.
  - Dropdown dilengkapi dengan kotak pencarian sticky di atas (`🔍 Ketik untuk mencari...`) dan daftar opsi yang scrollable dengan penanda aktif hijau lembut (`bg-green-50 text-[#005B3C] font-semibold`), persis seperti pada Gambar 2.
  - Tetap menyediakan tombol `+ Tambah` di samping kanan input untuk membuka modal penambahan perusahaan baru jika instansi belum terdaftar di database.
  - Menerapkan pola searchable select yang sama pada pilihan Provinsi Perusahaan dan Kabupaten/Kota Perusahaan dengan penutup otomatis saat klik di luar (click outside).
  - **Data yang Dapat Diubah Alumni**:
    - Biodata diri (Nama, NIK, NPWP, Tempat/Tgl Lahir, Agama, Jenis Kelamin, Golongan Darah, Kewarganegaraan).
    - Kontak & Alamat Domisili lengkap (Telepon, Email, Alamat, Provinsi, Kabupaten/Kota, Kecamatan, Kelurahan, Kode Pos).
    - Skripsi, Tugas Akhir, Repositori, Publikasi Ilmiah, Dosen Pembimbing & Penguji (`FormAkademik.vue`).
    - Data lengkap Orang Tua / Wali (`FormOrangTua.vue`).
    - Karier, Perusahaan, Atasan, dan Media Sosial (`FormKarier.vue`).
- **Sinkronisasi Kunci Form Data Orang Tua & Akademik**:
  - Memperbaiki ketidaksesuaian kunci (mismatch key) di `FormOrangTua.vue` (`nama_orang_tua`, `pekerjaan_orang_tua`, `nomor_telepon_orang_tua`, dll) dan `FormAkademik.vue` (`angkatan_masuk`, `dosen_pembimbing_1`, `dosen_penguji_1`, `keterangan_hasil_yudisium`) agar data tersinkronisasi 100% dua arah dengan backend.
- **Relasi Wilayah Perusahaan (Company) Terhubung Lewat ID**:
  - `CompanySeeder.php` diperbarui untuk menempatkan perusahaan di 3 wilayah spesifik:
    1. **DI Yogyakarta**: Kabupaten **Sleman** (ID provinsi DIY kode `34`, ID kabupaten Sleman kode `34.04`). Perusahaan: *PT Gameloft Indonesia*, *PT Niagahoster*, *PT Djarum Sleman*, *CV Javan Cipta Solusi*.
    2. **DKI Jakarta**: **Kota Jakarta Pusat** (ID provinsi DKI Jakarta kode `31`, ID kabupaten Kota Jakarta Pusat kode `31.71`). Perusahaan: *PT Bank Central Asia Tbk*, *PT Telekomunikasi Indonesia Tbk*, *PT Tokopedia*, *PT Astra International Tbk*.
    3. **Aceh**: Kabupaten **Aceh Selatan** (ID provinsi Aceh kode `11`, ID kabupaten Aceh Selatan kode `11.01`). Perusahaan: *PT Perkebunan Nusantara I (PTPN)*, *PT Bank Aceh Syariah Tapaktuan*, *CV Samudera Selatan Digital*.
  - Seluruh relasi `province_id` dan `kabupaten_id` terhubung via foreign key yang valid.
- **Kelengkapan Seeder Data Alumni 100% Rata Terisi**:
  - `DataAkademikSeeder.php`: Mengisi 100% data untuk seluruh 10 alumni (termasuk NIK, KK, NISN, BPJS, NPWP, domisili lengkap terhubung ke ID wilayah, dan data paten kelulusan).
  - `DataOrangTuaSeeder.php`: Mengisi lengkap data orang tua untuk seluruh 10 alumni.
  - `YudisiumSeeder.php`: Mengisi lengkap data skripsi bahasa Indonesia & Inggris, repositori, jenis & status publikasi, nama dosen pembimbing & penguji, serta predikat kelulusan untuk seluruh 10 alumni.
  - `AlumniSeeder.php`: Mengaitkan `company_id` (terdistribusi ke Sleman, Jakarta Pusat, dan Aceh Selatan), data atasan (`atasan_id`), posisi jabatan, keahlian (`expert`), minat (`minat`), dan tautan media sosial.
  - `DatabaseSeeder.php`: Mengatur urutan pemanggilan seeder agar `CompanySeeder` dijalankan sebelum `AlumniSeeder`.

- **Tata Letak Kartu Kotak Berdampingan (Kanan-Kiri) Khusus Pertanyaan F6 & F7**:
  - Mengelompokkan kartu pertanyaan `F6` ("Berapa perusahaan dilamar?") dan `F7` ("Berapa perusahaan merespons?") menjadi grid 2 kolom berdampingan (`grid grid-cols-1 md:grid-cols-2 gap-6`).
  - Masing-masing disajikan dalam kartu kotak `rounded-[2rem] shadow-sm` proporsional dengan input angka besar di tengah sehingga tampilan lebih rapi, hemat ruang vertikal, dan ergonomis.
- **Dukungan Input Dinamis untuk Opsi "Lainnya / Tuliskan" di Semua Tipe Pilihan**:
  - Menyediakan kotak isian teks (`input text`) dinamis yang otomatis muncul saat alumni mencentang atau memilih opsi jawaban yang mengandung kata `"Lainnya"` atau `"Tuliskan"` pada tipe `multiple_choice` / `checkbox` maupun `single_choice` / `radio`.
  - Input teks terintegrasi langsung ke state pengiriman jawaban `SimpanJawabanController` dan tersimpan rapi ke tabel `responses`.
- **Pembersihan Teks Pertanyaan F10 & Verifikasi Database Seeding**:
  - Menghapus teks instruksi legacy `"KEMUDIAN LANJUT KE F17"` dari database. Teks pertanyaan `F10` di database dan seeder kini bersih murni: `"Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir? Pilihlah Satu Jawaban."`.
  - Memverifikasi bahwa seluruh alur branching `jump_to` (lompat ke `F17-1` atau lanjut normal) dikendalikan 100% dari kolom `jump_to` di tabel `question_options` di database tanpa hardcode teks di frontend maupun seeder.

## [2026-09-08] Perbaikan Hover Pilihan Kuesioner, Eliminasi Border Kasar, & Persistensi State/Draft saat Refresh Halaman
- **Perbaikan Visual Opsi Pilihan Saat Hover (Anti-Whiteout)**:
  - Mengatasi masalah teks dan placeholder input di dalam opsi pilihan kuesioner yang memutih saat kursor mouse di-hover (seperti pada pertanyaan F3).
  - Mengganti utilitas CSS bentrok Tailwind (`peer-checked:bg-[#005B3C] peer-checked:text-white hover:bg-green-50`) dengan binding reaktif Vue murni.
  - Opsi belum terpilih: latar `bg-gray-50/90 text-gray-700 hover:bg-emerald-50 hover:text-[#005B3C]`.
  - Opsi terpilih: latar `bg-[#005B3C] text-white shadow-md hover:bg-[#00482f]`.
  - Kotak input dinamis pada tipe `radio_input`: Latar putih kontras tinggi `bg-white text-gray-900 placeholder-gray-400 font-bold border-0 shadow-inner` sehingga teks input dan placeholder terbaca dengan sangat jelas.
- **Penghapusan Border Kasar (Clean & Modern UI)**:
  - Menghapus border tebal (`border-4`, `border-2`) pada kartu pertanyaan, header banner, stepper navigasi bagian, dan floating action button di halaman kuesioner alumni.
  - Tampilan diganti dengan *subtle shadow* (`shadow-sm`, `shadow-xs`) dan sudut halus (`rounded-[2rem]`) yang bersih dan rapi.
- **Persistensi Penuh Tampilan & Draft Saat Refresh Halaman (Tidak Pindah Tampilan & Data Tidak Hilang)**:
  - **Kuesioner Alumni (`/alumni/kuesioner`)**:
    - Menyimpan section kuesioner aktif ke URL query (`?sec=...`) dan `localStorage`. Saat halaman di-refresh (F5), alumni tetap berada di bagian/section yang sedang dikerjakan, tidak terlempar ke Section 1.
    - Menyimpan draft jawaban yang sedang diisi secara real-time (*debounced* 250ms) ke `localStorage`. Jika halaman di-refresh sebelum tombol simpan ditekan, seluruh draft isian tidak akan hilang.
    - Menambahkan `preserveState: true` dan `preserveScroll: true` pada seluruh navigasi step.
  - **Profil Alumni (`/alumni/profile`)**:
    - Menyimpan tab aktif (`pribadi`, `akademik`, `orangtua`, `karier`) ke URL query (`?tab=...`) dan `localStorage`. Saat di-refresh, pengguna tetap berada di tab yang sama.
    - Menyimpan draft input perubahan form profil ke `localStorage` agar data yang belum tersubmit tidak hilang saat halaman di-refresh secara tidak sengaja.
  - **Kelola Pertanyaan Biro 3 (`/biro3/pertanyaan`)**:
    - Menyimpan ID section aktif ke URL query (`?sec_id=...`) dan `localStorage`. Saat admin me-refresh halaman, section yang sedang dikelola tidak berpindah kembali ke Bagian 1.

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
## [2026-09-08] Standarisasi Format Data Tabel responses & Sinkronisasi Ekspor
- **Standarisasi Kolom `responses`**:
  - `answer_json`: Dikhususkan menyimpan JSON array murni untuk pertanyaan pilihan ganda (`multiple_choice`/`checkbox`), associative array/object untuk `radio_input` dan `multiple_number`, serta null untuk pertanyaan berjawaban tunggal.
  - `answer_text`: Dikhususkan menyimpan format string/varchar bersih yang siap dibaca dan diekspor (Excel/CSV) tanpa perlu parsing JSON di setiap baris laporan.
- **Dukungan Opsi "Lainnya"**:
  - Jika alumni memilih opsi "Lainnya" dan mengetikkan teks sendiri:
    - Pada pertanyaan `single_choice`/`radio`: tersimpan sebagai varchar rapi `"Lainnya: [isi teks]"` di `answer_text`.
    - Pada pertanyaan `multiple_choice`/`checkbox`: teks dimasukkan ke dalam JSON array di `answer_json` (`["Opsi 1", "Lainnya: [isi teks]"]`) dan digabungkan dengan koma di `answer_text` (`"Opsi 1, Lainnya: [isi teks]"`).
- **Format Khusus `radio_input` & `multiple_number`**:
  - Pertanyaan `radio_input` (seperti F3 dan F5): menghasilkan kalimat utuh di `answer_text` (misal: `"Kira-kira 4 bulan sebelum lulus"`).
  - Pertanyaan `multiple_number` (seperti F13): menghasilkan rincian nominal rupiah terformat dengan label opsi di `answer_text` (misal: `"Dari Pekerjaan Utama: Rp 6.500.000, Dari Lembur dan Tips: Rp 750.000, Dari Pekerjaan Lainnya: Rp 0"`).
- **Sinkronisasi Otomatis Pertanyaan Profil (F1 s/d F2H)**:
  - Pertanyaan F1 s/d F2H yang ditarik dari profil alumni (`data_akademiks`, `alumnis`, `companies`, `atasans`) otomatis tersinkronisasi dan tersimpan di tabel `responses` untuk setiap alumni melalui `KuesionerSyncService`.
  - `QuestionMappingSeeder` diperbarui sehingga saat seeding dijalankan, tabel `responses` langsung terisi lengkap untuk seluruh data alumni.
- **Prefill Dua Arah (*Roundtrip Persistence*)**:
  - `KuesionerController.php` diperbarui agar dapat memecah kembali nilai `"Lainnya: [teks]"` ke state form frontend (`_custom`), memastikan data tidak hilang atau rusak saat halaman direfresh atau dibuka kembali.
- **Isolasi Input Mandiri pada Tipe `radio_input` (F3 & F5)**:
  - Memperbaiki model input pada kartu bertipe `radio_input` agar terikat ke `inputs[opt.id]` per opsi (bukan satu variabel `input` global). Mengeliminasi bug di mana angka yang diketik di opsi "Sebelum lulus" otomatis ikut terisi ke opsi "Sesudah lulus" saat berganti pilihan.
- **Eliminasi Mutlak Nilai NULL pada Tabel `responses`**:
  - `SimpanJawabanController.php` kini menyaring pertanyaan yang belum dijawab / belum sampai di section aktif sehingga tidak ada lagi record kosong (`answer_text: null, answer_json: null`) yang tersimpan ke database.
  - Nilai rincian numerik (F13) yang tidak diisi otomatis terisi angka `0` (bukan `null`).
  - Membersihkan seluruh record dummy yang sebelumnya bernilai null di database sehingga tabel `responses` bersih 100% dari nilai null.

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
