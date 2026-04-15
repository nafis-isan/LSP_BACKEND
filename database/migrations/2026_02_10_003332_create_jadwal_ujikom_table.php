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
        Schema::create('jadwal_ujikom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_id')->constrained('skema')->onDelete('cascade');
            $table->foreignId('tuk_id')->constrained('tuks')->onDelete('cascade');
            $table->foreignId('tahun_aktif_id')->constrained('tahun_aktif')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->integer('kuota');
            $table->enum('status', ['dibuka', 'ditutup', 'selesai', 'dibatalkan'])->default('dibuka');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_ujikom');
    }
};
