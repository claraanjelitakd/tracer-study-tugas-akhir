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

        // 2. Seed Superadmin
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'password' => $adminPassword,
                'role' => 'superadmin',
                'must_change_password' => false,
            ]
        );

        // 3. Seed 10 Data Alumni Dummy
        // Format NIM: (2 digit Prodi) + (2 digit Angkatan) + (4 digit Urut)
        // Password default: 01012001 (Sesuai tanggal_lahir default)

        for ($i = 1; $i <= 10; $i++) {
            // Ambil Prodi secara acak
            $randomProdi = $prodiList[array_rand($prodiList)];
            
            // Generate angkatan acak (misal 2020 - 2024) -> '20' sampai '24'
            $angkatanKode = str_pad(rand(20, 24), 2, '0', STR_PAD_LEFT);
            
            // Nomor urut
            $nomorUrut = str_pad($i, 4, '0', STR_PAD_LEFT);
            
            // Rakit NIM
            $nim = $randomProdi['kode'] . $angkatanKode . $nomorUrut;
            
            // Tanggal lahir dummy (digunakan sebagai password default)
            // Format YYYY-MM-DD
            $tglLahir = '2001-01-01'; 
            // Format ddmmyyyy untuk password sesuai contoh sebelumnya (01012001)
            $passwordAlumni = Hash::make('01012001');

            // Ciptakan User
            $alumniUser = User::updateOrCreate(
                ['username' => $nim],
                [
                    'name' => 'Alumni ' . $randomProdi['nama'] . ' ' . $i,
                    'password' => $passwordAlumni,
                    'role' => 'alumni',
                    'must_change_password' => true,
                ]
            );

            // Deteksi Prodi & Angkatan dari NIM
            $parsedInfo = Alumni::parseNim($nim);
            $prodiId = null;
            if ($parsedInfo) {
                // Cari ID Prodi di DB
                $prodiDb = Prodi::where('kode_prodi', $parsedInfo['kode_prodi'])->first();
                if ($prodiDb) {
                    $prodiId = $prodiDb->id;
                }
            }

            // Ciptakan record Alumni
            Alumni::updateOrCreate(
                ['user_id' => $alumniUser->id],
                [
                    'F1' => $nim,
                    'F2A' => $alumniUser->name,
                    'F2B' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'F2C' => 'alumni' . $i . '@example.com',
                    'F2D' => 'Jl. Kaliurang Km. ' . rand(5, 15) . ', Yogyakarta',
                    'tanggal_lahir' => $tglLahir,
                    'prodi_id' => $prodiId,
                    'angkatan' => $parsedInfo ? $parsedInfo['angkatan'] : null,
                ]
            );
        }

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
