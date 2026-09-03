<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DataAkademik;
use App\Models\Alumni;

class DataAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alumniUsers = User::where('role', 'alumni')->get();

        foreach ($alumniUsers as $index => $user) {
            $nim = $user->username;
            $parsedInfo = Alumni::parseNim($nim);
            
            DataAkademik::updateOrCreate(
                ['nim' => $nim],
                [
                    'nama' => 'Alumni Dummy ' . $index,
                    'angkatan_masuk' => $parsedInfo ? $parsedInfo['angkatan'] : null,
                    'tempat_lahir' => 'Yogyakarta',
                    'tanggal_lahir' => '2001-08-15', // 15 Agustus 2001 (sesuai password: 15082001)
                    'agama' => 'Kristen',
                    'jenis_kelamin' => ($index % 2 == 0) ? 'Laki-laki' : 'Perempuan',
                    'golongan_darah' => 'O',
                    'warga_negara' => 'WNI',
                    'nomor_telepon' => '0812345678' . str_pad($index, 2, '0', STR_PAD_LEFT),
                    'email_pribadi' => 'pribadi' . $index . '@gmail.com',
                    'email_students' => 'student' . $index . '@students.ukdw.ac.id',
                    'alamat_saat_ini' => 'Jl. Kaliurang Km. ' . rand(5, 15) . ', Yogyakarta',
                    'kelurahan' => 'Sinduharjo',
                    'kecamatan' => 'Ngaglik',
                    'kabupaten_id' => 1, // Pastikan 1 ada (misal Sleman/Jogja)
                    'provinsi_id' => 1, // Pastikan 1 ada (DIY)
                    'kode_pos' => '55581',
                    'nik' => '3404' . str_pad($index, 12, '0', STR_PAD_LEFT),
                    'no_kk' => '340411' . str_pad($index, 10, '0', STR_PAD_LEFT),
                    'nisn' => '999' . str_pad($index, 7, '0', STR_PAD_LEFT),
                    'no_bpjs' => '0001' . str_pad($index, 9, '0', STR_PAD_LEFT),
                    'status_mahasiswa' => 'Lulus',
                    'tahun_akademik_lulus' => 'Gasal 2026/2027',
                    'tahun_lulus' => ($parsedInfo ? $parsedInfo['angkatan'] : 2020) + 4,
                    'ipk' => rand(300, 399) / 100,
                    'total_sks' => 144,
                    'total_angka_kualitas' => rand(500, 600),
                ]
            );
        }
    }
}
