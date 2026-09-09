<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Kabupaten;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. DI Yogyakarta & Sleman
        $diy = Province::where('kode_provinsi', '34')
            ->orWhere('nama_provinsi', 'LIKE', '%Yogyakarta%')
            ->first() ?? Province::firstOrCreate(['nama_provinsi' => 'DI Yogyakarta', 'kode_provinsi' => '34']);

        $sleman = Kabupaten::where('kode_kabupaten', '34.04')
            ->orWhere(function ($q) use ($diy) {
                $q->where('province_id', $diy->id)->where('nama_kabupaten', 'LIKE', '%Sleman%');
            })
            ->first() ?? Kabupaten::firstOrCreate(['province_id' => $diy->id, 'nama_kabupaten' => 'Sleman', 'kode_kabupaten' => '34.04']);

        // 2. DKI Jakarta & Kota Jakarta Pusat
        $jakarta = Province::where('kode_provinsi', '31')
            ->orWhere('nama_provinsi', 'LIKE', '%Jakarta%')
            ->first() ?? Province::firstOrCreate(['nama_provinsi' => 'DKI Jakarta', 'kode_provinsi' => '31']);

        $jakpus = Kabupaten::where('kode_kabupaten', '31.71')
            ->orWhere(function ($q) use ($jakarta) {
                $q->where('province_id', $jakarta->id)->where('nama_kabupaten', 'LIKE', '%Jakarta Pusat%');
            })
            ->first() ?? Kabupaten::firstOrCreate(['province_id' => $jakarta->id, 'nama_kabupaten' => 'Kota Jakarta Pusat', 'kode_kabupaten' => '31.71']);

        // 3. Aceh & Aceh Selatan
        $aceh = Province::where('kode_provinsi', '11')
            ->orWhere('nama_provinsi', 'LIKE', '%Aceh%')
            ->first() ?? Province::firstOrCreate(['nama_provinsi' => 'Aceh (NAD)', 'kode_provinsi' => '11']);

        $acehSelatan = Kabupaten::where('kode_kabupaten', '11.01')
            ->orWhere(function ($q) use ($aceh) {
                $q->where('province_id', $aceh->id)->where('nama_kabupaten', 'LIKE', '%Aceh Selatan%');
            })
            ->first() ?? Kabupaten::firstOrCreate(['province_id' => $aceh->id, 'nama_kabupaten' => 'Aceh Selatan', 'kode_kabupaten' => '11.01']);

        $companies = [
            // ==========================================
            // Perusahaan di Sleman, DI Yogyakarta
            // ==========================================
            [
                'nama_perusahaan' => 'PT Gameloft Indonesia',
                'province_id' => $diy->id,
                'kabupaten_id' => $sleman->id,
                'alamat' => 'Pacific Building, Jl. Laksda Adisucipto No. 157, Sleman',
                'kode_pos' => '55281',
                'skala' => 'Internasional',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'PT Niagahoster',
                'province_id' => $diy->id,
                'kabupaten_id' => $sleman->id,
                'alamat' => 'Jl. Palagan Tentara Pelajar No. 81, Sleman',
                'kode_pos' => '55581',
                'skala' => 'Nasional',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'PT Djarum Sleman Regional',
                'province_id' => $diy->id,
                'kabupaten_id' => $sleman->id,
                'alamat' => 'Jl. Magelang Km 7.5, Mlati, Sleman',
                'kode_pos' => '55284',
                'skala' => 'Nasional',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'CV Javan Cipta Solusi',
                'province_id' => $diy->id,
                'kabupaten_id' => $sleman->id,
                'alamat' => 'Jl. Kaliurang Km 9.2, Ngaglik, Sleman',
                'kode_pos' => '55581',
                'skala' => 'Lokal',
                'status_verifikasi' => 'Terverifikasi',
            ],

            // ==========================================
            // Perusahaan di Kota Jakarta Pusat, DKI Jakarta
            // ==========================================
            [
                'nama_perusahaan' => 'PT Bank Central Asia Tbk (Kantor Pusat)',
                'province_id' => $jakarta->id,
                'kabupaten_id' => $jakpus->id,
                'alamat' => 'Menara BCA, Grand Indonesia, Jl. M.H. Thamrin No. 1, Kota Jakarta Pusat',
                'kode_pos' => '10310',
                'skala' => 'Nasional',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'PT Telekomunikasi Indonesia Tbk (Telkom Landmark)',
                'province_id' => $jakarta->id,
                'kabupaten_id' => $jakpus->id,
                'alamat' => 'The Telkom Hub, Jl. Jend. Gatot Subroto Kav. 52, Kota Jakarta Pusat',
                'kode_pos' => '12710',
                'skala' => 'Nasional',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'PT Tokopedia',
                'province_id' => $jakarta->id,
                'kabupaten_id' => $jakpus->id,
                'alamat' => 'Tokopedia Tower Ciputra World 2, Kota Jakarta Pusat',
                'kode_pos' => '12930',
                'skala' => 'Nasional',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'PT Astra International Tbk',
                'province_id' => $jakarta->id,
                'kabupaten_id' => $jakpus->id,
                'alamat' => 'Menara Astra, Jl. Jend. Sudirman Kav. 5-6, Kota Jakarta Pusat',
                'kode_pos' => '10220',
                'skala' => 'Internasional',
                'status_verifikasi' => 'Terverifikasi',
            ],

            // ==========================================
            // Perusahaan di Aceh Selatan, Aceh
            // ==========================================
            [
                'nama_perusahaan' => 'PT Perkebunan Nusantara I (PTPN Unit Aceh Selatan)',
                'province_id' => $aceh->id,
                'kabupaten_id' => $acehSelatan->id,
                'alamat' => 'Jl. Merdeka No. 45, Tapaktuan, Aceh Selatan',
                'kode_pos' => '23715',
                'skala' => 'Nasional',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'PT Bank Aceh Syariah Cabang Tapaktuan',
                'province_id' => $aceh->id,
                'kabupaten_id' => $acehSelatan->id,
                'alamat' => 'Jl. Jenderal Sudirman No. 18, Tapaktuan, Aceh Selatan',
                'kode_pos' => '23711',
                'skala' => 'Lokal',
                'status_verifikasi' => 'Terverifikasi',
            ],
            [
                'nama_perusahaan' => 'CV Samudera Selatan Digital',
                'province_id' => $aceh->id,
                'kabupaten_id' => $acehSelatan->id,
                'alamat' => 'Jl. Teuku Umar No. 8, Tapaktuan, Aceh Selatan',
                'kode_pos' => '23714',
                'skala' => 'Lokal',
                'status_verifikasi' => 'Terverifikasi',
            ],
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(
                ['nama_perusahaan' => $company['nama_perusahaan']],
                $company
            );
        }
    }
}
