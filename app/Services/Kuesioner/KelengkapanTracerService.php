<?php

namespace App\Services\Kuesioner;

use App\Models\Alumni;
use App\Models\Question;
use App\Models\Response;

/**
 * KelengkapanTracerService
 *
 * Fungsi:
 * Mengaudit status kelengkapan data seorang alumni secara komprehensif, mencakup:
 * 1. Kelengkapan Profil (Data Pribadi, Data Akademik, Data Orang Tua, Data Perusahaan, Data Atasan, dan Posisi Jabatan).
 * 2. Kelengkapan Kuesioner Wajib (F8, Waktu Tunggu F3/F5, Lokasi Kerja F2F, Jenis Perusahaan F11, Nama Perusahaan F2E,
 *    Posisi Jabatan F2G, Studi Lanjut F24A & F24B, Keselarasan Bidang Studi F14, Kesesuaian Pendidikan F15, dan
 *    Evaluasi Kompetensi F17-1 sampai F17-54).
 *
 * Pertanyaan selain daftar wajib di atas bersifat OPSIONAL dan tidak membatalkan kelengkapan status alumni.
 */
class KelengkapanTracerService
{
    /**
     * Daftar kode pertanyaan tracer study yang WAJIB diisi.
     *
     * @var array<int, string>
     */
    public static function getMandatoryQuestionCodes(): array
    {
        $mandatoryCodes = [
            'F8',   // Apakah anda bekerja saat ini (termasuk kerja sambilan dan wirausaha)?
            'F11',  // Jenis perusahaan/instansi/institusi
            'F14',  // Seberapa erat hubungan bidang studi dengan pekerjaan
            'F15',  // Tingkat pendidikan yang paling tepat/sesuai
            'F24A', // Sumberdana pembiayaan kuliah S1 di UKDW
            'F24B', // Sumberdana pembiayaan kuliah S2 jika studi lanjut
        ];

        // F17-1 sampai F17-54 (54 butir evaluasi kompetensi)
        for ($i = 1; $i <= 54; $i++) {
            $mandatoryCodes[] = "F17-{$i}";
        }

        return $mandatoryCodes;
    }

    /**
     * Memeriksa apakah suatu kode pertanyaan berstatus Wajib.
     *
     * @param  string  $code  Kode pertanyaan (misal: 'F8', 'F17-1', 'F18')
     */
    public static function isMandatoryQuestion(string $code): bool
    {
        // Pertanyaan profil otomatis & instrumen wajib inti
        if (in_array($code, ['F1', 'F2A', 'F2B', 'F2C', 'F2D', 'F2E', 'F2E1', 'F2E2', 'F2E3', 'F2F', 'F2G', 'F2H', 'F3', 'F5'])) {
            return true;
        }

        return in_array($code, self::getMandatoryQuestionCodes());
    }

    /**
     * Evaluasi kelengkapan profil alumni.
     *
     * @return array Shape: ['is_complete' => bool, 'percentage' => int, 'missing_fields' => array]
     */
    public static function evaluasiProfil(Alumni $alumni): array
    {
        $alumni->refresh();
        $alumni->load(['dataAkademik.orangTua', 'company.province', 'company.kabupaten', 'atasan', 'user']);

        $dataAkademik = $alumni->dataAkademik;
        $orangTua = $dataAkademik?->orangTua;
        $company = $alumni->company;
        $atasan = $alumni->atasan;

        $fields = [
            // Data Pribadi & Kontak
            'Nama Lengkap' => $dataAkademik?->nama,
            'NIM' => $alumni->nim,
            'Tempat Lahir' => $dataAkademik?->tempat_lahir,
            'Tanggal Lahir' => $dataAkademik?->tanggal_lahir,
            'Agama' => $dataAkademik?->agama,
            'Jenis Kelamin' => $dataAkademik?->jenis_kelamin,
            'Nomor Telepon/HP' => $dataAkademik?->nomor_telepon,
            'Email Pribadi' => $dataAkademik?->email_pribadi,
            'Alamat Domisili Saat Ini' => $dataAkademik?->alamat_saat_ini,
            'NIK KTP' => $dataAkademik?->nik,

            // Data Akademik
            'IPK Kelulusan' => $dataAkademik?->ipk,
            'Tahun Kelulusan' => $dataAkademik?->tahun_akademik_lulus ?? $alumni->tahun_lulus,

            // Data Orang Tua
            'Nama Orang Tua' => $orangTua?->nama_orang_tua,
            'Pekerjaan Orang Tua' => $orangTua?->pekerjaan,
            'Alamat Orang Tua' => $orangTua?->alamat,
            'Nomor Telepon Orang Tua' => $orangTua?->nomor_telepon,

            // Data Perusahaan & Atasan
            'Nama Perusahaan' => $company?->nama_perusahaan,
            'Alamat Perusahaan' => $company?->alamat,
            'Skala Perusahaan' => $company?->skala,
            'Provinsi Perusahaan' => $company?->province_id,
            'Kabupaten Perusahaan' => $company?->kabupaten_id,
            'Nama Atasan' => $atasan?->nama,
            'Email Atasan' => $atasan?->email,
            'Nomor Telepon Atasan' => $atasan?->telepon,
            'Posisi Jabatan' => $alumni->posisi_jabatan,

            // Data Media Sosial & Profesional Alumni (Tabel alumnis)
            'Bidang Keahlian (Expertise)' => $alumni->expert,
            'Minat & Ketertarikan' => $alumni->minat,
            'LinkedIn Profil URL' => $alumni->linkedin_url,
            'LinkedIn Username' => $alumni->linkedin_username,
            'Instagram Profil URL' => $alumni->instagram_url,
            'Facebook Profil URL' => $alumni->facebook_url,
            'Kode Pos Perusahaan (Zipcode)' => $alumni->zipcode,
        ];

        $isTeologi = ($alumni->prodi?->kode_prodi === '31' || substr((string) $alumni->nim, 0, 2) === '31');
        if ($isTeologi) {
            $fields['Jenis Pekerjaan (Gerejawi)'] = $alumni->jenis_pekerjaan;
        }

        $missing = [];
        foreach ($fields as $label => $val) {
            if ($val === null || trim((string) $val) === '') {
                $missing[] = $label;
            }
        }

        $totalFields = count($fields);
        $filledFields = $totalFields - count($missing);
        $percentage = (int) round(($filledFields / $totalFields) * 100);

        return [
            'is_complete' => empty($missing),
            'percentage' => $percentage,
            'filled_count' => $filledFields,
            'total_fields' => $totalFields,
            'missing_fields' => $missing,
        ];
    }

