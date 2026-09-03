<?php

namespace App\Http\Controllers\AdminBiroTiga\KelolaAlumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Alumni;
use App\Models\Prodi;

/**
 * DaftarAlumniController
 * 
 * Fungsi: Menampilkan daftar seluruh alumni dalam bentuk tabel/grid.
 * Tujuan: Memungkinkan admin Biro 3 untuk mencari, memfilter, dan melihat ringkasan data alumni.
 */
class DaftarAlumniController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Alumni
     */
    public function tampilkanDaftarAlumni(Request $request)
    {
        $pencarian = $request->input('search');
        $idProdi = $request->input('prodi_id');

        $alumni = Alumni::with(['dataAkademik', 'prodi', 'company', 'user'])
            ->when($pencarian, function ($query, $pencarian) {
                $query->whereHas('dataAkademik', function ($q) use ($pencarian) {
                    $q->where('nama', 'like', "%{$pencarian}%")
                      ->orWhere('nim', 'like', "%{$pencarian}%");
                })
                ->orWhere('linkedin_username', 'like', "%{$pencarian}%")
                ->orWhere('linkedin_url', 'like', "%{$pencarian}%");
            })
            ->when($idProdi, function ($query, $idProdi) {
                $query->where('prodi_id', $idProdi);
            })
            ->paginate(15)
            ->withQueryString();

        $daftarProdi = Prodi::all();

        return Inertia::render('AdminBiroTiga/AlumniIndex', [
            'alumnis' => $alumni,
            'prodis' => $daftarProdi,
            'filters' => $request->only(['search', 'prodi_id'])
        ]);
    }
}

