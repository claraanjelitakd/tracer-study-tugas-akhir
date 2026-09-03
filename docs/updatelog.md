# Changelog Tracer Study

Semua perubahan besar pada sistem dicatat dalam dokumen ini.

## [2026-09-04] - Restrukturisasi Arsitektur & Pembersihan Logika
**Arsitektur Kode & Controller:**
- Menghapus semua fungsi `Closure` di `routes/web.php` untuk memisahkan *Routing* secara murni.
- Membuat struktur folder Controller bersarang (Nested) per Aktor/User, lalu dipecah per-Fungsi.
- Menerapkan penamaan `UpperCamelCase` dan **Bahasa Indonesia** untuk semua folder, class, dan fungsi di *Controller*.
- Memisahkan Controller Monolitik (`AuthController`, `AlumniProfileController`, `QuestionnaireController`, `Biro3\AlumniController`) menjadi Controller berprinsip *Single Responsibility*.

**Daftar Controller Baru (Bahasa Indonesia):**
- `App\Http\Controllers\Tamu\BerandaController`
- `App\Http\Controllers\Otentikasi\LoginController`
- `App\Http\Controllers\Otentikasi\UbahKataSandiController`
- `App\Http\Controllers\Alumni\Dashboard\DashboardController`
- `App\Http\Controllers\Alumni\Profil\ProfilController`
- `App\Http\Controllers\Alumni\Kuesioner\KuesionerController`
- `App\Http\Controllers\Alumni\Kuesioner\SimpanJawabanController`
- `App\Http\Controllers\AdminBiroTiga\Dashboard\DashboardController`
- `App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DaftarAlumniController`
- `App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DetailAlumniController`
- `App\Http\Controllers\AdminBiroTiga\KelolaAlumni\SinkronisasiLinkedinController`
- `App\Http\Controllers\AdminProdi\Dashboard\DashboardController`
- `App\Http\Controllers\SuperAdmin\Dashboard\DashboardController`

**Frontend (Vue):**
- Membersihkan `resources/js/Pages/alumni/kuesioner/index.vue` dari logika JavaScript yang rumit (GSAP, Jump Logic) untuk menaati aturan "Zero Logic Frontend". Data disiapkan 100% matang dari Backend.
- Membersihkan `resources/js/Pages/alumni/profile/index.vue` dari logika animasi GSAP.
- Menambahkan komentar dokumentasi di *Frontend* yang mengindikasikan larangan penulisan `const` dan fungsi JS secara ekstensif.

**Dokumentasi:**
- Semua *Controller* baru dilengkapi dengan DocBlocks (Komentar terstruktur) dalam bahasa Indonesia untuk memudahkan pemeliharaan kode.
- File `docs/changelog.md` ini dibuat untuk melacak pembaruan di masa mendatang.
