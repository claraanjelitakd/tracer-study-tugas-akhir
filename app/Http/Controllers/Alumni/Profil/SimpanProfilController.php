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
        if (!empty($dataUntukAkademik)) {
            $dataAkademik->update($dataUntukAkademik);
        }
        
        // 2. Simpan ke Yudisium
        $dataYudisium = array_intersect_key($dataTervalidasi, array_flip((new Yudisium)->getFillable()));
        if (!empty($dataYudisium)) {
            Yudisium::updateOrCreate(
                ['nim' => $alumni->nim],
                $dataYudisium
            );
        }
        
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
                ]
            );
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
            // Update nama/telepon jika sudah ada tapi beda? (opsional, untuk sekarang firstOrCreate cukup)
            $idAtasan = $atasan->id;
        }

        // =========================================================================================
        // 5. UPDATE PROFIL ALUMNI UTAMA
        // =========================================================================================
        $alumni->update([
            'instagram_url' => $dataTervalidasi['instagram_url'],
            'facebook_url' => $dataTervalidasi['facebook_url'],
            'linkedin_url' => $dataTervalidasi['linkedin_url'],
            'linkedin_username' => $dataTervalidasi['linkedin_username'],
            'expert' => $dataTervalidasi['expert'],
            'minat' => $dataTervalidasi['minat'],
            'zipcode' => $dataTervalidasi['zipcode'],
            'company_id' => $idPerusahaan,
            'atasan_id' => $idAtasan,
        ]);

        return redirect()->back()->with('success', 'Profil dan Data Akademik berhasil diperbarui.');
    }
}
