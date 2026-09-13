<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Question
 *
 * Merepresentasikan butir pertanyaan dalam kuesioner tracer study.
 *
 * Kolom penting:
 * - prodi_id: Nullable foreign key ke tabel 'prodis'. Jika diisi, pertanyaan
 *             hanya ditampilkan untuk alumni program studi tersebut (misal: F2E untuk Filsafat Keilahian).
 *             Jika null, pertanyaan berlaku umum untuk seluruh program studi.
 * - code: Kode unik pertanyaan (misal: F1, F2E, F3, F8, F17-1).
 * - type: Tipe input (single_choice, multiple_choice, text, number, searchable_select, dll).
 * - is_required: Menentukan apakah pertanyaan wajib diisi sebelum lanjut/submit.
 *
 * Catatan Arsitektur:
 * Branching / alur lompatan (jump logic) tidak lagi disimpan di tabel ini, melainkan
 * dikelola secara modular per-opsi jawaban di tabel 'question_options' via kolom 'jump_to'.
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_section_id',
        'code',
        'question_text',
        'type',
        'is_required',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Bagian / Section kuesioner tempat pertanyaan ini berada.
     */
    public function section()
    {
        return $this->belongsTo(QuestionSection::class, 'question_section_id');
    }

    /**
     * Opsi-opsi jawaban untuk pertanyaan bertipe pilihan (single_choice / multiple_choice).
     * Setiap opsi dapat memiliki target lompatan (jump_to).
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }
}
