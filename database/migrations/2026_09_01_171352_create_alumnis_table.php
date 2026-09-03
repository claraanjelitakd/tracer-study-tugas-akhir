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
            
            // F1: Nomor Mahasiswa / NIM (Primary identifier for alumni)
            $table->string('F1')->unique()->comment('Nomor Mahasiswa / NIM');
            
            // Relasi ke Master Prodi & Kolom Angkatan
            $table->foreignId('prodi_id')->nullable()->constrained('prodis')->nullOnDelete();
            $table->year('angkatan')->nullable()->comment('Tahun Angkatan');
            
            // F2A: Nama Mahasiswa
            $table->string('F2A')->comment('Nama Mahasiswa');
            
            // F2B: Nomor Telepon/HP
            $table->string('F2B')->nullable()->comment('Nomor Telepon/HP');
            
            // F2C: Alamat Email
            $table->string('F2C')->nullable()->comment('Alamat Email');
            
            // F2D: Alamat Sekarang
            $table->text('F2D')->nullable()->comment('Alamat Sekarang');
            
            // Tambahan
            $table->date('tanggal_lahir')->nullable()->comment('Tanggal Lahir Alumni');
            $table->decimal('ipk', 3, 2)->nullable()->comment('Indeks Prestasi Kumulatif');
            $table->integer('sac_points')->nullable()->comment('Poin Student Activity Center');
            
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
