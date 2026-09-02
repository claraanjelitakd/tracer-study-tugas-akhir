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
            ['column_name' => 'F1', 'question_code' => 'F1'],
            ['column_name' => 'F2A', 'question_code' => 'F2A'],
            ['column_name' => 'F2B', 'question_code' => 'F2B'],
            ['column_name' => 'F2C', 'question_code' => 'F2C'],
            ['column_name' => 'F2D', 'question_code' => 'F2D'],
        ];

        foreach ($mappings as $map) {
            $question = \App\Models\Question::where('code', $map['question_code'])->first();
            if ($question) {
                \App\Models\QuestionMapping::updateOrCreate(
                    ['table_name' => 'alumnis', 'column_name' => $map['column_name']],
                    ['question_id' => $question->id]
                );
            }
        }
    }
}
