<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Province;
use App\Models\Kabupaten;
use App\Models\Company;
use App\Models\DataAkademik;

class AlumniProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $alumni = $user->alumni()->with(['prodi', 'company', 'company.province', 'company.kabupaten', 'dataAkademik'])->first();
        
        $provinces = Province::all();
        $kabupatens = Kabupaten::all();
        
        $dataAkademik = clone $alumni?->dataAkademik;
        $orangTua = clone $alumni?->dataAkademik?->orangTua;

        // Merakit formData murni di backend agar frontend Vue tidak perlu logika inisialisasi / pengecekan manual
        $formData = [
            // Identitas Pribadi
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
            'judul_ta' => $dataAkademik?->judul_ta ?? '',
            'status_mahasiswa' => $dataAkademik?->status_mahasiswa ?? 'Lulus',
            'tahun_lulus' => $dataAkademik?->tahun_lulus ?? '',
            'ipk' => $dataAkademik?->ipk ?? '',
            'total_sks' => $dataAkademik?->total_sks ?? '',
            'total_angka_kualitas' => $dataAkademik?->total_angka_kualitas ?? '',
            
            // Data Orang Tua
            'nama_orang_tua' => $orangTua?->nama_orang_tua ?? '',
            'pekerjaan_orang_tua' => $orangTua?->pekerjaan ?? '',
            'alamat_orang_tua' => $orangTua?->alamat ?? '',
            'kota_orang_tua' => $orangTua?->kota ?? '',
            'kabupaten_id_orang_tua' => $orangTua?->kabupaten_id ?? '',
            'provinsi_id_orang_tua' => $orangTua?->provinsi_id ?? '',
            'kode_pos_orang_tua' => $orangTua?->kode_pos ?? '',
            'nomor_telepon_orang_tua' => $orangTua?->nomor_telepon ?? '',
            
            // Profil Tambahan / Tracer
            'instagram_url' => $alumni?->instagram_url ?? '',
            'facebook_url' => $alumni?->facebook_url ?? '',
            'linkedin_url' => $alumni?->linkedin_url ?? '',
            'linkedin_username' => $alumni?->linkedin_username ?? '',
            
            'expert' => $alumni?->expert ?? '',
            'minat' => $alumni?->minat ?? '',
            'zipcode' => $alumni?->zipcode ?? '', // Zipcode untuk perusahaan
            
            'nama_perusahaan' => $alumni?->company?->nama_perusahaan ?? '',
            'company_province_id' => $alumni?->company?->province_id ?? '',
            'company_kabupaten_id' => $alumni?->company?->kabupaten_id ?? '',
        ];
        
        return Inertia::render('alumni/profile/index', [
            'alumniData' => $alumni,
            'formData' => $formData,
            'provinces' => $provinces,
            'kabupatens' => $kabupatens,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $alumni = $user->alumni;

        // Validasi belum mencakup semua, untuk saat ini dibebaskan karena jumlahnya masif
        // Idealnya tiap step di-validasi
        $validated = $request->all();

        // 1. Simpan ke Data Akademik (tabel terpisah, dihubungkan via NIM/F1)
        $dataAkademik = \App\Models\DataAkademik::firstOrCreate(
            ['nim' => $alumni->nim],
            [
                'nama' => $user->name,
                'email_students' => $user->email,
            ]
        );
        
        // Filter out fields that belong to DataAkademik
        $akademikData = array_intersect_key($validated, array_flip((new \App\Models\DataAkademik)->getFillable()));
        if (!empty($akademikData)) {
            $dataAkademik->update($akademikData);
        }
        
        // 1b. Simpan Data Orang Tua
        $orangTuaData = [
            'nama_orang_tua' => $validated['nama_orang_tua'] ?? null,
            'pekerjaan' => $validated['pekerjaan_orang_tua'] ?? null,
            'alamat' => $validated['alamat_orang_tua'] ?? null,
            'kota' => $validated['kota_orang_tua'] ?? null,
            'kabupaten_id' => $validated['kabupaten_id_orang_tua'] ?? null,
            'provinsi_id' => $validated['provinsi_id_orang_tua'] ?? null,
            'kode_pos' => $validated['kode_pos_orang_tua'] ?? null,
            'nomor_telepon' => $validated['nomor_telepon_orang_tua'] ?? null,
        ];
        if (array_filter($orangTuaData)) {
            \App\Models\DataOrangTua::updateOrCreate(
                ['nim' => $alumni->nim],
                $orangTuaData
            );
        }

        // 2. Tangani Perusahaan (Company)
        $companyId = $alumni->company_id;
        if (!empty($validated['nama_perusahaan'])) {
            $company = Company::firstOrCreate(
                ['nama_perusahaan' => $validated['nama_perusahaan']],
                [
                    'province_id' => $validated['company_province_id'] ?? null,
                    'kabupaten_id' => $validated['company_kabupaten_id'] ?? null,
                ]
            );
            $companyId = $company->id;
        }

        // 3. Simpan data sosial media & profesional ke tabel alumnis
        $alumni->update([
            'instagram_url' => $validated['instagram_url'],
            'facebook_url' => $validated['facebook_url'],
            'linkedin_url' => $validated['linkedin_url'],
            'linkedin_username' => $validated['linkedin_username'],
            'expert' => $validated['expert'],
            'minat' => $validated['minat'],
            'zipcode' => $validated['zipcode'],
            'company_id' => $companyId,
        ]);

        return redirect()->back()->with('success', 'Profil dan Data Akademik berhasil diperbarui.');
    }
}
