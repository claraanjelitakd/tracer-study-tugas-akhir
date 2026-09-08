<?php

namespace App\Http\Controllers\Alumni\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Alumni;
use App\Models\Response;

/**
 * DashboardController
 * 
 * Fungsi: Menampilkan halaman utama (Dashboard) untuk pengguna dengan peran (role) Alumni.
 * Tujuan: Menyajikan ringkasan aktivitas alumni, seperti jumlah kuesioner yang sudah diisi, serta status kelengkapan profil.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard Alumni
     * 
     * Hubungan dengan Frontend:
     * Method ini adalah pasangan dari file: resources/js/Pages/Alumni/Dashboard.vue
     * Data yang ada di dalam array Inertia::render(...) akan otomatis diterima oleh Dashboard.vue
     */
    public function tampilkanDashboard(Request $request)
    {
        // Baris 1: Ambil data user yang sedang login saat ini dari sesi autentikasi
        $user = $request->user();

        // Baris 2: Cari data alumni di tabel 'alumnis' yang memiliki 'user_id' sama dengan user yang login
        $alumni = Alumni::where('user_id', $user->id)->first();
        
        // Baris 3: Buat variabel default untuk menghitung jumlah kuesioner yang sudah diisi (awal mula = 0)
        $jumlahRespon = 0;

        // Baris 4: Buat variabel default penanda profil lengkap (awal mula = false / belum lengkap)
        $profileCompleted = false;
        
        // Baris 5: Cek apakah data alumni tersebut ditemukan di database
        if ($alumni) {
            // Baris 6: Hitung berapa banyak baris jawaban kuesioner yang dimiliki alumni ini di tabel 'responses'
            $jumlahRespon = Response::where('alumni_id', $alumni->id)->count();

            // Baris 7: Cek kolom 'is_profile_completed' di tabel alumni (jika true berarti profil sudah lengkap)
            $profileCompleted = $alumni->is_profile_completed ?? true; 
        }
        
        // Baris 8: Tentukan apakah kuesioner sudah selesai diisi.
        // Jika $jumlahRespon lebih dari 0 (> 0), maka bernilai true (selesai), jika 0 maka bernilai false (belum selesai)
        $questionnaireCompleted = $jumlahRespon > 0;

        // Baris 9: Buka file Frontend "resources/js/Pages/Alumni/Dashboard.vue"
        // sekaligus MENGIRIMKAN paket data (props) di bawah ini ke Vue:
        return Inertia::render('Alumni/Dashboard', [
            'user'                   => $user,                   // Dikirim ke Vue: Data akun user login (nama, email, role)
            'alumni'                 => $alumni,                 // Dikirim ke Vue: Data detail profil alumni (NIM, dll)
            'responsesCount'         => $jumlahRespon,           // Dikirim ke Vue: Angka total jawaban kuesioner
            'profileCompleted'       => $profileCompleted,       // Dikirim ke Vue: Boolean (true/false) status profil
            'questionnaireCompleted' => $questionnaireCompleted, // Dikirim ke Vue: Boolean (true/false) status kuesioner
        ]);
    }
}

