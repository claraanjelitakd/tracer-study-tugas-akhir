<?php

namespace App\Http\Controllers\SuperAdmin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * DashboardController
 * 
 * Fungsi: Menampilkan halaman utama untuk Superadmin.
 * Tujuan: Menyajikan keseluruhan ringkasan sistem, manajemen pengguna, dan status aplikasi.
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Superadmin
     */
    public function tampilkanDashboard()
    {
        return Inertia::render('SuperAdmin/Dashboard');
    }
}

