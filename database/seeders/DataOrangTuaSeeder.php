<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataAkademik;
use App\Models\DataOrangTua;

class DataOrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akademiks = DataAkademik::all();

        foreach ($akademiks as $index => $akademik) {
            DataOrangTua::updateOrCreate(
                ['nim' => $akademik->nim],
                [
                    'nama_orang_tua' => 'Bapak/Ibu ' . $akademik->nama,
                    'pekerjaan' => 'PNS',
                    'alamat' => $akademik->alamat_saat_ini, // Samakan dengan domisili anak
                    'kota' => 'Yogyakarta',
                    'kabupaten_id' => 1,
                    'provinsi_id' => 1,
                    'kode_pos' => '55581',
                    'nomor_telepon' => '0898765432' . str_pad($index, 2, '0', STR_PAD_LEFT),
                ]
            );
        }
    }
}
