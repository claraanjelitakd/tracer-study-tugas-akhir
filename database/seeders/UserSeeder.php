<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPassword = Hash::make('password123'); // Ganti dengan password yang lebih aman di production

        // 1. Seed Superadmin
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'password' => $adminPassword,
                'role' => 'superadmin',
                'must_change_password' => false,
            ]
        );

        // 2. Seed 10 Data User Alumni Dummy
        $prodiList = Prodi::all();
        if ($prodiList->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $randomProdi = $prodiList->random();
            $angkatanKode = str_pad(rand(20, 24), 2, '0', STR_PAD_LEFT);
            $nomorUrut = str_pad($i, 4, '0', STR_PAD_LEFT);
            $nim = $randomProdi->kode_prodi . '1' . $angkatanKode . $nomorUrut;

            // Password format DDMMYYYY
            $tglLahir = '15082001';
            
            User::updateOrCreate(
                ['username' => $nim],
                [
                    'name' => null, // Dikosongkan agar tidak reduksi dengan DataAkademik
                    'email' => null, // Dikosongkan, pakai email_pribadi di DataAkademik
                    'password' => Hash::make($tglLahir),
                    'role' => 'alumni',
                    'must_change_password' => true,
                ]
            );
        }
    }
}
