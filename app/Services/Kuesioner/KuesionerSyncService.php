<?php

namespace App\Services\Kuesioner;

use App\Models\Alumni;
use App\Models\Question;
use App\Models\Response;

/**
 * KuesionerSyncService
 *
 * Fungsi: Menyinkronkan data profil alumni (Data Akademik, Akun User, Perusahaan, dan Atasan)
 * secara otomatis ke tabel `responses` untuk kelompok instrumen F1 s/d F2H.
 *
 * Arsitektur:
 * - Mengeliminasi kebutuhan alumni mengisi ulang identitas dan tempat kerja di kuesioner.
 * - Menjaga tabel `responses` tetap lengkap 100% untuk semua 23 pertanyaan inti (F1..F23)
 *   sehingga siap ditarik oleh modul Export Excel, CSV, dan Pelaporan Resmi Dikti.
 * - Kolom `answer_text` diisi varchar bersih, dan `answer_json` bernilai null.
 */
class KuesionerSyncService
{
    /**
     * Menyinkronkan data profil alumni ke tabel responses untuk pertanyaan F1 sampai F2H.
     *
     * @param  Alumni  $alumni  Model alumni yang akan disinkronkan datanya.
     */
    public static function syncProfileResponses(Alumni $alumni): void
    {
        $alumni->refresh();
        $alumni->load(['dataAkademik', 'company.province', 'company.kabupaten', 'atasan', 'user', 'prodi']);

        $alamatPerusahaanParts = array_filter([
            $alumni->company?->alamat,
            $alumni->company?->kabupaten?->nama_kabupaten,
            $alumni->company?->province?->nama_provinsi,
            $alumni->zipcode,
        ]);
        $alamatPerusahaan = ! empty($alamatPerusahaanParts) ? implode(', ', $alamatPerusahaanParts) : null;

        $isTeologi = ($alumni->prodi?->kode_prodi === '31' || substr($alumni->nim, 0, 2) === '31');

        if (! $isTeologi) {
            $qF2D1 = Question::where('code', 'F2D1')->first();
            if ($qF2D1) {
                Response::where('alumni_id', $alumni->id)->where('question_id', $qF2D1->id)->delete();
            }
        }

        $profileMap = [
            'F1' => $alumni->nim,
            'F2A' => $alumni->dataAkademik?->nama,
            'F2B' => $alumni->dataAkademik?->nomor_telepon,
            'F2C' => $alumni->dataAkademik?->email_pribadi,
            'F2D' => $alumni->dataAkademik?->alamat_saat_ini,
            'F2D1' => $isTeologi ? $alumni->jenis_pekerjaan : null,
            'F2E' => $alumni->company?->nama_perusahaan,
            'F2E1' => $alumni->atasan?->nama,
            'F2E2' => $alumni->atasan?->telepon,
            'F2E3' => $alumni->atasan?->email,
            'F2F' => $alamatPerusahaan,
            'F2G' => $alumni->posisi_jabatan,
            'F2H' => $alumni->company?->skala,
        ];

        foreach ($profileMap as $code => $val) {
            $question = Question::where('code', $code)->first();
            if (! $question) {
                continue;
            }

            if ($val === null || trim((string) $val) === '') {
                // Jika data profil dikosongkan/dihapus, hapus respon otomatisnya agar tidak terhitung terisi
                Response::where('alumni_id', $alumni->id)
                    ->where('question_id', $question->id)
                    ->delete();
            } else {
                Response::updateOrCreate(
                    ['alumni_id' => $alumni->id, 'question_id' => $question->id],
                    [
                        'answer_text' => (string) $val,
                        'answer_json' => null,
                    ]
                );
            }
        }
    }
}
