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
        Schema::create('kriteria_kerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('element_id');
            $table->string('kode_kriteria')->unique();
            $table->text('uraian_kriteria');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->foreign('element_id')
                ->references('id')
                ->on('element')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria_kerja');
    }
};
