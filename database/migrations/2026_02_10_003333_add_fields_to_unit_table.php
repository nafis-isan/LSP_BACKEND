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
        Schema::table('unit', function (Blueprint $table) {
            $table->enum('jenis_standar', ['SKKNI', 'standar khusus', 'standar internasional'])->nullable()->after('nama_unit');
            $table->integer('jumlah_elemen')->nullable()->after('jenis_standar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit', function (Blueprint $table) {
            $table->dropColumn(['jenis_standar', 'jumlah_elemen']);
        });
    }
};
