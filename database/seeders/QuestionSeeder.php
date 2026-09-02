<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            // Identitas & Informasi Pribadi (Section 1)
            ['question_section_id' => 1, 'code' => 'F1', 'question_text' => 'Nomor Mahasiswa', 'type' => 'text', 'is_required' => true, 'order' => 1],
            ['question_section_id' => 1, 'code' => 'F2A', 'question_text' => 'Nama Mahasiswa', 'type' => 'text', 'is_required' => true, 'order' => 2],
            ['question_section_id' => 1, 'code' => 'F2B', 'question_text' => 'Nomor Telepon/HP', 'type' => 'text', 'is_required' => true, 'order' => 3],
            ['question_section_id' => 1, 'code' => 'F2C', 'question_text' => 'Alamat Email', 'type' => 'text', 'is_required' => true, 'order' => 4],
            ['question_section_id' => 1, 'code' => 'F2D', 'question_text' => 'Alamat Sekarang', 'type' => 'text', 'is_required' => true, 'order' => 5],

            // Status Pekerjaan & Perusahaan (Section 2)
            ['question_section_id' => 2, 'code' => 'F2E', 'question_text' => 'Jenis Pekerjaan & Nama Perusahaan / Instansi / Institusi', 'type' => 'radio_text', 'is_required' => true, 'order' => 6],
            ['question_section_id' => 2, 'code' => 'F2E1', 'question_text' => 'Nama Atasan', 'type' => 'text', 'is_required' => false, 'order' => 7],
            ['question_section_id' => 2, 'code' => 'F2E2', 'question_text' => 'Nomor Telepon Atasan', 'type' => 'text', 'is_required' => false, 'order' => 8],
            ['question_section_id' => 2, 'code' => 'F2E3', 'question_text' => 'Email Atasan', 'type' => 'text', 'is_required' => false, 'order' => 9],
            ['question_section_id' => 2, 'code' => 'F2F', 'question_text' => 'Alamat Perusahaan/Instansi', 'type' => 'text', 'is_required' => true, 'order' => 10],
            ['question_section_id' => 2, 'code' => 'F2G', 'question_text' => 'Posisi Jabatan Anda Sekarang', 'type' => 'radio', 'is_required' => true, 'order' => 11],
            ['question_section_id' => 2, 'code' => 'F2H', 'question_text' => 'Skala Perusahaan/Instansi', 'type' => 'radio', 'is_required' => true, 'order' => 12],

            // Pencarian Kerja (Section 3 & 4)
            ['question_section_id' => 3, 'code' => 'F3', 'question_text' => 'Kapan anda mulai mencari pekerjaan?', 'type' => 'radio_input', 'is_required' => true, 'jump_logic' => ['Saya tidak mencari kerja (Jump ke F8)' => 'F8'], 'order' => 13],
            
            ['question_section_id' => 4, 'code' => 'F4', 'question_text' => 'Bagaimana anda mencari pekerjaan tersebut? (Jawaban bisa > 1)', 'type' => 'checkbox', 'is_required' => true, 'order' => 14],
            ['question_section_id' => 4, 'code' => 'F5', 'question_text' => 'Berapa bulan waktu untuk memperoleh pekerjaan pertama?', 'type' => 'radio_input', 'is_required' => true, 'order' => 15],
            ['question_section_id' => 4, 'code' => 'F6', 'question_text' => 'Berapa perusahaan yang sudah dilamar sebelum mendapat pekerjaan pertama?', 'type' => 'number', 'is_required' => true, 'order' => 16],
            ['question_section_id' => 4, 'code' => 'F7', 'question_text' => 'Berapa banyak perusahaan yang merespons lamaran?', 'type' => 'number', 'is_required' => true, 'order' => 17],

            // Situasi Saat Ini (Section 5 & 6)
            ['question_section_id' => 5, 'code' => 'F8', 'question_text' => 'Apakah anda bekerja saat ini (termasuk sambilan & wirausaha)?', 'type' => 'radio', 'is_required' => true, 'jump_logic' => ['Ya (Jump ke F11)' => 'F11'], 'order' => 18],
            
            ['question_section_id' => 6, 'code' => 'F9', 'question_text' => 'Bagaimana situasi anda saat ini? (Jawaban bisa > 1)', 'type' => 'checkbox', 'is_required' => true, 'order' => 19],
            // Semua opsi F10 lompat ke F17 agar tidak nyasar ke F11 (pekerjaan)
            ['question_section_id' => 6, 'code' => 'F10', 'question_text' => 'Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir?', 'type' => 'radio', 'is_required' => true, 'jump_logic' => [
                'Tidak' => 'F17',
                'Tidak, tapi menunggu hasil lamaran' => 'F17',
                'Ya, akan mulai bekerja 2 minggu ke depan (Jump ke F17)' => 'F17',
                'Ya, tapi belum pasti bekerja 2 minggu ke depan (Jump ke F17)' => 'F17',
                'Lainnya (aktif mencari) (Jump ke F17)' => 'F17'
            ], 'order' => 20],

            // Karakteristik Pekerjaan Saat Ini (Section 7)
            ['question_section_id' => 7, 'code' => 'F11', 'question_text' => 'Jenis perusahaan/instansi tempat bekerja sekarang:', 'type' => 'radio', 'is_required' => true, 'order' => 21],
            ['question_section_id' => 7, 'code' => 'F12', 'question_text' => 'Tempat bekerja bergerak di bidang apa? (KBLI 2009)', 'type' => 'searchable_select', 'is_required' => true, 'order' => 22],
            ['question_section_id' => 7, 'code' => 'F13', 'question_text' => 'Berapa pendapatan anda setiap bulannya?', 'type' => 'multiple_number', 'is_required' => true, 'order' => 23],
            ['question_section_id' => 7, 'code' => 'F14', 'question_text' => 'Seberapa erat hubungan bidang studi dengan pekerjaan?', 'type' => 'radio', 'is_required' => true, 'order' => 24],
            ['question_section_id' => 7, 'code' => 'F15', 'question_text' => 'Tingkat pendidikan yang paling tepat/sesuai untuk pekerjaan:', 'type' => 'radio', 'is_required' => true, 'order' => 25],
            ['question_section_id' => 7, 'code' => 'F16', 'question_text' => 'Alasan mengambil pekerjaan tidak sesuai: (Jawaban bisa > 1)', 'type' => 'checkbox', 'is_required' => false, 'order' => 26],

            // Evaluasi Kompetensi (Section 8)
            ['question_section_id' => 8, 'code' => 'F17', 'question_text' => 'Evaluasi Kompetensi Lulusan (A) & Kontribusi PT (B): (Skala: 1 = Sangat Rendah s.d. 5 = Sangat Tinggi)', 'type' => 'matrix_dual', 'is_required' => true, 'order' => 27],

            // Kurikulum & Fasilitas (Section 9)
            ['question_section_id' => 9, 'code' => 'F18', 'question_text' => 'Persentase kesesuaian Mata Kuliah S1 UKDW dengan pekerjaan:', 'type' => 'radio', 'is_required' => true, 'order' => 28],
            ['question_section_id' => 9, 'code' => 'F19', 'question_text' => 'Manfaat proses pembelajaran di UKDW: (Skala: 1 = Tidak Sama Sekali s.d. 5 = Sangat Besar)', 'type' => 'matrix', 'is_required' => true, 'order' => 29],
            ['question_section_id' => 9, 'code' => 'F20', 'question_text' => 'Kondisi fasilitas belajar di UKDW: (Skala: 1 = Sangat Buruk s.d. 5 = Sangat Baik)', 'type' => 'matrix', 'is_required' => true, 'order' => 30],
            ['question_section_id' => 9, 'code' => 'F21', 'question_text' => 'Penilaian pengalaman belajar: (Skala: 1 = Sangat Buruk s.d. 5 = Sangat Baik)', 'type' => 'matrix', 'is_required' => true, 'order' => 31],
            ['question_section_id' => 9, 'code' => 'F22', 'question_text' => 'Aspek tambahan pengalaman belajar: (Skala: 1 = Sangat Buruk s.d. 5 = Sangat Baik)', 'type' => 'matrix', 'is_required' => true, 'order' => 32],
        ];

        foreach ($questions as $q) {
            \App\Models\Question::updateOrCreate(
                ['code' => $q['code']],
                $q
            );
        }
    }
}
