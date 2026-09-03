<?php

namespace App\Http\Controllers\AdminProdi\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * DashboardController
 * 
 * Fungsi: Menampilkan halaman utama untuk Admin Program Studi.
 * Tujuan: Menyajikan statistik atau grafik terkait kelulusan mahasiswa per prodi.
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Admin Prodi
     */
    public function tampilkanDashboard()
    {
        return Inertia::render('AdminProdi/Dashboard');
    }
}

