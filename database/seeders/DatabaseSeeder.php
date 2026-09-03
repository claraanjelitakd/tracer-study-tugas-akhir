<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Membuat akun default untuk Admin Biro 3, Admin Prodi, Superadmin, dan contoh Alumni.
     */
    public function run(): void
    {
        $adminPassword = \Illuminate\Support\Facades\Hash::make('rahasia123');

        // Admin Biro 3
        User::updateOrCreate(
            ['username' => 'jojo'],
            [
                'name' => 'Jojo Biro 3',
                'password' => $adminPassword,
                'role' => 'admin_biro3',
                'must_change_password' => false,
            ]
        );

        // Admin Prodi
        User::updateOrCreate(
            ['username' => 'clara'],
            [
                'name' => 'Clara Prodi',
                'password' => $adminPassword,
                'role' => 'admin_prodi',
                'must_change_password' => false,
            ]
        );

        // Superadmin
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'password' => $adminPassword,
                'role' => 'superadmin',
                'must_change_password' => false,
            ]
        );

        // Contoh Alumni
        $alumniUser = User::updateOrCreate(
            ['username' => '72230607'],
            [
                'name' => 'Alumni Percobaan',
                'password' => \Illuminate\Support\Facades\Hash::make('01012001'),
                'role' => 'alumni',
                'must_change_password' => true,
            ]
        );

        // Buat record di tabel alumnis yang berelasi dengan user di atas
        \App\Models\Alumni::updateOrCreate(
            ['user_id' => $alumniUser->id],
            [
                'F1' => '72230607',
                'F2A' => 'Alumni Percobaan',
                'F2B' => '081234567890',
                'F2C' => 'alumni@example.com',
                'F2D' => 'Jl. Kaliurang Km. 5, Yogyakarta',
                'ipk' => 3.75,
                'sac_points' => 150,
            ]
        );

        $this->call([
            QuestionnaireSeeder::class,
            QuestionSectionSeeder::class,
            QuestionSeeder::class,
            QuestionOptionSeeder::class,
            QuestionMappingSeeder::class,
        ]);
    }
}
