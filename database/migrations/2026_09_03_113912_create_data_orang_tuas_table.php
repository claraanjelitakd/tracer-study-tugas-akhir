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
        Schema::create('data_orang_tuas', function (Blueprint $table) {
            $table->id();

            // Relasi ke DataAkademik berdasarkan NIM
            $table->string('nim')->unique();
            $table->foreign('nim')->references('nim')->on('data_akademiks')->cascadeOnDelete();

            $table->string('nama_orang_tua');
            $table->string('pekerjaan')->nullable();

            // Alamat & Kontak
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable(); // Alternatif jika tidak ingin pakai foreign key kabupaten_id
            $table->foreignId('kabupaten_id')->nullable()->constrained('kabupatens')->nullOnDelete();
            $table->foreignId('provinsi_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->string('kode_pos')->nullable();
            $table->string('nomor_telepon')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_orang_tuas');
    }
};
