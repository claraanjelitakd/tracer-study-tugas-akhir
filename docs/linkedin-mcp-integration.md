# Integrasi LinkedIn MCP (Sinkronisasi Data Alumni)

Dokumen ini menjelaskan alur kerja, batasan, dan cara penggunaan fitur Sinkronisasi Data LinkedIn untuk Admin Biro 3 pada aplikasi Tracer Study Alumni.

## 1. Tujuan Fitur
Memudahkan Admin Biro 3 untuk memperbarui data pekerjaan saat ini (*Current Job*) dan perusahaan (*Current Company*) dari alumni dengan mengambil data langsung dari profil LinkedIn mereka.

## 2. Cara Kerja Sinkronisasi
Aplikasi Laravel tidak melakukan web scraping secara langsung. Proses ini didelegasikan ke **LinkedIn MCP Server** (Model Context Protocol) melalui sebuah *wrapper script* Python (`scripts/linkedin_mcp_client.py`).
Laravel akan memanggil script ini menggunakan *subprocess* PHP. Script Python akan berkomunikasi dengan instance lokal LinkedIn menggunakan pustaka `linkedin-mcp-server` (yang menggunakan *patchright* di bawah tenda) untuk mengambil data JSON/Markdown dari profil alumni.

## 3. Alur Admin Biro 3
1. **Login Biro 3**: Admin masuk menggunakan kredensial `admin_biro3`.
2. **Halaman Data Alumni**: Admin langsung diarahkan ke halaman tabel daftar alumni.
3. **Pencarian/Filter**: Admin dapat mencari berdasarkan Nama, NIM, atau Username LinkedIn.
4. **Detail Alumni**: Klik tombol "Detail" pada salah satu alumni.
5. **Sinkronisasi**: Jika alumni memiliki data `linkedin_url` atau `linkedin_username`, tombol **Sinkronkan Data LinkedIn** akan muncul.
6. **Preview**: Setelah tombol diklik, data baru dari LinkedIn akan ditampilkan sebagai *Preview*.
7. **Simpan/Batal**: Admin dapat memutuskan untuk **Simpan Perubahan** ke database atau menekan **Batal** jika data dianggap tidak valid.

## 4. Cara Login LinkedIn Manual
Laravel **tidak menyimpan kredensial LinkedIn Anda**. Karena sistem MCP menggunakan browser automation secara lokal:
1. Pastikan script python atau aplikasi MCP sudah dijalankan di mesin server/lokal Anda minimal sekali.
2. Saat pertama kali dijalankan, sistem MCP mungkin memerlukan autentikasi. Anda dapat membuka antarmuka lokal dari MCP (seperti VNC Viewer bawaan MCP) untuk melakukan proses login LinkedIn secara manual di *browser instance* yang dikendalikan oleh MCP.
3. Setelah login manual berhasil (cookies tersimpan di profil *patchright* OS), Laravel dapat memanggil MCP dengan mulus tanpa intervensi.

## 5. Data LinkedIn yang Digunakan
Untuk tahap ini, data yang diekstrak dibatasi pada:
- **Current Job Title** (Diambil dari headline atau experience teratas).
- **Current Company**
- **Location** (Lokasi umum dari headline).
- **Industry** (Industri tempat alumni bekerja).

## 6. Mapping Data LinkedIn ke Database
| Data LinkedIn | Database Tabel & Kolom | Keterangan |
| --- | --- | --- |
| `current_job` | `alumnis.expert` | Diupdate langsung. |
| `current_company` | `companies.nama_perusahaan` | Jika belum ada, dibuat *record* baru di tabel `companies`, lalu ID-nya disimpan ke `alumnis.company_id`. |
| `industry` | `companies.sektor` | Disimpan pada saat pembuatan *company* baru. |
| `location` | `companies.alamat` (optional) | Untuk sementara tidak dipetakan ke provinsi/kabupaten demi menghindari duplikasi yang keliru. |

## 7. Cara Menjalankan/Test Fitur
1. Pastikan Anda memiliki *environment* Python dan sudah menginstal *package* MCP:
   ```bash
   pip install mcp-server-linkedin
   ```
2. Pastikan file `scripts/linkedin_mcp_client.py` sudah ada di root project.
3. Login sebagai Admin Biro 3 di aplikasi Laravel.
4. Pilih alumni yang memiliki URL LinkedIn.
5. Klik **Sinkronkan Data LinkedIn**.
6. Amati *loading state*, dan periksa apakah *Preview* berhasil muncul.

## 8. Error yang Mungkin Terjadi
- **Timeout**: Jika MCP gagal mengekstrak data dalam waktu 60 detik (default timeout PHP `Process`).
- **Unauthenticated**: Jika session LinkedIn di browser MCP telah *expired*, Anda harus login manual kembali via antarmuka MCP.
- **Profile Not Found**: Terjadi jika username/URL LinkedIn yang dimasukkan alumni tidak valid atau akun LinkedIn mereka tidak publik/sudah dihapus.

## 9. Batasan Fitur Saat Ini
- Hanya bekerja satu-per-satu (*single sync*), tidak ada *bulk sync*.
- Tidak menggunakan token API LinkedIn resmi (menggunakan scraping bot lokal).
- Tidak menyimpan seluruh riwayat pendidikan atau riwayat pekerjaan masa lalu, hanya fokus pada kondisi *Current*.
