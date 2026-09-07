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
            ['title' => 'Tracer Study UKDW 2018'],
            [
                'description' => 'Kuesioner pelacakan jejak alumni Universitas Kristen Duta Wacana tahun 2018.',
                'year' => 2018,
                'is_active' => true,
            ]
        );
    }
}
