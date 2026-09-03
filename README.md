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
2. **Jumping Logic**: Sistem mampu melompat antar bagian (*sections*) jika alumni memberikan jawaban tertentu. Logika ini di-handle langsung di tabel `questions` via kolom JSON `jump_logic`. (Contoh: memilih 'Tidak bekerja' akan melewati pertanyaan tentang detail gaji).
3. **Data Type Parsing**: Komponen UI di Vue otomatis menyesuaikan tampilan berdasarkan `type` pertanyaan di database (contoh: `text`, `number`, `radio_input`, `matrix`, dll) serta memblokir validasi karakter non-numerik secara *native*.
4. **Data Response**: Semua jawaban alumni akan di-*serialize* ke dalam tabel `responses` dan dapat ditarik untuk keperluan Analitik Dasbor Admin Prodi maupun ekspor Excel/PDF.

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
# tracer-study
