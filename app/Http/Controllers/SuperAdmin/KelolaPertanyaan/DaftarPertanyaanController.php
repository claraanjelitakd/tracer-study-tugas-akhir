<?php

namespace App\Http\Controllers\SuperAdmin\KelolaPertanyaan;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Question;
use App\Models\QuestionSection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Class DaftarPertanyaanController
 *
 * Fungsi:
 * Controller khusus untuk menampilkan daftar butir pertanyaan instrumen kuesioner Tracer Study bagi Superadmin.
 * Mengambil data section, questions, prodi, dan mapping target percabangan (jump logic) untuk dirender di Frontend.
 */
class DaftarPertanyaanController extends Controller
{
    /**
     * Menampilkan halaman utama Kelola Pertanyaan kuesioner dengan filter section & jump target.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        // 1. Ambil seluruh pertanyaan beserta relasi section, opsi, dan prodi
        $questions = Question::with(['section.questionnaire', 'options', 'prodi'])
            ->orderBy('order', 'asc')
            ->get();

        // 2. Ambil seluruh section kuesioner berurutan
        $sections = QuestionSection::with('questionnaire')
            ->orderBy('order', 'asc')
            ->get();

        // 3. Ambil daftar program studi aktif
        $prodis = Prodi::orderBy('kode_prodi', 'asc')->get(['id', 'kode_prodi', 'nama_prodi']);

        // 4. Map kode pertanyaan ke teks pertanyaan untuk label percabangan jump_to
        $targetQuestionMap = Question::pluck('question_text', 'code')->toArray();

        // 5. Susun daftar pilihan target lompatan untuk dropdown
        $availableJumpTargets = Question::orderBy('order', 'asc')
            ->get(['id', 'code', 'question_text'])
            ->map(function ($q) {
                return [
                    'code' => $q->code,
                    'label' => $q->code.' — '.Str::limit($q->question_text, 65),
                    'text' => $q->question_text,
                ];
            });

        // 6. Render komponen Vue SuperAdmin/Pertanyaan/Index
        return Inertia::render('SuperAdmin/Pertanyaan/Index', [
            'questions' => $questions,
            'sections' => $sections,
            'prodis' => $prodis,
            'availableJumpTargets' => $availableJumpTargets,
            'targetQuestionMap' => $targetQuestionMap,
        ]);
    }
}
