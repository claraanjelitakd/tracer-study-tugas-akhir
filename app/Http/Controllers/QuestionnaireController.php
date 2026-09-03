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

        // Ambil kuesioner aktif (Kecuali Section 1 yang dipindah ke Biodata)
        $questionnaire = Questionnaire::where('is_active', true)
            ->with(['sections' => function($query) {
                $query->where('order', '>', 1)
                      ->orderBy('order', 'asc')
                      ->with('questions.options');
            }])
            ->first();

        if (!$questionnaire) {
            return Inertia::render('alumni/kuesioner/index', [
                'error' => 'Tidak ada kuesioner aktif saat ini.'
            ]);
        }

        // Ambil jawaban yang sudah ada
        $responses = Response::where('alumni_id', $alumni->id)
            ->get()
            ->keyBy('question_id');
            
        // Ambil mapping untuk prefill otomatis dari database
        $mappings = \App\Models\QuestionMapping::whereIn('table_name', ['alumnis', 'data_akademiks'])
            ->get()
            ->keyBy('question_id');

        // Merakit default jawaban di backend agar Vue murni sebagai UI
        $initialAnswers = [];
        if ($questionnaire) {
            foreach ($questionnaire->sections as $section) {
                foreach ($section->questions as $q) {
                    if (isset($responses[$q->id])) {
                        if (in_array($q->type, ['checkbox', 'matrix_dual', 'matrix', 'multiple_number'])) {
                            $initialAnswers[$q->id] = $responses[$q->id]->answer_json ?? [];
                        } else if (in_array($q->type, ['radio_input', 'radio_text'])) {
                            $initialAnswers[$q->id] = $responses[$q->id]->answer_json ?? ['selected' => '', 'input' => ''];
                        } else {
                            $initialAnswers[$q->id] = $responses[$q->id]->answer_text ?? '';
                        }
                    } else {
                        if ($q->type === 'checkbox') {
                            $initialAnswers[$q->id] = [];
                        } else if ($q->type === 'matrix_dual') {
                            $obj = [];
                            foreach ($q->options as $o) { $obj[$o->id] = ['A' => null, 'B' => null]; }
                            $initialAnswers[$q->id] = $obj;
                        } else if ($q->type === 'matrix') {
                            $obj = [];
                            foreach ($q->options as $o) { $obj[$o->id] = null; }
                            $initialAnswers[$q->id] = $obj;
                        } else if ($q->type === 'multiple_number') {
                            $initialAnswers[$q->id] = (object)[]; 
                        } else if (in_array($q->type, ['radio_input', 'radio_text'])) {
                            $initialAnswers[$q->id] = ['selected' => '', 'input' => ''];
                        } else {
                            $initialAnswers[$q->id] = '';
                            
                            // Auto-fill dari database alumni atau data_akademiks jika ada mapping
                            if (isset($mappings[$q->id])) {
                                $colName = $mappings[$q->id]->column_name;
                                $tableName = $mappings[$q->id]->table_name;
                                if ($tableName === 'data_akademiks') {
                                    $initialAnswers[$q->id] = $alumni->dataAkademik->$colName ?? '';
                                } else {
                                    $initialAnswers[$q->id] = $alumni->$colName ?? '';
                                }
                            }
                        }
                    }
                }
            }
        }

        return Inertia::render('alumni/kuesioner/index', [
            'questionnaire' => $questionnaire,
            'initialAnswers' => $initialAnswers,
            'error' => null
        ]);
    }

    public function saveSection(Request $request)
    {
        $user = Auth::user();
        $alumni = $user->alumni;

        $answers = $request->input('answers'); // Format: [question_id => answer_data]
        
        $questionIds = array_keys($answers);
        
        // Ambil mapping kolom khusus untuk tabel alumnis dan data_akademiks
        $mappings = \App\Models\QuestionMapping::whereIn('table_name', ['alumnis', 'data_akademiks'])
            ->whereIn('question_id', $questionIds)
            ->get()
            ->keyBy('question_id');
            
        $alumniUpdateData = [];
        $dataAkademikUpdateData = [];
        
        $alumniFillable = $alumni->getFillable();
        $dataAkademikFillable = $alumni->dataAkademik ? $alumni->dataAkademik->getFillable() : (new \App\Models\DataAkademik)->getFillable();

        foreach ($answers as $questionId => $answer) {
            $isJson = is_array($answer) || is_object($answer);
            
            \App\Models\Response::updateOrCreate(
                ['alumni_id' => $alumni->id, 'question_id' => $questionId],
                [
                    'answer_text' => $isJson ? null : $answer,
                    'answer_json' => $isJson ? $answer : null,
                ]
            );

            // Jika ada relasi mapping yang resmi tercatat di database, sinkronisasi datanya
            if (isset($mappings[$questionId]) && !$isJson) {
                $column = $mappings[$questionId]->column_name;
                $table = $mappings[$questionId]->table_name;
                
                if ($table === 'data_akademiks' && in_array($column, $dataAkademikFillable)) {
                    $dataAkademikUpdateData[$column] = $answer;
                } else if ($table === 'alumnis' && in_array($column, $alumniFillable)) {
                    $alumniUpdateData[$column] = $answer;
                }
            }
        }

        if (!empty($alumniUpdateData)) {
            $alumni->update($alumniUpdateData);
        }
        
        if (!empty($dataAkademikUpdateData)) {
            \App\Models\DataAkademik::updateOrCreate(
                ['nim' => $alumni->nim],
                $dataAkademikUpdateData
            );
        }

        return redirect()->back()->with('success', 'Jawaban berhasil disimpan.');
    }
}
