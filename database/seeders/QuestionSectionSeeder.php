<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracerStudy2018 = \App\Models\Questionnaire::where('title', 'Tracer Study UKDW 2018')->first();
        $qId = $tracerStudy2018 ? $tracerStudy2018->id : 1;

        $sections = [
            ['id' => 1, 'questionnaire_id' => $qId, 'title' => 'Identitas & Informasi Pribadi', 'order' => 1],
            ['id' => 2, 'questionnaire_id' => $qId, 'title' => 'Status Pekerjaan & Perusahaan', 'order' => 2],
            ['id' => 3, 'questionnaire_id' => $qId, 'title' => 'Waktu Mulai Mencari Kerja', 'order' => 3],
            ['id' => 4, 'questionnaire_id' => $qId, 'title' => 'Riwayat Lamaran Pekerjaan', 'order' => 4],
            ['id' => 5, 'questionnaire_id' => $qId, 'title' => 'Situasi Saat Ini', 'order' => 5],
            ['id' => 6, 'questionnaire_id' => $qId, 'title' => 'Detail Situasi Bekerja', 'order' => 6],
            ['id' => 7, 'questionnaire_id' => $qId, 'title' => 'Karakteristik Pekerjaan Saat Ini', 'order' => 7],
            ['id' => 8, 'questionnaire_id' => $qId, 'title' => 'Evaluasi Kompetensi', 'order' => 8],
            ['id' => 9, 'questionnaire_id' => $qId, 'title' => 'Kurikulum, Fasilitas & Nilai Kedutawacanaan', 'order' => 9],
        ];

        foreach ($sections as $section) {
            \App\Models\QuestionSection::updateOrCreate(
                ['id' => $section['id']],
                $section
            );
        }
    }
}
