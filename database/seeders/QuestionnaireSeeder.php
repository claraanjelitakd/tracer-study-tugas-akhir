<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Questionnaire::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Tracer Study Universitas 2026',
                'description' => 'Kuesioner pelacakan jejak alumni Universitas Kristen Duta Wacana tahun 2026.',
                'year' => 2026,
                'is_active' => true,
            ]
        );
    }
}
