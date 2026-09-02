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
# tracer-study
