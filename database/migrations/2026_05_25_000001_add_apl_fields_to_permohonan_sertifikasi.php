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
        Schema::table('permohonan_sertifikasi', function (Blueprint $table) {
            // Tambah kolom tipe_apl
            if (!Schema::hasColumn('permohonan_sertifikasi', 'tipe_apl')) {
                $table->enum('tipe_apl', ['APL-01', 'APL-02'])->after('jadwal_ujikom_id')->nullable();
            }

            // Tambah kolom dokumen_path
            if (!Schema::hasColumn('permohonan_sertifikasi', 'dokumen_path')) {
                $table->string('dokumen_path')->after('catatan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_sertifikasi', function (Blueprint $table) {
            if (Schema::hasColumn('permohonan_sertifikasi', 'tipe_apl')) {
                $table->dropColumn('tipe_apl');
            }

            if (Schema::hasColumn('permohonan_sertifikasi', 'dokumen_path')) {
                $table->dropColumn('dokumen_path');
            }
        });
    }
};
