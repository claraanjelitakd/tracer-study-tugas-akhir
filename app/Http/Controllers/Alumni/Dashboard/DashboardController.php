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
     */
    public function tampilkanDashboard(Request $request)
    {
        $user = $request->user();
        $alumni = Alumni::where('user_id', $user->id)->first();
        
        $jumlahRespon = 0;
        $profileCompleted = false;
        
        if ($alumni) {
            $jumlahRespon = Response::where('alumni_id', $alumni->id)->count();
            // Profil dianggap lengkap jika data alumni sudah diisi (terhubung)
            $profileCompleted = $alumni->is_profile_completed ?? true; 
        }
        
        $questionnaireCompleted = $jumlahRespon > 0;

        return Inertia::render('Alumni/Dashboard', [
            'user' => $user,
            'alumni' => $alumni,
            'responsesCount' => $jumlahRespon,
            'profileCompleted' => $profileCompleted,
            'questionnaireCompleted' => $questionnaireCompleted,
        ]);
    }
}

