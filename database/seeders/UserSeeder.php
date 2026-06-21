<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Administrator',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@lsp.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'level' => 'administrator',
            'status' => 'aktif',
        ]);

        // Asesor User
        User::create([
            'name' => 'Asesor Demo',
            'nama_lengkap' => 'Asesor Demo',
            'email' => 'asesor@lsp.com',
            'username' => 'asesor',
            'password' => Hash::make('asesor123'),
            'role' => 'asesor',
            'level' => 'asesor',
            'status' => 'aktif',
        ]);

        // Asesi User
        User::create([
            'name' => 'Asesi Demo',
            'nama_lengkap' => 'Asesi Demo',
            'email' => 'asesi@lsp.com',
            'username' => 'asesi',
            'password' => Hash::make('asesi123'),
            'role' => 'asesi',
            'level' => 'asesi',
            'status' => 'aktif',
        ]);
    }
}