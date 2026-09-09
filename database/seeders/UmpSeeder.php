<?php

namespace Database\Seeders;

use App\Models\Ump;
use Illuminate\Database\Seeder;

class UmpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $umpData = [
            // Sumatra
            ['kode_provinsi' => '11', 'tahun' => 2026, 'besaran' => 3932552, 'catatan' => null],
            ['kode_provinsi' => '12', 'tahun' => 2026, 'besaran' => 3228971, 'catatan' => null],
            ['kode_provinsi' => '13', 'tahun' => 2026, 'besaran' => 3182955, 'catatan' => null],
            ['kode_provinsi' => '14', 'tahun' => 2026, 'besaran' => 3780495, 'catatan' => null],
            ['kode_provinsi' => '15', 'tahun' => 2026, 'besaran' => 3471497, 'catatan' => null],
            ['kode_provinsi' => '16', 'tahun' => 2026, 'besaran' => 3942963, 'catatan' => null],
            ['kode_provinsi' => '17', 'tahun' => 2026, 'besaran' => 2827250, 'catatan' => null],
            ['kode_provinsi' => '18', 'tahun' => 2026, 'besaran' => 3047734, 'catatan' => null],
            ['kode_provinsi' => '19', 'tahun' => 2026, 'besaran' => 4035000, 'catatan' => null],
            ['kode_provinsi' => '21', 'tahun' => 2026, 'besaran' => 3879520, 'catatan' => null],

            // Jawa
            ['kode_provinsi' => '31', 'tahun' => 2026, 'besaran' => 5729876, 'catatan' => null],
            ['kode_provinsi' => '32', 'tahun' => 2026, 'besaran' => 2317601, 'catatan' => null],
            ['kode_provinsi' => '33', 'tahun' => 2026, 'besaran' => 2327386, 'catatan' => null],
            ['kode_provinsi' => '34', 'tahun' => 2026, 'besaran' => 2417495, 'catatan' => null],
            ['kode_provinsi' => '35', 'tahun' => 2026, 'besaran' => 2446880, 'catatan' => null],
            ['kode_provinsi' => '36', 'tahun' => 2026, 'besaran' => 3100881, 'catatan' => null],

            // Nusa Tenggara & Bali
            ['kode_provinsi' => '51', 'tahun' => 2026, 'besaran' => 3207459, 'catatan' => null],
            ['kode_provinsi' => '52', 'tahun' => 2026, 'besaran' => 2673861, 'catatan' => null],
            ['kode_provinsi' => '53', 'tahun' => 2026, 'besaran' => 2455898, 'catatan' => null],

            // Kalimantan
            ['kode_provinsi' => '61', 'tahun' => 2026, 'besaran' => 3054552, 'catatan' => null],
            ['kode_provinsi' => '62', 'tahun' => 2026, 'besaran' => 3686138, 'catatan' => null],
            ['kode_provinsi' => '63', 'tahun' => 2026, 'besaran' => 3725000, 'catatan' => null],
            ['kode_provinsi' => '64', 'tahun' => 2026, 'besaran' => 3680000, 'catatan' => null],
            ['kode_provinsi' => '65', 'tahun' => 2026, 'besaran' => 3775243, 'catatan' => null],

            // Sulawesi
            ['kode_provinsi' => '71', 'tahun' => 2026, 'besaran' => 4002630, 'catatan' => null],
            ['kode_provinsi' => '72', 'tahun' => 2026, 'besaran' => 3179565, 'catatan' => null],
            ['kode_provinsi' => '73', 'tahun' => 2026, 'besaran' => 3921088, 'catatan' => null],
            ['kode_provinsi' => '74', 'tahun' => 2026, 'besaran' => 3306496, 'catatan' => null],
            ['kode_provinsi' => '75', 'tahun' => 2026, 'besaran' => 3405144, 'catatan' => null],
            ['kode_provinsi' => '76', 'tahun' => 2026, 'besaran' => 3315934, 'catatan' => null],

            // Maluku & Papua
            ['kode_provinsi' => '81', 'tahun' => 2026, 'besaran' => 3334490, 'catatan' => null],
            ['kode_provinsi' => '82', 'tahun' => 2026, 'besaran' => 3552840, 'catatan' => null],
            ['kode_provinsi' => '91', 'tahun' => 2026, 'besaran' => 4436283, 'catatan' => null],
            ['kode_provinsi' => '92', 'tahun' => 2026, 'besaran' => 3841000, 'catatan' => null],
            ['kode_provinsi' => '93', 'tahun' => 2026, 'besaran' => 4508850, 'catatan' => null],
            ['kode_provinsi' => '94', 'tahun' => 2026, 'besaran' => 4285848, 'catatan' => null],
            ['kode_provinsi' => '95', 'tahun' => 2026, 'besaran' => 4508714, 'catatan' => null],
            ['kode_provinsi' => '96', 'tahun' => 2026, 'besaran' => 3766000, 'catatan' => null],
        ];

        foreach ($umpData as $data) {
            Ump::updateOrCreate(
                [
                    'kode_provinsi' => $data['kode_provinsi'],
                    'tahun' => $data['tahun'],
                ],
                [
                    'besaran' => $data['besaran'],
                    'catatan' => $data['catatan'],
                ]
            );
        }

        $this->command?->info('Berhasil melakukan seeding data UMP 2026 untuk '.count($umpData).' provinsi.');
    }
}
