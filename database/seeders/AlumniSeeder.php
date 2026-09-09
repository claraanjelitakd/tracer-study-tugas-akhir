<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Atasan;
use App\Models\Company;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alumniUsers = User::where('role', 'alumni')->get();
        $companies = Company::all();

        $jabatanList = [
            'Staff',
            'Supervisor',
            'Low manager',
            'Midle Manager',
            'Direksi / Top Manager',
            'Staff',
            'Supervisor',
            'Midle Manager',
            'Staff',
            'Supervisor',
        ];

        $expertList = [
            'Web Development & Cloud Computing',
            'Data Science & Predictive Analytics',
            'UI/UX Design & User Research',
            'Cyber Security & Penetration Testing',
            'Financial Auditing & Taxation',
            'Architectural 3D Modeling & BIM',
            'Food Quality Assurance & HACCP',
            'Clinical Medicine & Diagnostics',
            'Pastoral Care & Christian Ministry',
            'English Curriculum & Instructional Design',
        ];

        $minatList = [
            'Open Source, IoT, Artificial Intelligence',
            'Big Data, Deep Learning, Cloud Architecture',
            'Design Systems, Accessibility, Interaction Design',
            'Network Security, Cryptography, Blockchain',
            'Investment Portfolio, Stock Analysis, FinTech',
            'Sustainable Architecture, Green Building Design',
            'Food Biotechnology, Fermentation Science',
            'Public Health, Medical Research, Telemedicine',
            'Community Leadership, Interfaith Dialogue',
            'Language Acquisition, EdTech, Translation',
        ];

        $atasanDataList = [
            ['nama' => 'Ir. Bambang Trihatmojo, M.Eng.', 'email' => 'bambang.t@perusahaan.co.id', 'telepon' => '081223344551'],
            ['nama' => 'Dra. Sri Wahyuni, M.Si.', 'email' => 'sri.wahyuni@perusahaan.co.id', 'telepon' => '081223344552'],
            ['nama' => 'Hartono Kusuma, S.E., Ak.', 'email' => 'hartono.k@perusahaan.co.id', 'telepon' => '081223344553'],
            ['nama' => 'Maya Indrawati, S.T., M.Kom.', 'email' => 'maya.indrawati@perusahaan.co.id', 'telepon' => '081223344554'],
            ['nama' => 'Rendra Pratama, S.Kom., MBA.', 'email' => 'rendra.pratama@perusahaan.co.id', 'telepon' => '081223344555'],
        ];

        foreach ($alumniUsers as $index => $user) {
            $nim = $user->username;
            $parsedInfo = Alumni::parseNim($nim);

            $prodiId = null;
            if ($parsedInfo) {
                $prodiDb = Prodi::where('kode_prodi', $parsedInfo['kode_prodi'])->first();
                if ($prodiDb) {
                    $prodiId = $prodiDb->id;
                }
            }

            // Assign company secara bergantian jika ada perusahaan
            $companyId = null;
            if ($companies->isNotEmpty()) {
                $comp = $companies[$index % $companies->count()];
                $companyId = $comp->id;
            }

            // Buat atau kaitkan data atasan
            $atasanInfo = $atasanDataList[$index % count($atasanDataList)];
            $atasan = Atasan::firstOrCreate(
                ['email' => 'atasan.'.$index.'.'.$atasanInfo['email']],
                [
                    'nama' => $atasanInfo['nama'],
                    'telepon' => $atasanInfo['telepon'],
                ]
            );

            $cleanUsername = strtolower(str_replace(' ', '', explode(' ', $user->name ?? 'alumni')[0])).$index;

            Alumni::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim' => $nim,
                    'prodi_id' => $prodiId,
                    'company_id' => $companyId,
                    'atasan_id' => $atasan?->id,
                    'posisi_jabatan' => $jabatanList[$index % count($jabatanList)],
                    'jenis_pekerjaan' => ($parsedInfo && $parsedInfo['kode_prodi'] === '31') ? 'Gerejawi' : null,
                    'expert' => $expertList[$index % count($expertList)],
                    'minat' => $minatList[$index % count($minatList)],
                    'linkedin_url' => 'https://linkedin.com/in/'.$cleanUsername,
                    'linkedin_username' => $cleanUsername,
                    'instagram_url' => 'https://instagram.com/'.$cleanUsername,
                    'facebook_url' => 'https://facebook.com/'.$cleanUsername,
                    'zipcode' => ($index % 3 == 0) ? '55281' : (($index % 3 == 1) ? '10120' : '23715'),
                ]
            );
        }
    }
}
