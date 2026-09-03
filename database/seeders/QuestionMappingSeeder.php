<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            ['column_name' => 'nim', 'question_code' => 'F1'],
            ['column_name' => 'nama', 'question_code' => 'F2A'],
            ['column_name' => 'nomor_telepon', 'question_code' => 'F2B'],
            ['column_name' => 'email', 'question_code' => 'F2C'],
            ['column_name' => 'alamat_saat_ini', 'question_code' => 'F2D'],
        ];

        foreach ($mappings as $map) {
            $question = \App\Models\Question::where('code', $map['question_code'])->first();
            if ($question) {
                \App\Models\QuestionMapping::updateOrCreate(
                    ['table_name' => 'data_akademiks', 'column_name' => $map['column_name']],
                    ['question_id' => $question->id]
                );
            }
        }
    }
}
