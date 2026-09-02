<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Questionnaire;
use App\Models\QuestionSection;
use App\Models\Response;
use App\Models\Alumni;
use Illuminate\Support\Facades\Auth;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $alumni = $user->alumni;

        if (!$alumni) {
            abort(403, 'Profil Alumni tidak ditemukan.');
        }

        // Ambil kuesioner aktif
        $questionnaire = Questionnaire::where('is_active', true)
            ->with(['sections.questions.options'])
            ->first();

        if (!$questionnaire) {
            return Inertia::render('Alumni/Kuesioner/Index', [
                'error' => 'Tidak ada kuesioner aktif saat ini.'
            ]);
        }

        // Ambil jawaban yang sudah ada
        $responses = Response::where('alumni_id', $alumni->id)
            ->get()
            ->keyBy('question_id');
            
        // Ambil mapping untuk prefill otomatis dari database
        $mappings = \App\Models\QuestionMapping::where('table_name', 'alumnis')
            ->get()
            ->keyBy('question_id');

        return Inertia::render('Alumni/Kuesioner/Index', [
            'questionnaire' => $questionnaire,
            'responses' => $responses,
            'alumniData' => $alumni,
            'mappings' => $mappings
        ]);
    }

    public function saveSection(Request $request)
    {
        $user = Auth::user();
        $alumni = $user->alumni;

        $answers = $request->input('answers'); // Format: [question_id => answer_data]
        
        $questionIds = array_keys($answers);
        
        // Ambil mapping kolom khusus untuk tabel alumnis
        $mappings = \App\Models\QuestionMapping::where('table_name', 'alumnis')
            ->whereIn('question_id', $questionIds)
            ->get()
            ->keyBy('question_id');
            
        $alumniUpdateData = [];
        $fillableColumns = $alumni->getFillable();

        foreach ($answers as $questionId => $answer) {
            $isJson = is_array($answer) || is_object($answer);
            
            Response::updateOrCreate(
                ['alumni_id' => $alumni->id, 'question_id' => $questionId],
                [
                    'answer_text' => $isJson ? null : $answer,
                    'answer_json' => $isJson ? $answer : null,
                ]
            );

            // Jika ada relasi mapping yang resmi tercatat di database, sinkronisasi datanya
            if (isset($mappings[$questionId]) && !$isJson) {
                $column = $mappings[$questionId]->column_name;
                // Double check keamanan (harus fillable di model)
                if (in_array($column, $fillableColumns)) {
                    $alumniUpdateData[$column] = $answer;
                }
            }
        }

        if (!empty($alumniUpdateData)) {
            $alumni->update($alumniUpdateData);
        }

        return redirect()->back()->with('success', 'Jawaban berhasil disimpan.');
    }
}
