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
        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Relasi ke tabel data_akademiks (NIM)
            $table->string('nim')->unique()->comment('NIM - referensi ke data_akademiks');
            $table->foreign('nim')->references('nim')->on('data_akademiks')->cascadeOnDelete();
            
            // Relasi ke Master Prodi
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete();
            
            // Tambahan (Profil)
            // (tanggal_lahir, ipk, dan sac_points telah dihapus atau dipindah)
            // Data Akademik Tambahan dipindah ke tabel data_akademiks
            
            // Sosial Media
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('linkedin_username')->nullable();
            
            // Profesional & Pekerjaan
            $table->string('expert')->nullable()->comment('Keahlian Spesifik');
            $table->string('minat')->nullable()->comment('Minat/Interest');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->comment('Perusahaan tempat bekerja');
            $table->string('zipcode')->nullable()->comment('Kode Pos Wilayah Kerja');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
