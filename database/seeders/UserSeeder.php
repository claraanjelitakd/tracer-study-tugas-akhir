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
            $nim = $randomProdi->kode_prodi . $angkatanKode . $nomorUrut;
            $passwordAlumni = Hash::make('01012001');

            User::updateOrCreate(
                ['username' => $nim],
                [
                    'name' => 'Alumni ' . $randomProdi->nama_prodi . ' ' . $i,
                    'password' => $passwordAlumni,
                    'role' => 'alumni',
                    'must_change_password' => true,
                    'email' => 'alumni' . $i . '@example.com',
                ]
            );
        }
    }
}
