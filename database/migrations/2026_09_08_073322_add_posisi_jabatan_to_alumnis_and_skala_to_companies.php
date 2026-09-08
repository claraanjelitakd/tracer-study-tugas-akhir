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
        Schema::table('alumnis', function (Blueprint $table) {
            $table->string('posisi_jabatan')->nullable()->after('company_id');
            $table->string('jenis_pekerjaan')->nullable()->after('posisi_jabatan');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->string('skala')->nullable()->after('sektor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropColumn(['posisi_jabatan', 'jenis_pekerjaan']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['skala']);
        });
    }
};
