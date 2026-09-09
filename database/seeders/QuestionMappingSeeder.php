<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Question;
use App\Models\QuestionMapping;
use App\Services\Kuesioner\KuesionerSyncService;
use Illuminate\Database\Seeder;

class QuestionMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            ['table_name' => 'data_akademiks', 'column_name' => 'nim', 'question_code' => 'F1'],
            ['table_name' => 'data_akademiks', 'column_name' => 'nama', 'question_code' => 'F2A'],
            ['table_name' => 'data_akademiks', 'column_name' => 'nomor_telepon', 'question_code' => 'F2B'],
            ['table_name' => 'data_akademiks', 'column_name' => 'email_pribadi', 'question_code' => 'F2C'],
            ['table_name' => 'data_akademiks', 'column_name' => 'alamat_saat_ini', 'question_code' => 'F2D'],

            // Relasi ke tabel companies
            ['table_name' => 'companies', 'column_name' => 'nama_perusahaan', 'question_code' => 'F2E'],
            ['table_name' => 'companies', 'column_name' => 'alamat', 'question_code' => 'F2F'],
            ['table_name' => 'companies', 'column_name' => 'skala', 'question_code' => 'F2H'],

            // Relasi ke tabel atasans
            ['table_name' => 'atasans', 'column_name' => 'nama', 'question_code' => 'F2E1'],
            ['table_name' => 'atasans', 'column_name' => 'telepon', 'question_code' => 'F2E2'],
            ['table_name' => 'atasans', 'column_name' => 'email', 'question_code' => 'F2E3'],

            // Relasi ke tabel alumnis
            ['table_name' => 'alumnis', 'column_name' => 'posisi_jabatan', 'question_code' => 'F2G'],
            ['table_name' => 'alumnis', 'column_name' => 'jenis_pekerjaan', 'question_code' => 'F2D1'],
        ];

        foreach ($mappings as $map) {
            $question = Question::where('code', $map['question_code'])->first();
            if ($question) {
                QuestionMapping::updateOrCreate(
                    ['table_name' => $map['table_name'], 'column_name' => $map['column_name']],
                    ['question_id' => $question->id]
                );
            }
        }

        // Pastikan tabel responses langsung terisi untuk pertanyaan identitas & profil (F1..F2H) bagi seluruh alumni
        foreach (Alumni::all() as $alumni) {
            KuesionerSyncService::syncProfileResponses($alumni);
        }
    }
}
