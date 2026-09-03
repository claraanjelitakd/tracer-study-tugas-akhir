<?php

namespace App\Http\Controllers\Alumni\Kuesioner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Questionnaire;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;

/**
 * KuesionerController
 * 
 * Fungsi: Menampilkan daftar pertanyaan kuesioner kepada alumni.
 * Tujuan: Menyediakan data kuesioner yang sudah diformat rapi dari sisi backend sehingga Frontend (Vue) tidak perlu melakukan logika kompleks.
 */
class KuesionerController extends Controller
{
    /**
     * Menampilkan Halaman Kuesioner
     */
    public function tampilkanKuesioner()
    {
        $pengguna = Auth::user();
        $alumni = $pengguna->alumni;

        if (!$alumni) {
            abort(403, 'Profil Alumni tidak ditemukan.');
        }

        // Ambil kuesioner aktif (Kecuali Section 1 yang dipindah ke Biodata)
        $kuesioner = Questionnaire::where('is_active', true)
            ->with(['sections' => function($query) {
                $query->where('order', '>', 1)
                      ->orderBy('order', 'asc')
                      ->with('questions.options');
            }])
            ->first();

        if (!$kuesioner) {
            return Inertia::render('Alumni/Kuesioner', [
                'error' => 'Tidak ada kuesioner aktif saat ini.',
                'questionnaire' => null,
                'initialAnswers' => []
            ]);
        }

        // Ambil jawaban yang sudah ada
        $jawabanTersimpan = Response::where('alumni_id', $alumni->id)
            ->get()
            ->keyBy('question_id');
            
        // Ambil mapping untuk prefill otomatis dari database
        $pemetaan = \App\Models\QuestionMapping::whereIn('table_name', ['alumnis', 'data_akademiks'])
            ->get()
            ->keyBy('question_id');

        // Merakit default jawaban di backend agar Vue murni sebagai UI
        $jawabanAwal = [];
        if ($kuesioner) {
            foreach ($kuesioner->sections as $bagian) {
                foreach ($bagian->questions as $pertanyaan) {
                    if (isset($jawabanTersimpan[$pertanyaan->id])) {
                        if (in_array($pertanyaan->type, ['checkbox', 'matrix_dual', 'matrix', 'multiple_number'])) {
                            $jawabanAwal[$pertanyaan->id] = $jawabanTersimpan[$pertanyaan->id]->answer_json ?? [];
                        } else if (in_array($pertanyaan->type, ['radio_input', 'radio_text'])) {
                            $jawabanAwal[$pertanyaan->id] = $jawabanTersimpan[$pertanyaan->id]->answer_json ?? ['selected' => '', 'input' => ''];
                        } else {
                            $jawabanAwal[$pertanyaan->id] = $jawabanTersimpan[$pertanyaan->id]->answer_text ?? '';
                        }
                    } else {
                        if ($pertanyaan->type === 'checkbox') {
                            $jawabanAwal[$pertanyaan->id] = [];
                        } else if ($pertanyaan->type === 'matrix_dual') {
                            $obj = [];
                            foreach ($pertanyaan->options as $opsi) { $obj[$opsi->id] = ['A' => null, 'B' => null]; }
                            $jawabanAwal[$pertanyaan->id] = $obj;
                        } else if ($pertanyaan->type === 'matrix') {
                            $obj = [];
                            foreach ($pertanyaan->options as $opsi) { $obj[$opsi->id] = null; }
                            $jawabanAwal[$pertanyaan->id] = $obj;
                        } else if ($pertanyaan->type === 'multiple_number') {
                            $jawabanAwal[$pertanyaan->id] = (object)[]; 
                        } else if (in_array($pertanyaan->type, ['radio_input', 'radio_text'])) {
                            $jawabanAwal[$pertanyaan->id] = ['selected' => '', 'input' => ''];
                        } else {
                            $jawabanAwal[$pertanyaan->id] = '';
                            
                            // Auto-fill dari database alumni atau data_akademiks jika ada mapping
                            if (isset($pemetaan[$pertanyaan->id])) {
                                $namaKolom = $pemetaan[$pertanyaan->id]->column_name;
                                $namaTabel = $pemetaan[$pertanyaan->id]->table_name;
                                if ($namaTabel === 'data_akademiks') {
                                    $jawabanAwal[$pertanyaan->id] = $alumni->dataAkademik->$namaKolom ?? '';
                                } else {
                                    $jawabanAwal[$pertanyaan->id] = $alumni->$namaKolom ?? '';
                                }
                            }
                        }
                    }
                }
            }
        }

        return Inertia::render('Alumni/Kuesioner', [
            'questionnaire' => $kuesioner,
            'initialAnswers' => $jawabanAwal,
            'error' => null
        ]);
    }
}

