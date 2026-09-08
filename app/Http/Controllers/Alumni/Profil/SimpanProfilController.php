<?php

namespace App\Http\Controllers\Alumni\Profil;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\DataAkademik;
use App\Models\DataOrangTua;
use App\Models\Yudisium;

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
            'total_angka_kualitas'
        ];
        foreach ($fieldPaten as $paten) {
            unset($dataUntukAkademik[$paten]);
        }

        if (!empty($dataUntukAkademik)) {
            $dataAkademik->update($dataUntukAkademik);
        }
        
        // 2. Data Yudisium bersifat paten (ditarik langsung dari pangkalan data universitas)
        // dan tidak boleh diubah oleh alumni.
        
        // 3. Simpan Data Orang Tua
        $dataOrangTua = [
            'nama_orang_tua' => $dataTervalidasi['nama_orang_tua'] ?? null,
            'pekerjaan' => $dataTervalidasi['pekerjaan_orang_tua'] ?? null,
            'alamat' => $dataTervalidasi['alamat_orang_tua'] ?? null,
            'kota' => $dataTervalidasi['kota_orang_tua'] ?? null,
            'kabupaten_id' => $dataTervalidasi['kabupaten_id_orang_tua'] ?? null,
            'provinsi_id' => $dataTervalidasi['provinsi_id_orang_tua'] ?? null,
            'kode_pos' => $dataTervalidasi['kode_pos_orang_tua'] ?? null,
            'nomor_telepon' => $dataTervalidasi['nomor_telepon_orang_tua'] ?? null,
        ];
        if (array_filter($dataOrangTua)) {
            DataOrangTua::updateOrCreate(
                ['nim' => $alumni->nim],
                $dataOrangTua
            );
        }

        // =========================================================================================
        // 4. TANGANI PERUSAHAAN (COMPANY)
        // =========================================================================================
        $idPerusahaan = $alumni->company_id;
        
        // Cek apakah alumni mengisi nama perusahaan pada FormKarier
        if (!empty($dataTervalidasi['nama_perusahaan'])) {
            // Gunakan firstOrCreate untuk mencari perusahaan berdasarkan nama.
            // Jika nama perusahaan sudah ada (dibuat admin atau alumni lain), maka akan me-return ID nya.
            // Jika belum ada, sistem akan membuat data perusahaan baru (dengan status default 'Menunggu Verifikasi')
            $perusahaan = Company::firstOrCreate(
                ['nama_perusahaan' => $dataTervalidasi['nama_perusahaan']],
                [
                    'province_id' => $dataTervalidasi['company_province_id'] ?? null,
                    'kabupaten_id' => $dataTervalidasi['company_kabupaten_id'] ?? null,
                    'alamat' => $dataTervalidasi['company_alamat'] ?? null,
                    'skala' => $dataTervalidasi['company_skala'] ?? null,
                ]
            );
            $updateComp = array_filter([
                'province_id' => $dataTervalidasi['company_province_id'] ?? null,
                'kabupaten_id' => $dataTervalidasi['company_kabupaten_id'] ?? null,
                'alamat' => $dataTervalidasi['company_alamat'] ?? null,
                'skala' => $dataTervalidasi['company_skala'] ?? null,
            ]);
            if (!empty($updateComp)) {
                $perusahaan->update($updateComp);
            }
            $idPerusahaan = $perusahaan->id;
        }

        // 5. Tangani Data Atasan (Supervisor)
        $idAtasan = $alumni->atasan_id;
        if (!empty($dataTervalidasi['email_atasan']) && !empty($dataTervalidasi['nama_atasan'])) {
            $atasan = \App\Models\Atasan::firstOrCreate(
                ['email' => $dataTervalidasi['email_atasan']],
                [
                    'nama' => $dataTervalidasi['nama_atasan'],
                    'telepon' => $dataTervalidasi['telepon_atasan'] ?? null,
                ]
            );
            if (!empty($dataTervalidasi['nama_atasan']) || !empty($dataTervalidasi['telepon_atasan'])) {
                $atasan->update(array_filter([
                    'nama' => $dataTervalidasi['nama_atasan'] ?? null,
                    'telepon' => $dataTervalidasi['telepon_atasan'] ?? null,
                ]));
            }
            $idAtasan = $atasan->id;
        }

        // =========================================================================================
        // 6. UPDATE PROFIL ALUMNI UTAMA
        // =========================================================================================
        $alumni->update([
            'instagram_url' => $dataTervalidasi['instagram_url'] ?? null,
            'facebook_url' => $dataTervalidasi['facebook_url'] ?? null,
            'linkedin_url' => $dataTervalidasi['linkedin_url'] ?? null,
            'linkedin_username' => $dataTervalidasi['linkedin_username'] ?? null,
            'expert' => $dataTervalidasi['expert'] ?? null,
            'minat' => $dataTervalidasi['minat'] ?? null,
            'posisi_jabatan' => $dataTervalidasi['posisi_jabatan'] ?? null,
            'jenis_pekerjaan' => $dataTervalidasi['jenis_pekerjaan'] ?? null,
            'zipcode' => $dataTervalidasi['zipcode'] ?? null,
            'company_id' => $idPerusahaan,
            'atasan_id' => $idAtasan,
        ]);

        // Sinkronisasi otomatis ke tabel responses kuesioner
        \App\Services\Kuesioner\KuesionerSyncService::syncProfileResponses($alumni);

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
