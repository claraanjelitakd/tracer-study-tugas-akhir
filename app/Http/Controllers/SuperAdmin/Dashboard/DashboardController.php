<?php

namespace App\Http\Controllers\SuperAdmin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Alumni;
use App\Models\Question;
use App\Models\QuestionSection;
use App\Models\Response;
use App\Models\Prodi;

/**
 * Class DashboardController (SuperAdmin)
 * 
 * Fungsi:
 * Menampilkan halaman dasbor eksekutif utama untuk Superadmin.
 * 
 * Tujuan:
 * Menyajikan gambaran umum ekosistem Tracer Study secara menyeluruh,
 * mencakup status instrumen kuesioner, total responden alumni,
 * serta akses cepat menuju modul pengelolaan kuesioner dan pengaturan sistem.
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Superadmin beserta metrik ringkasan sistem.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function tampilkanDashboard(Request $request)
    {
        $user = $request->user();

        // 1. Menghitung ringkasan statistik kuesioner dan partisipasi
        $totalPertanyaan = Question::count();
        $totalSections = QuestionSection::count();
        $totalAlumni = Alumni::count();
        $totalResponden = Response::distinct('alumni_id')->count('alumni_id');
        $totalProdi = Prodi::count();

        // 2. Mengembalikan view dasbor Superadmin
        return Inertia::render('SuperAdmin/Dashboard', [
            'user' => $user,
            'stats' => [
                'total_pertanyaan' => $totalPertanyaan,
                'total_sections' => $totalSections,
                'total_alumni' => $totalAlumni,
                'total_responden' => $totalResponden,
                'total_prodi' => $totalProdi,
            ],
        ]);
    }
}
