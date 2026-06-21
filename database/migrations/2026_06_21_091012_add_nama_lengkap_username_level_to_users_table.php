<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->after('name');
            $table->string('username')->nullable()->unique()->after('email');
            $table->enum('level', ['administrator', 'asesor', 'asesi', 'validator'])
                ->nullable()
                ->after('role');
        });

        // Backfill dari kolom lama (name/email/role) supaya akun yang sudah ada tetap bisa dipakai
        DB::table('users')->get()->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'nama_lengkap' => $user->nama_lengkap ?? $user->name,
                'username' => $user->username ?? explode('@', $user->email)[0],
                'level' => $user->level ?? ($user->role === 'admin' ? 'administrator' : $user->role),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_lengkap', 'username', 'level']);
        });
    }
};
