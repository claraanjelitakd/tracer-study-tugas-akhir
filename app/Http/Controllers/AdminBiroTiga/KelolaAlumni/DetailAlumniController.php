<?php

namespace App\Http\Controllers\AdminBiroTiga\KelolaAlumni;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Inertia\Inertia;

/**
 * DetailAlumniController
 *
 * Fungsi: Menampilkan profil rinci dari seorang alumni.
 * Tujuan: Memungkinkan admin Biro 3 untuk meninjau kelengkapan profil, rekam jejak akademik,
 *         status kelulusan yudisium, dan riwayat karir LinkedIn.
 */
class DetailAlumniController extends Controller
{
    /**
     * Tampilkan Detail Alumni
     */
    public function tampilkanDetailAlumni($id)
    {
        $alumni = Alumni::with(['dataAkademik.yudisium', 'yudisium', 'prodi', 'company', 'user'])->findOrFail($id);

        return Inertia::render('AdminBiroTiga/AlumniShow', [
            'alumni' => $alumni,
        ]);
    }
}
