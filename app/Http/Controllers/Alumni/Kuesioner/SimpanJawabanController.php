<?php

namespace App\Http\Controllers\Alumni\Kuesioner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SimpanJawabanController
 * 
 * Fungsi: Menangani proses penyimpanan jawaban kuesioner dari pengguna.
 * Tujuan: Menerima data jawaban dari Frontend, memprosesnya, dan menyimpannya ke tabel respons serta mensinkronisasi data ke tabel profil jika ada relasi.
 */
class SimpanJawabanController extends Controller
{
    /**
     * Memproses Penyimpanan Jawaban Kuesioner
     */
    public function simpanJawabanKuesioner(Request $request)
    {
        $pengguna = Auth::user();
        $alumni = $pengguna->alumni;

        $jawabanMasuk = $request->input('answers'); // Format: [question_id => answer_data]
        
        $kumpulanIdPertanyaan = array_keys($jawabanMasuk);
        
        // Ambil mapping kolom khusus untuk tabel alumnis dan data_akademiks
        $pemetaan = \App\Models\QuestionMapping::whereIn('table_name', ['alumnis', 'data_akademiks'])
            ->whereIn('question_id', $kumpulanIdPertanyaan)
            ->get()
            ->keyBy('question_id');
            
        $dataUpdateAlumni = [];
        $dataUpdateAkademik = [];
        
        $kolomAlumni = $alumni->getFillable();
        $kolomAkademik = $alumni->dataAkademik ? $alumni->dataAkademik->getFillable() : (new \App\Models\DataAkademik)->getFillable();

        foreach ($jawabanMasuk as $idPertanyaan => $jawaban) {
            $isJson = is_array($jawaban) || is_object($jawaban);
            
            \App\Models\Response::updateOrCreate(
                ['alumni_id' => $alumni->id, 'question_id' => $idPertanyaan],
                [
                    'answer_text' => $isJson ? null : $jawaban,
                    'answer_json' => $isJson ? $jawaban : null,
                ]
            );

            // Jika ada relasi mapping yang resmi tercatat di database, sinkronisasi datanya
            if (isset($pemetaan[$idPertanyaan]) && !$isJson) {
                $kolom = $pemetaan[$idPertanyaan]->column_name;
                $tabel = $pemetaan[$idPertanyaan]->table_name;
                
                if ($tabel === 'data_akademiks' && in_array($kolom, $kolomAkademik)) {
                    $dataUpdateAkademik[$kolom] = $jawaban;
                } else if ($tabel === 'alumnis' && in_array($kolom, $kolomAlumni)) {
                    $dataUpdateAlumni[$kolom] = $jawaban;
                }
            }
        }

        if (!empty($dataUpdateAlumni)) {
            $alumni->update($dataUpdateAlumni);
        }
        
        if (!empty($dataUpdateAkademik)) {
            \App\Models\DataAkademik::updateOrCreate(
                ['nim' => $alumni->nim],
                $dataUpdateAkademik
            );
        }

        return redirect()->back()->with('success', 'Jawaban berhasil disimpan.');
    }
}
