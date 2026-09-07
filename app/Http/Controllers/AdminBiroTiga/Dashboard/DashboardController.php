<?php

namespace App\Http\Controllers\AdminBiroTiga\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Alumni;
use App\Models\Prodi;
use App\Models\Question;
use App\Models\Response;

/**
 * DashboardController (Biro 3)
 * 
 * Fungsi: Menyajikan dashboard eksekutif resmi untuk Administrator Biro 3 (Biro Kemahasiswaan, Alumni, dan Pengembangan Karir).
 * Tujuan: Memberikan ringkasan indikator kinerja utama (KPI) pelacakan alumni, tingkat partisipasi pengisian kuesioner,
 *         serta pintu gerbang navigasi utama ke pengelolaan data alumni dan instrumen pertanyaan.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard Utama Biro 3
     */
    public function tampilkanDashboard(Request $request)
    {
        $user = $request->user();

        // 1. Perhitungan Metrik KPI Utama
        $totalAlumni = Alumni::count();
        $totalResponden = Response::distinct('alumni_id')->count('alumni_id');
        $persentaseRespon = $totalAlumni > 0 ? round(($totalResponden / $totalAlumni) * 100, 1) : 0;
        $totalPertanyaan = Question::count();
        $totalProdi = Prodi::count();
        $alumniLinkedIn = Alumni::where(function ($q) {
            $q->whereNotNull('linkedin_url')
              ->where('linkedin_url', '!=', '')
              ->orWhereNotNull('linkedin_username')
              ->where('linkedin_username', '!=', '');
        })->count();

        // 2. Ringkasan Statistik Partisipasi per Program Studi
        $prodiSummaries = Prodi::withCount('alumnis')
            ->orderBy('kode_prodi', 'asc')
            ->get()
            ->map(function ($prodi) {
                $respondenCount = Alumni::where('prodi_id', $prodi->id)
                    ->whereHas('responses')
                    ->count();

                $rate = $prodi->alumnis_count > 0 
                    ? round(($respondenCount / $prodi->alumnis_count) * 100, 1) 
                    : 0;

                return [
                    'id' => $prodi->id,
                    'kode_prodi' => $prodi->kode_prodi,
                    'nama_prodi' => $prodi->nama_prodi,
                    'total_alumni' => $prodi->alumnis_count,
                    'total_responden' => $respondenCount,
                    'response_rate' => $rate,
                ];
            });

        // 3. Data Alumni Terbaru untuk Pratinjau Cepat
        $recentAlumni = Alumni::with(['prodi', 'dataAkademik', 'user'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($alumni) {
                return [
                    'id' => $alumni->id,
                    'nim' => $alumni->user->username ?? $alumni->nim,
                    'nama' => $alumni->dataAkademik->nama ?? $alumni->user->name ?? 'Belum terisi',
                    'prodi' => $alumni->prodi->nama_prodi ?? '-',
                    'has_linkedin' => !empty($alumni->linkedin_url) || !empty($alumni->linkedin_username),
                    'has_responded' => $alumni->responses()->exists(),
                ];
            });

        return Inertia::render('AdminBiroTiga/Dashboard', [
            'user' => $user,
            'stats' => [
                'total_alumni' => $totalAlumni,
                'total_responden' => $totalResponden,
                'persentase_respon' => $persentaseRespon,
                'total_pertanyaan' => $totalPertanyaan,
                'total_prodi' => $totalProdi,
                'alumni_linkedin' => $alumniLinkedIn,
            ],
            'prodiSummaries' => $prodiSummaries,
            'recentAlumni' => $recentAlumni,
        ]);
    }
}
