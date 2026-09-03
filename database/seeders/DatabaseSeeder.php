<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Alumni;
use App\Models\Prodi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = Hash::make('password123'); // Ganti dengan password yang lebih aman di production

        // 1. Seed Tabel Master Prodi
        $prodiList = [
            ['kode' => '71', 'nama' => 'Informatika'],
            ['kode' => '72', 'nama' => 'Sistem Informasi'],
            ['kode' => '11', 'nama' => 'Manajemen'],
            ['kode' => '12', 'nama' => 'Akuntansi'],
            ['kode' => '21', 'nama' => 'Arsitektur'],
            ['kode' => '22', 'nama' => 'Desain Produk'],
            ['kode' => '41', 'nama' => 'Biologi'],
            ['kode' => '42', 'nama' => 'Teknologi Pangan'],
            ['kode' => '61', 'nama' => 'Kedokteran'],
            ['kode' => '31', 'nama' => 'Filsafat Keilahian'],
            ['kode' => '81', 'nama' => 'Pendidikan Bahasa Inggris'],
            ['kode' => '82', 'nama' => 'Studi Humanitas'],
        ];

        foreach ($prodiList as $p) {
            Prodi::updateOrCreate(
                ['kode_prodi' => $p['kode']],
                ['nama_prodi' => $p['nama']]
            );
        }

        // Seed Dummy Province & Kabupaten
        $provinsi = \App\Models\Province::updateOrCreate(
            ['id' => 1],
            ['nama_provinsi' => 'DI Yogyakarta']
        );
        \App\Models\Kabupaten::updateOrCreate(
            ['id' => 1],
            ['province_id' => $provinsi->id, 'nama_kabupaten' => 'Sleman']
        );

        $this->call([
            UserSeeder::class,
            DataAkademikSeeder::class,
            AlumniSeeder::class,
            DataOrangTuaSeeder::class,
            YudisiumSeeder::class,
        ]);

        // 4. Seed Questionnaire Data
        $this->call([
            QuestionnaireSeeder::class,
            QuestionSectionSeeder::class,
            QuestionSeeder::class,
            QuestionOptionSeeder::class,
            QuestionMappingSeeder::class,
        ]);
    }
}
