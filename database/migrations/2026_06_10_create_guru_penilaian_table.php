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
        Schema::create('guru_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesor_id')->constrained('asesor')->onDelete('cascade');
            $table->foreignId('asesi_id')->constrained('asesi')->onDelete('cascade');
            $table->foreignId('jadwal_ujikom_id')->constrained('jadwal_ujikom')->onDelete('cascade');
            $table->foreignId('element_id')->constrained('element')->onDelete('cascade');
            $table->integer('nilai')->nullable(); // 0-100
            $table->enum('status', ['menunggu', 'dinilai', 'ditinjau', 'selesai'])->default('menunggu');
            $table->text('komentar')->nullable();
            $table->timestamp('tanggal_penilaian')->nullable();
            $table->timestamps();

            $table->unique(['asesor_id', 'asesi_id', 'jadwal_ujikom_id', 'element_id'], 'guru_penilaian_unique');
            $table->index('asesor_id');
            $table->index('asesi_id');
            $table->index('jadwal_ujikom_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_penilaian');
    }
};
