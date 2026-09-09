<?php

namespace App\Http\Controllers\SuperAdmin\KelolaAlumni;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Atasan;
use App\Models\Company;
use App\Models\DataAkademik;
use App\Models\DataOrangTua;
use App\Models\Kabupaten;
use App\Models\Province;
use App\Models\Questionnaire;
use App\Models\Response;
use App\Services\Kuesioner\KelengkapanTracerService;
use App\Services\Kuesioner\KuesionerSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * DetailAlumniSuperAdminController
 *
 * Fungsi:
 * Menampilkan halaman detail mandiri untuk profil mahasiswa/alumni sekaligus audit jawaban
 * kuesioner tracer study bagi Super Admin.
 *
 * Fitur:
 * 1. Rekap profil lengkap (Data Pribadi, Data Akademik, Yudisium, Orang Tua, Perusahaan, Atasan).
 * 2. Form lengkap pengeditan profil oleh Super Admin untuk membantu alumni memperbarui datanya.
 * 3. Tabulasi jawaban kuesioner tracer study per section dengan hitungan belum dijawab.
 * 4. Indikator butir pertanyaan Wajib vs Opsional secara visual.
 */
class DetailAlumniSuperAdminController extends Controller
{
    /**
     * Tampilkan Halaman Detail Profil & Kuesioner Alumni
     *
     * @param  int|string  $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $alumni = Alumni::with([
            'dataAkademik.yudisium',
            'dataAkademik.orangTua',
            'company.province',
            'company.kabupaten',
            'atasan',
            'user',
            'prodi',
        ])->findOrFail($id);

        // Pastikan respon profil tersinkron ke tabel responses
        KuesionerSyncService::syncProfileResponses($alumni);

        // Ambil evaluasi kelengkapan terpadu
        $evaluasi = KelengkapanTracerService::evaluasiKelengkapanTotal($alumni);

        // Ambil seluruh jawaban kuesioner alumni
        $savedResponses = Response::where('alumni_id', $alumni->id)
            ->get()
            ->keyBy('question_id');

        // Ambil seluruh section dan pertanyaan dari kuesioner aktif
        $alumniProdiId = $alumni->prodi_id;
        $kuesioner = Questionnaire::where('is_active', true)
            ->with(['sections' => function ($secQuery) use ($alumniProdiId) {
                $secQuery->orderBy('order', 'asc')
                    ->with(['questions' => function ($qQuery) use ($alumniProdiId) {
                        $qQuery->where(function ($sub) use ($alumniProdiId) {
                            $sub->whereNull('prodi_id');
                            if ($alumniProdiId) {
                                $sub->orWhere('prodi_id', $alumniProdiId);
                            }
                        })->orderBy('order', 'asc')
                            ->with('options');
                    }]);
            }])
            ->first();

        // Susun struktur data section & jawaban terformat untuk Frontend
        $sectionsWithAnswers = [];

        if ($kuesioner) {
            foreach ($kuesioner->sections as $section) {
                $questionsList = [];

                foreach ($section->questions as $question) {
                    $resp = $savedResponses->get($question->id);
                    $isMandatory = KelengkapanTracerService::isMandatoryQuestion($question->code);

                    $hasAnswer = false;
                    $displayAnswer = null;

                    if ($resp) {
                        if (! empty($resp->answer_text) && trim((string) $resp->answer_text) !== '') {
                            $hasAnswer = true;
                            $displayAnswer = (string) $resp->answer_text;
                        } elseif (is_array($resp->answer_json) && count($resp->answer_json) > 0) {
                            $hasAnswer = true;
                            $displayAnswer = implode(', ', $resp->answer_json);
                        }
                    }

                    $questionsList[] = [
                        'id' => $question->id,
                        'code' => $question->code,
                        'question_text' => $question->question_text,
                        'type' => $question->type,
                        'is_mandatory' => $isMandatory,
                        'is_answered' => $hasAnswer,
                        'answer_text' => $displayAnswer,
                        'options' => $question->options->map(function ($opt) {
                            return [
                                'code' => $opt->code,
                                'text' => $opt->option_text,
                            ];
                        }),
                    ];
                }

                // Hitung berapa pertanyaan wajib di section ini yang belum dijawab
                $unansweredCount = count(array_filter($questionsList, function ($item) {
                    return $item['is_mandatory'] && ! $item['is_answered'];
                }));

                $sectionsWithAnswers[] = [
                    'id' => $section->id,
                    'title' => $section->title,
                    'order' => $section->order,
                    'questions' => $questionsList,
                    'unanswered_mandatory_count' => $unansweredCount,
                ];
            }
        }

        // Susun Data Form Profil Lengkap (sama persis dengan Profil Alumni)
        $dataAkademik = $alumni->dataAkademik;
        $orangTua = $dataAkademik?->orangTua;
        $yudisium = $dataAkademik?->yudisium;
        $atasan = $alumni->atasan;

        $formData = [
            // Identitas Pribadi
            'nim' => $alumni->nim ?? '',
            'nama' => $dataAkademik?->nama ?? '',
            'tempat_lahir' => $dataAkademik?->tempat_lahir ?? '',
            'tanggal_lahir' => $dataAkademik?->tanggal_lahir ?? '',
            'agama' => $dataAkademik?->agama ?? '',
            'jenis_kelamin' => $dataAkademik?->jenis_kelamin ?? '',
            'golongan_darah' => $dataAkademik?->golongan_darah ?? '',
            'warga_negara' => $dataAkademik?->warga_negara ?? 'WNI',
            'nik' => $dataAkademik?->nik ?? '',
            'no_kk' => $dataAkademik?->no_kk ?? '',
            'nisn' => $dataAkademik?->nisn ?? '',
            'no_bpjs' => $dataAkademik?->no_bpjs ?? '',
            'npwp' => $dataAkademik?->npwp ?? '',

            // Kontak & Alamat Pribadi
            'alamat_saat_ini' => $dataAkademik?->alamat_saat_ini ?? '',
            'kelurahan' => $dataAkademik?->kelurahan ?? '',
            'kecamatan' => $dataAkademik?->kecamatan ?? '',
            'kabupaten_id' => $dataAkademik?->kabupaten_id ?? '',
            'provinsi_id' => $dataAkademik?->provinsi_id ?? '',
            'kode_pos' => $dataAkademik?->kode_pos ?? '',
            'nomor_telepon' => $dataAkademik?->nomor_telepon ?? '',
            'email_pribadi' => $dataAkademik?->email_pribadi ?? '',
            'email_students' => $dataAkademik?->email_students ?? '',

            // Data Akademik Utama
            'angkatan_masuk' => $dataAkademik?->angkatan_masuk ?? '',
            'status_mahasiswa' => $dataAkademik?->status_mahasiswa ?? 'Lulus',
            'tahun_akademik_lulus' => $dataAkademik?->tahun_akademik_lulus ?? '',
            'tahun_lulus' => $dataAkademik?->tahun_lulus ?? '',
            'ipk' => $dataAkademik?->ipk ?? '',
            'total_sks' => $dataAkademik?->total_sks ?? '',
            'total_angka_kualitas' => $dataAkademik?->total_angka_kualitas ?? '',

            // Yudisium
            'judul_ta' => $yudisium?->judul_ta ?? '',
            'judul_ta_inggris' => $yudisium?->judul_ta_inggris ?? '',
            'dosen_pembimbing_1' => $yudisium?->dosen_pembimbing_1 ?? '',
            'dosen_pembimbing_2' => $yudisium?->dosen_pembimbing_2 ?? '',
            'dosen_penguji_1' => $yudisium?->dosen_penguji_1 ?? '',
            'dosen_penguji_2' => $yudisium?->dosen_penguji_2 ?? '',
            'url_publikasi' => $yudisium?->url_publikasi ?? '',
            'jenis_publikasi' => $yudisium?->jenis_publikasi ?? '',
            'status_publikasi' => $yudisium?->status_publikasi ?? '',
            'keterangan_hasil_yudisium' => $yudisium?->keterangan_hasil_yudisium ?? '',
            'proses_yudisium' => $yudisium?->proses_yudisium ?? '',

            // Data Orang Tua
            'nama_orang_tua' => $orangTua?->nama_orang_tua ?? '',
            'pekerjaan_orang_tua' => $orangTua?->pekerjaan ?? '',
            'alamat_orang_tua' => $orangTua?->alamat ?? '',
            'kota_orang_tua' => $orangTua?->kota ?? '',
            'kabupaten_id_orang_tua' => $orangTua?->kabupaten_id ?? '',
            'provinsi_id_orang_tua' => $orangTua?->provinsi_id ?? '',
            'kode_pos_orang_tua' => $orangTua?->kode_pos ?? '',
            'nomor_telepon_orang_tua' => $orangTua?->nomor_telepon ?? '',

            // Karier / Profil Profesional
            'instagram_url' => $alumni->instagram_url ?? '',
            'facebook_url' => $alumni->facebook_url ?? '',
            'linkedin_url' => $alumni->linkedin_url ?? '',
            'linkedin_username' => $alumni->linkedin_username ?? '',
            'expert' => $alumni->expert ?? '',
            'minat' => $alumni->minat ?? '',
            'posisi_jabatan' => $alumni->posisi_jabatan ?? '',
            'jenis_pekerjaan' => $alumni->jenis_pekerjaan ?? '',
            'zipcode' => $alumni->zipcode ?? '',

            // Data Perusahaan
            'nama_perusahaan' => $alumni->company?->nama_perusahaan ?? '',
            'company_alamat' => $alumni->company?->alamat ?? '',
            'company_skala' => $alumni->company?->skala ?? '',
            'company_province_id' => $alumni->company?->province_id ?? '',
            'company_kabupaten_id' => $alumni->company?->kabupaten_id ?? '',
            'company_status_verifikasi' => $alumni->company?->status_verifikasi ?? '',

            // Data Atasan
            'nama_atasan' => $atasan?->nama ?? '',
            'email_atasan' => $atasan?->email ?? '',
            'telepon_atasan' => $atasan?->telepon ?? '',
        ];

        $provinces = Province::orderBy('nama_provinsi', 'asc')->get();
        $kabupatens = Kabupaten::orderBy('nama_kabupaten', 'asc')->get();
        $companies = Company::select('id', 'nama_perusahaan', 'province_id', 'kabupaten_id', 'alamat', 'kode_pos', 'skala', 'status_verifikasi')->get();

        return Inertia::render('SuperAdmin/Alumni/Show', [
            'alumni' => $alumni,
            'evaluasi' => $evaluasi,
            'sections' => $sectionsWithAnswers,
            'formData' => $formData,
            'provinces' => $provinces,
            'kabupatens' => $kabupatens,
            'companies' => $companies,
        ]);
    }

    /**
     * Memperbarui Data Profil Alumni oleh Super Admin
     *
     * @param  int|string  $id
     * @return RedirectResponse
     */
    public function updateProfile(Request $request, $id)
    {
        $alumni = Alumni::findOrFail($id);
        $data = $request->all();

        // 1. Perbarui Data Akademik
        $dataAkademik = DataAkademik::firstOrCreate(
            ['nim' => $alumni->nim],
            ['nama' => $data['nama'] ?? $alumni->nim]
        );

        $dataAkademikFields = array_intersect_key($data, array_flip((new DataAkademik)->getFillable()));
        foreach ($dataAkademikFields as $key => $val) {
            if ($val === '') {
                $dataAkademikFields[$key] = null;
            }
        }
        if (! empty($dataAkademikFields)) {
            $dataAkademik->update($dataAkademikFields);
        }

        // 2. Perbarui Data Orang Tua
        $dataOrangTua = [
            'nama_orang_tua' => ! empty($data['nama_orang_tua']) ? $data['nama_orang_tua'] : null,
            'pekerjaan' => ! empty($data['pekerjaan_orang_tua']) ? $data['pekerjaan_orang_tua'] : null,
            'alamat' => ! empty($data['alamat_orang_tua']) ? $data['alamat_orang_tua'] : null,
            'kota' => ! empty($data['kota_orang_tua']) ? $data['kota_orang_tua'] : null,
            'kabupaten_id' => ! empty($data['kabupaten_id_orang_tua']) ? $data['kabupaten_id_orang_tua'] : null,
            'provinsi_id' => ! empty($data['provinsi_id_orang_tua']) ? $data['provinsi_id_orang_tua'] : null,
            'kode_pos' => ! empty($data['kode_pos_orang_tua']) ? $data['kode_pos_orang_tua'] : null,
            'nomor_telepon' => ! empty($data['nomor_telepon_orang_tua']) ? $data['nomor_telepon_orang_tua'] : null,
        ];
        DataOrangTua::updateOrCreate(
            ['nim' => $alumni->nim],
            $dataOrangTua
        );

        // 3. Tangani Data Perusahaan
        $companyId = null;
        if (! empty($data['nama_perusahaan'])) {
            $company = Company::firstOrCreate(
                ['nama_perusahaan' => $data['nama_perusahaan']],
                [
                    'province_id' => $data['company_province_id'] ?? null,
                    'kabupaten_id' => $data['company_kabupaten_id'] ?? null,
                    'alamat' => $data['company_alamat'] ?? null,
                    'skala' => $data['company_skala'] ?? null,
                    'status_verifikasi' => 'Terverifikasi',
                ]
            );

            // Perbarui detail alamat/wilayah perusahaan jika sudah ada
            $company->update([
                'province_id' => ! empty($data['company_province_id']) ? $data['company_province_id'] : null,
                'kabupaten_id' => ! empty($data['company_kabupaten_id']) ? $data['company_kabupaten_id'] : null,
                'alamat' => ! empty($data['company_alamat']) ? $data['company_alamat'] : null,
                'skala' => ! empty($data['company_skala']) ? $data['company_skala'] : null,
            ]);

            $companyId = $company->id;
        }

        // 4. Tangani Data Atasan
        $atasanId = null;
        if (! empty($data['nama_atasan'])) {
            $atasan = ! empty($alumni->atasan_id) ? Atasan::find($alumni->atasan_id) : null;
            if ($atasan) {
                $atasan->update([
                    'nama' => $data['nama_atasan'],
                    'email' => ! empty($data['email_atasan']) ? $data['email_atasan'] : null,
                    'telepon' => ! empty($data['telepon_atasan']) ? $data['telepon_atasan'] : null,
                ]);
            } else {
                $atasan = Atasan::create([
                    'nama' => $data['nama_atasan'],
                    'email' => ! empty($data['email_atasan']) ? $data['email_atasan'] : null,
                    'telepon' => ! empty($data['telepon_atasan']) ? $data['telepon_atasan'] : null,
                ]);
            }
            $atasanId = $atasan->id;
        }

        // 5. Perbarui Model Alumni
        $alumni->update([
            'company_id' => $companyId,
            'atasan_id' => $atasanId,
            'posisi_jabatan' => ! empty($data['posisi_jabatan']) ? $data['posisi_jabatan'] : null,
            'jenis_pekerjaan' => ! empty($data['jenis_pekerjaan']) ? $data['jenis_pekerjaan'] : null,
            'expert' => ! empty($data['expert']) ? $data['expert'] : null,
            'minat' => ! empty($data['minat']) ? $data['minat'] : null,
            'zipcode' => ! empty($data['zipcode']) ? $data['zipcode'] : null,
            'instagram_url' => ! empty($data['instagram_url']) ? $data['instagram_url'] : null,
            'facebook_url' => ! empty($data['facebook_url']) ? $data['facebook_url'] : null,
            'linkedin_url' => ! empty($data['linkedin_url']) ? $data['linkedin_url'] : null,
            'linkedin_username' => ! empty($data['linkedin_username']) ? $data['linkedin_username'] : null,
        ]);

        $alumni->refresh();

        // 6. Sinkronkan Respon Kuesioner (F1 s/d F2H)
        KuesionerSyncService::syncProfileResponses($alumni);

        return redirect()->back()->with('success', 'Data profil mahasiswa berhasil diperbarui oleh Super Admin.');
    }
}
