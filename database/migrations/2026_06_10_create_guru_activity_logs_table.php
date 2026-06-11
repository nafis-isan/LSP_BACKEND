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
        Schema::create('guru_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asesor_id')->constrained('asesor')->onDelete('cascade');
            $table->string('activity_type'); // login, logout, view_asesi, input_nilai, upload_dokumen, etc
            $table->string('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('data')->nullable(); // Bisa menyimpan data tambahan (asesi_id, jadwal_ujikom_id, dll)
            $table->timestamps();

            $table->index('asesor_id');
            $table->index('activity_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_activity_logs');
    }
};
