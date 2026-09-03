<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Alumni;
use App\Models\Prodi;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alumniUsers = User::where('role', 'alumni')->get();

        foreach ($alumniUsers as $user) {
            $nim = $user->username;
            $parsedInfo = Alumni::parseNim($nim);
            
            $prodiId = null;
            if ($parsedInfo) {
                $prodiDb = Prodi::where('kode_prodi', $parsedInfo['kode_prodi'])->first();
                if ($prodiDb) {
                    $prodiId = $prodiDb->id;
                }
            }

            Alumni::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim' => $nim,
                    'prodi_id' => $prodiId,
                ]
            );
        }
    }
}
