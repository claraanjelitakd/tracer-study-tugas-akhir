<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_section_id')->constrained()->cascadeOnDelete();
            // Scoping program studi spesifik (misal: F2E khusus prodi Filsafat Keilahian kode 31). Null = berlaku umum untuk semua prodi.
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete();
            $table->string('code')->unique(); // e.g. F1, F2E, F3, F8
            $table->text('question_text');
            $table->string('type'); // single_choice, multiple_choice, text, number, searchable_select, dll.
            $table->boolean('is_required')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            // Catatan: Kolom jump_logic tidak lagi ditaruh di tabel questions, melainkan dikelola per-opsi di question_options via kolom jump_to.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
