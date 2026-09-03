<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class YudisiumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alumniUsers = \App\Models\User::where('role', 'alumni')->get();

        foreach ($alumniUsers as $index => $user) {
            $nim = $user->username;

            \App\Models\Yudisium::updateOrCreate(
                ['nim' => $nim],
                [
                    'dosen_pembimbing_1' => 'Dr. Budi Santoso',
                    'dosen_pembimbing_2' => 'Ir. Andi Wijaya',
                    'dosen_penguji_1' => 'Prof. Dr. Supriadi',
                    'judul_ta' => 'Sistem Informasi Manajemen Alumni Berbasis Web',
                    'judul_ta_inggris' => 'Web-Based Alumni Management Information System',
                    'url_publikasi' => 'https://repository.ukdw.ac.id/' . $nim,
                    'jenis_publikasi' => 'Jurnal Nasional',
                    'status_publikasi' => 'Terbit',
                    'keterangan_hasil_yudisium' => 'Lulus dengan Pujian',
                    'proses_yudisium' => 'Lulus',
                ]
            );
        }
    }
}
