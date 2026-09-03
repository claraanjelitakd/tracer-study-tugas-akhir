<?php

namespace App\Http\Controllers\Tamu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * BerandaController
 * 
 * Fungsi: Menangani tampilan halaman utama (Landing Page) untuk tamu (guest) yang belum login.
 * Tujuan: Menyajikan informasi umum sistem Tracer Study sebelum pengguna masuk.
 */
class BerandaController extends Controller
{
    /**
     * Menampilkan Halaman Muka (Landing Page)
     */
    public function tampilkanBeranda()
    {
        return Inertia::render('Tamu/Beranda');
    }
}

