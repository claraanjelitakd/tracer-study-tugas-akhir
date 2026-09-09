<?php

namespace App\Http\Controllers\Alumni\Profil;

use App\Http\Controllers\Controller;
use App\Models\Atasan;
use App\Models\Company;
use App\Models\DataAkademik;
use App\Models\DataOrangTua;
use App\Models\Yudisium;
use App\Services\Kuesioner\KuesionerSyncService;
use Illuminate\Http\Request;

/**
 * SimpanProfilController
 *
 * Fungsi: Menangani logika penyimpanan (update) profil alumni ke berbagai tabel database
 * (DataAkademik, Yudisium, DataOrangTua, Alumnis, dan Company).
 * Tujuan: Memisahkan logika penyimpanan dari logika penampilan data (Single Responsibility).
 */
class SimpanProfilController extends Controller
{
    /**
     * Menyimpan Perubahan Profil
     */
    public function simpanPerubahanProfil(Request $request)
    {
        $pengguna = $request->user();
        $alumni = $pengguna->alumni;

        $dataTervalidasi = $request->all();

        // 1. Simpan ke Data Akademik (tabel terpisah, dihubungkan via NIM/F1)
        $dataAkademik = DataAkademik::firstOrCreate(
            ['nim' => $alumni->nim],
            [
                'nama' => $pengguna->name,
                'email_students' => $pengguna->email,
            ]
        );

        // Filter bidang yang dimiliki oleh DataAkademik
        $dataUntukAkademik = array_intersect_key($dataTervalidasi, array_flip((new DataAkademik)->getFillable()));

        // Proteksi Bidang Paten Resmi Universitas (NIM, Kelulusan, Angkatan, IPK, SKS)
        // Data ini paten dari pihak universitas dan tidak boleh diubah oleh alumni
        $fieldPaten = [
            'nim',
            'angkatan_masuk',
            'status_mahasiswa',
            'tahun_akademik_lulus',
            'tahun_lulus',
            'ipk',
            'total_sks',
            'total_angka_kualitas',
        ];
        foreach ($fieldPaten as $paten) {
            unset($dataUntukAkademik[$paten]);
        }

        foreach ($dataUntukAkademik as $key => $val) {
            if ($val === '') {
                $dataUntukAkademik[$key] = null;
            }
        }

        if (! empty($dataUntukAkademik)) {
            $dataAkademik->update($dataUntukAkademik);
        }

        // 2. Data Yudisium bersifat paten (ditarik langsung dari pangkalan data universitas)
        // dan tidak boleh diubah oleh alumni.

        // 3. Simpan Data Orang Tua (Simpan null jika field dikosongkan)
        $dataOrangTua = [
            'nama_orang_tua' => ! empty($dataTervalidasi['nama_orang_tua']) ? $dataTervalidasi['nama_orang_tua'] : null,
            'pekerjaan' => ! empty($dataTervalidasi['pekerjaan_orang_tua']) ? $dataTervalidasi['pekerjaan_orang_tua'] : null,
            'alamat' => ! empty($dataTervalidasi['alamat_orang_tua']) ? $dataTervalidasi['alamat_orang_tua'] : null,
            'kota' => ! empty($dataTervalidasi['kota_orang_tua']) ? $dataTervalidasi['kota_orang_tua'] : null,
            'kabupaten_id' => ! empty($dataTervalidasi['kabupaten_id_orang_tua']) ? $dataTervalidasi['kabupaten_id_orang_tua'] : null,
            'provinsi_id' => ! empty($dataTervalidasi['provinsi_id_orang_tua']) ? $dataTervalidasi['provinsi_id_orang_tua'] : null,
            'kode_pos' => ! empty($dataTervalidasi['kode_pos_orang_tua']) ? $dataTervalidasi['kode_pos_orang_tua'] : null,
            'nomor_telepon' => ! empty($dataTervalidasi['nomor_telepon_orang_tua']) ? $dataTervalidasi['nomor_telepon_orang_tua'] : null,
        ];
        DataOrangTua::updateOrCreate(
            ['nim' => $alumni->nim],
            $dataOrangTua
        );

        // =========================================================================================
        // 4. TANGANI PERUSAHAAN (COMPANY)
        // =========================================================================================
        $idPerusahaan = null;

        // Cek apakah alumni mengisi nama perusahaan pada FormKarier
        if (! empty($dataTervalidasi['nama_perusahaan'])) {
            $perusahaan = Company::firstOrCreate(
                ['nama_perusahaan' => $dataTervalidasi['nama_perusahaan']],
                [
                    'province_id' => $dataTervalidasi['company_province_id'] ?? null,
                    'kabupaten_id' => $dataTervalidasi['company_kabupaten_id'] ?? null,
                    'alamat' => $dataTervalidasi['company_alamat'] ?? null,
                    'skala' => $dataTervalidasi['company_skala'] ?? null,
                ]
            );
            $perusahaan->update([
                'province_id' => ! empty($dataTervalidasi['company_province_id']) ? $dataTervalidasi['company_province_id'] : null,
                'kabupaten_id' => ! empty($dataTervalidasi['company_kabupaten_id']) ? $dataTervalidasi['company_kabupaten_id'] : null,
                'alamat' => ! empty($dataTervalidasi['company_alamat']) ? $dataTervalidasi['company_alamat'] : null,
                'skala' => ! empty($dataTervalidasi['company_skala']) ? $dataTervalidasi['company_skala'] : null,
            ]);
            $idPerusahaan = $perusahaan->id;
        }

        // 5. Tangani Data Atasan (Supervisor)
        $idAtasan = null;
        if (! empty($dataTervalidasi['nama_atasan'])) {
            $atasan = ! empty($alumni->atasan_id)
                ? Atasan::find($alumni->atasan_id)
                : null;

            if ($atasan) {
                $atasan->update([
                    'nama' => $dataTervalidasi['nama_atasan'],
                    'email' => ! empty($dataTervalidasi['email_atasan']) ? $dataTervalidasi['email_atasan'] : null,
                    'telepon' => ! empty($dataTervalidasi['telepon_atasan']) ? $dataTervalidasi['telepon_atasan'] : null,
                ]);
            } else {
                $atasan = Atasan::create([
                    'nama' => $dataTervalidasi['nama_atasan'],
                    'email' => ! empty($dataTervalidasi['email_atasan']) ? $dataTervalidasi['email_atasan'] : null,
                    'telepon' => ! empty($dataTervalidasi['telepon_atasan']) ? $dataTervalidasi['telepon_atasan'] : null,
                ]);
            }
            $idAtasan = $atasan->id;
        }

        // =========================================================================================
        // 6. UPDATE PROFIL ALUMNI UTAMA
        // =========================================================================================
        $alumni->update([
            'instagram_url' => ! empty($dataTervalidasi['instagram_url']) ? $dataTervalidasi['instagram_url'] : null,
            'facebook_url' => ! empty($dataTervalidasi['facebook_url']) ? $dataTervalidasi['facebook_url'] : null,
            'linkedin_url' => ! empty($dataTervalidasi['linkedin_url']) ? $dataTervalidasi['linkedin_url'] : null,
            'linkedin_username' => ! empty($dataTervalidasi['linkedin_username']) ? $dataTervalidasi['linkedin_username'] : null,
            'expert' => ! empty($dataTervalidasi['expert']) ? $dataTervalidasi['expert'] : null,
            'minat' => ! empty($dataTervalidasi['minat']) ? $dataTervalidasi['minat'] : null,
            'posisi_jabatan' => ! empty($dataTervalidasi['posisi_jabatan']) ? $dataTervalidasi['posisi_jabatan'] : null,
            'jenis_pekerjaan' => ! empty($dataTervalidasi['jenis_pekerjaan']) ? $dataTervalidasi['jenis_pekerjaan'] : null,
            'zipcode' => ! empty($dataTervalidasi['zipcode']) ? $dataTervalidasi['zipcode'] : null,
            'company_id' => $idPerusahaan,
            'atasan_id' => $idAtasan,
        ]);

        $alumni->refresh();

        // Sinkronisasi otomatis ke tabel responses kuesioner
        KuesionerSyncService::syncProfileResponses($alumni);

        return redirect()->back()->with('success', 'Profil dan Data Akademik berhasil diperbarui.');
    }

    /**
     * Menyimpan Perusahaan Baru Langsung dari Modal Pop-up Tambah Perusahaan
     */
    public function tambahPerusahaanBaru(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'province_id' => 'required|exists:provinces,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'kode_pos' => 'required|string|max:15',
            'alamat' => 'nullable|string',
            'skala' => 'nullable|string',
        ], [
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'province_id.required' => 'Provinsi perusahaan wajib dipilih.',
            'kabupaten_id.required' => 'Kabupaten/Kota perusahaan wajib dipilih.',
            'kode_pos.required' => 'Kode pos perusahaan wajib diisi.',
        ]);

        $perusahaan = Company::create([
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'province_id' => $validated['province_id'],
            'kabupaten_id' => $validated['kabupaten_id'],
            'alamat' => $validated['alamat'] ?? null,
            'kode_pos' => $validated['kode_pos'],
            'skala' => $validated['skala'] ?? 'Lokal',
            'status_verifikasi' => 'Menunggu Verifikasi',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil ditambahkan ke database! Status: Menunggu Verifikasi.',
            'company' => $perusahaan,
        ]);
    }
}
