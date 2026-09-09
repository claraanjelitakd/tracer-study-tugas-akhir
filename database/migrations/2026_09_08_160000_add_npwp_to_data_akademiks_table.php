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
        Schema::table('data_akademiks', function (Blueprint $table) {
            if (! Schema::hasColumn('data_akademiks', 'npwp')) {
                $table->string('npwp', 30)->nullable()->after('no_bpjs')->comment('Nomor Pokok Wajib Pajak');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_akademiks', function (Blueprint $table) {
            if (Schema::hasColumn('data_akademiks', 'npwp')) {
                $table->dropColumn('npwp');
            }
        });
    }
};
