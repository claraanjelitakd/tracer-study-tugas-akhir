# Update Log Tracer Study

## 4 September 2026
- **Fix**: Menambahkan relasi user pada eager loading Alumni::with() di DaftarAlumniController dan DetailAlumniController untuk mencegah error render Vue.
- **Feature**: Mendesain ulang Login.vue menggunakan referensi SIMASTER UGM (split screen, bg overlay, username/NIM).
- **Feature**: Mengubah indikator loading di Login.vue dari spinner tombol menjadi Popup Alert Vue yang interaktif.
- **UI/UX**: Mengubah tampilan global \pigo-loader.vue\ menjadi popup modern (glassmorphism) yang seragam dengan halaman login.
- **Bug Fix**: Memperbaiki navbar di halaman *Landing Page* (Tamu) yang menyebabkan *stuck* karena melempar *user* kembali ke \/login\. Tombol kini berubah cerdas menjadi 'Dashboard' dan mengarah ke _dashboard_ masing-masing role.
- **Keamanan**: Menambahkan konfigurasi \SESSION_EXPIRE_ON_CLOSE=true\ di \.env\ agar sesi/kuki otomatis dihapus saat browser ditutup (menyelesaikan masalah nyangkut login otomatis).
