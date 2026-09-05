<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Province;
use App\Models\Kabupaten;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan DIY & Sleman
        $diy = Province::firstOrCreate(['nama_provinsi' => 'DI Yogyakarta']);
        $sleman = Kabupaten::firstOrCreate(['province_id' => $diy->id, 'nama_kabupaten' => 'Sleman']);
        $kotaJogja = Kabupaten::firstOrCreate(['province_id' => $diy->id, 'nama_kabupaten' => 'Kota Yogyakarta']);

        // Temukan DKI Jakarta & Jakarta Selatan
        $jakarta = Province::firstOrCreate(['nama_provinsi' => 'DKI Jakarta']);
        $jaksel = Kabupaten::firstOrCreate(['province_id' => $jakarta->id, 'nama_kabupaten' => 'Jakarta Selatan']);
        $jakpus = Kabupaten::firstOrCreate(['province_id' => $jakarta->id, 'nama_kabupaten' => 'Jakarta Pusat']);

        $companies = [
            // Perusahaan di Yogyakarta
            [
                'nama_perusahaan' => 'PT Djarum',
                'province_id' => $diy?->id,
                'kabupaten_id' => $sleman?->id,
                'status_verifikasi' => 'Terverifikasi'
            ],
            [
                'nama_perusahaan' => 'PT Gameloft Indonesia',
                'province_id' => $diy?->id,
                'kabupaten_id' => $kotaJogja?->id,
                'status_verifikasi' => 'Terverifikasi'
            ],
            [
                'nama_perusahaan' => 'PT Niagahoster',
                'province_id' => $diy?->id,
                'kabupaten_id' => $sleman?->id,
                'status_verifikasi' => 'Terverifikasi'
            ],
            
            // Perusahaan di Jakarta
            [
                'nama_perusahaan' => 'PT Tokopedia',
                'province_id' => $jakarta?->id,
                'kabupaten_id' => $jaksel?->id,
                'status_verifikasi' => 'Terverifikasi'
            ],
            [
                'nama_perusahaan' => 'PT Gojek Indonesia',
                'province_id' => $jakarta?->id,
                'kabupaten_id' => $jaksel?->id,
                'status_verifikasi' => 'Terverifikasi'
            ],
            [
                'nama_perusahaan' => 'PT Bank Central Asia Tbk',
                'province_id' => $jakarta?->id,
                'kabupaten_id' => $jakpus?->id,
                'status_verifikasi' => 'Terverifikasi'
            ],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['nama_perusahaan' => $company['nama_perusahaan']],
                $company
            );
        }
    }
}
