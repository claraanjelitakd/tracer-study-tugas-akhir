# Aturan Penamaan File (Case-Sensitivity Linux)

Untuk menghindari error saat dideploy ke server Linux (yang bersifat *case-sensitive*), ikuti aturan penamaan berikut setiap membuat file atau mengubah kode:

## 1. Frontend (Vue, JS, CSS)
- **Rekomendasi:** Gunakan huruf kecil dengan strip (`kebab-case`) untuk nama file dan folder baru. Contoh: `alumni-card.vue`.
- **Wajib (Import):** Saat melakukan `import`, pastikan *case* (huruf besar/kecil) **sama persis** dengan nama file asli yang ada di sistem (disk).
  - Benar: `import HeroSection from './HeroSection.vue'` (jika file bernama `HeroSection.vue`)
  - Salah: `import HeroSection from './herosection.vue'` (akan error di Linux).

## 2. Backend (Laravel, PHP)
- **DILARANG** menggunakan huruf kecil semua untuk class Laravel (seperti Controller, Model, Middleware, Seeder, Migration).
- Laravel memiliki standar **PSR-4 autoloading** yang mengharuskan nama file dan namespace **sama persis** (*PascalCase*).
- **Contoh Benar:** `app/Models/User.php`
- **Contoh Salah:** `app/models/user.php` -> Ini akan membuat aplikasi Laravel *CRASH* di Linux karena Class tidak ditemukan.
- Pastikan statment `use App\Models\User;` sama persis *case*-nya dengan struktur folder aslinya.
