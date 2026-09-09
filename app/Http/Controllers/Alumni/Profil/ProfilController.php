<?php

namespace App\Http\Controllers\Alumni\Profil;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Kabupaten;
use App\Models\Province;
use Illuminate\Http\Request;

/**
 * ProfilController
 *
 * Fungsi: Mengelola halaman pengisian profil alumni (biodata diri, data akademik, dan orang tua).
 * Tujuan: Menyediakan antarmuka bagi alumni untuk memperbarui data pribadi dan pekerjaan mereka.
 */
class ProfilController extends Controller
{
    /**
     * Menampilkan Halaman Profil
     */
    public function tampilkanHalamanProfil(Request $request)
    {
        $pengguna = $request->user();
        $alumni = $pengguna->alumni()->with(['prodi', 'company', 'company.province', 'company.kabupaten', 'dataAkademik.yudisium', 'dataAkademik.orangTua', 'atasan'])->first();

        $provinsi = Province::all();
        $kabupaten = Kabupaten::all();
        $dataAkademik = $alumni?->dataAkademik;
        $orangTua = $alumni?->dataAkademik?->orangTua;
        $yudisium = $alumni?->dataAkademik?->yudisium;
        $atasan = $alumni?->atasan;

        // Merakit formData murni di backend agar frontend Vue tidak perlu logika inisialisasi / pengecekan manual
        $formData = [
            // Identitas Pribadi
            'nim' => $alumni?->nim ?? '',
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

            // Yudisium (Skripsi & Dosen)
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
            'instagram_url' => $alumni?->instagram_url ?? '',
            'facebook_url' => $alumni?->facebook_url ?? '',
            'linkedin_url' => $alumni?->linkedin_url ?? '',
            'linkedin_username' => $alumni?->linkedin_username ?? '',

            'expert' => $alumni?->expert ?? '',
            'minat' => $alumni?->minat ?? '',
            'posisi_jabatan' => $alumni?->posisi_jabatan ?? '',
            'jenis_pekerjaan' => $alumni?->jenis_pekerjaan ?? '',
            'zipcode' => $alumni?->zipcode ?? '', // Zipcode untuk perusahaan

            'nama_perusahaan' => $alumni?->company?->nama_perusahaan ?? '',
            'company_alamat' => $alumni?->company?->alamat ?? '',
            'company_skala' => $alumni?->company?->skala ?? '',
            'company_province_id' => $alumni?->company?->province_id ?? '',
            'company_kabupaten_id' => $alumni?->company?->kabupaten_id ?? '',
            'company_status_verifikasi' => $alumni?->company?->status_verifikasi ?? '',

            // Data Atasan
            'nama_atasan' => $atasan?->nama ?? '',
            'email_atasan' => $atasan?->email ?? '',
            'telepon_atasan' => $atasan?->telepon ?? '',
        ];

        // Get all companies for Autocomplete (id, name, province_id, kabupaten_id, alamat, kode_pos, skala, status_verifikasi)
        $companies = Company::select('id', 'nama_perusahaan', 'province_id', 'kabupaten_id', 'alamat', 'kode_pos', 'skala', 'status_verifikasi')->get();

        return inertia('Alumni/Profil/Index', [
            'alumniData' => $alumni,
            'formData' => $formData,
            'provinces' => $provinsi,
            'kabupatens' => $kabupaten,
            'companies' => $companies,
        ]);
    }
}
