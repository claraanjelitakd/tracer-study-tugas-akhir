<?php

namespace App\Http\Controllers\Alumni\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Services\Kuesioner\KelengkapanTracerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * DashboardController
 *
 * Fungsi: Menampilkan halaman utama (Dashboard) untuk pengguna dengan peran (role) Alumni.
 * Tujuan: Menyajikan ringkasan aktivitas alumni, persentase kelengkapan profil dan kuesioner tracer study.
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
        $user = $request->user();
        $alumni = Alumni::where('user_id', $user->id)->first();

        $profilePercentage = 0;
        $profileCompleted = false;
        $profileFilledCount = 0;
        $profileTotalCount = 23;
        $profileMissingFields = [];

        $questionnairePercentage = 0;
        $questionnaireCompleted = false;
        $questionnaireAnsweredCount = 0;
        $questionnaireTotalCount = 61;
        $questionnaireMissing = [];

        if ($alumni) {
            $eval = KelengkapanTracerService::evaluasiKelengkapanTotal($alumni);

            $profilePercentage = $eval['profile']['percentage'] ?? 0;
            $profileCompleted = $eval['profile']['is_complete'] ?? false;
            $profileMissingFields = $eval['profile']['missing_fields'] ?? [];
            $profileTotalCount = $eval['profile']['total_fields'] ?? 30;
            $profileFilledCount = $eval['profile']['filled_count'] ?? max(0, $profileTotalCount - count($profileMissingFields));

            $questionnairePercentage = $eval['questionnaire']['percentage'] ?? 0;
            $questionnaireCompleted = $eval['questionnaire']['is_complete'] ?? false;
            $questionnaireAnsweredCount = $eval['questionnaire']['answered_count'] ?? 0;
            $questionnaireTotalCount = $eval['questionnaire']['total_mandatory'] ?? 61;
            $questionnaireMissing = $eval['questionnaire']['missing_questions'] ?? [];
        }

        return Inertia::render('Alumni/Dashboard', [
            'user' => $user,
            'alumni' => $alumni,
            'profilePercentage' => $profilePercentage,
            'profileCompleted' => $profileCompleted,
            'profileFilledCount' => $profileFilledCount,
            'profileTotalCount' => $profileTotalCount,
            'profileMissingFields' => $profileMissingFields,
            'questionnairePercentage' => $questionnairePercentage,
            'questionnaireCompleted' => $questionnaireCompleted,
            'questionnaireAnsweredCount' => $questionnaireAnsweredCount,
            'questionnaireTotalCount' => $questionnaireTotalCount,
            'questionnaireMissing' => $questionnaireMissing,
        ]);
    }
}
