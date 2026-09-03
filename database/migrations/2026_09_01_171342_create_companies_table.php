<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->foreignId('province_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kabupaten_id')->nullable()->constrained()->nullOnDelete();
            $table->text('alamat')->nullable();
            $table->string('sektor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