    /**
     * Evaluasi kelengkapan kuesioner tracer study wajib bagi alumni.
     *
     * @return array Shape: ['is_complete' => bool, 'percentage' => int, 'answered_count' => int, 'total_mandatory' => int, 'missing_questions' => array]
     */
    public static function evaluasiKuesionerWajib(Alumni $alumni): array
    {
        // Pastikan respon profil tersinkron
        KuesionerSyncService::syncProfileResponses($alumni);

        // Ambil ID dan respon pertanyaan alumni
        $responses = Response::where('alumni_id', $alumni->id)
            ->with('question')
            ->get()
            ->keyBy(function ($item) {
                return $item->question?->code;
            });

        $mandatoryCodes = self::getMandatoryQuestionCodes();
        $missing = [];

        // 1. Cek F3 / F5 (Pertanyaan waktu tunggu kerja <= 6 bulan)
        $hasF3 = ! empty($responses->get('F3')?->answer_text);
        $hasF5 = ! empty($responses->get('F5')?->answer_text);
        if (! $hasF3 && ! $hasF5) {
            $missing[] = 'F3 / F5 (Waktu Mulai Mencari Kerja / Lama Waktu Tunggu)';
        }

        // 2. Cek pertanyaan wajib lainnya (F8, F11, F14, F15, F24A, F24B, F17-1..54)
        foreach ($mandatoryCodes as $code) {
            $resp = $responses->get($code);
            $hasAnswer = false;

            if ($resp) {
                if (! empty($resp->answer_text) && trim((string) $resp->answer_text) !== '') {
                    $hasAnswer = true;
                } elseif (is_array($resp->answer_json) && count($resp->answer_json) > 0) {
                    $hasAnswer = true;
                }
            }

            if (! $hasAnswer) {
                $q = Question::where('code', $code)->first();
                $label = $q ? "{$code} ({$q->question_text})" : $code;
                $missing[] = $label;
            }
        }

        $totalMandatory = count($mandatoryCodes) + 1; // +1 untuk blok F3/F5
        $missingCount = count($missing);
        $answeredCount = max(0, $totalMandatory - $missingCount);
        $percentage = (int) round(($answeredCount / $totalMandatory) * 100);

        return [
            'is_complete' => ($missingCount === 0),
            'percentage' => $percentage,
            'answered_count' => $answeredCount,
            'total_mandatory' => $totalMandatory,
            'missing_questions' => $missing,
        ];
    }

    /**
     * Evaluasi total akhir status alumni (Profil + Kuesioner Wajib).
     *
     * @return array Shape: ['status' => string, 'is_complete' => bool, 'profile' => array, 'questionnaire' => array]
     */
    public static function evaluasiKelengkapanTotal(Alumni $alumni): array
    {
        $evalProfil = self::evaluasiProfil($alumni);
        $evalKuesioner = self::evaluasiKuesionerWajib($alumni);

        $isComplete = $evalProfil['is_complete'] && $evalKuesioner['is_complete'];

        return [
            'status' => $isComplete ? 'Selesai' : 'Belum Selesai',
            'is_complete' => $isComplete,
            'profile' => $evalProfil,
            'questionnaire' => $evalKuesioner,
        ];
    }
}
