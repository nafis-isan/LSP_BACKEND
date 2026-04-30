<?php

namespace Database\Seeders;

use App\Models\Tuk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tuk::create([
            'nama_tuk' => 'TUK Jakarta Pusat',
            'jenis_tuk' => 'sewaktu',
            'deskripsi' => 'Tempat Uji Kompetensi di Jakarta Pusat',
            'foto' => null,
        ]);

        Tuk::create([
            'nama_tuk' => 'TUK Bandung',
            'jenis_tuk' => 'mandiri',
            'deskripsi' => 'Tempat Uji Kompetensi di Bandung',
            'foto' => null,
        ]);

        Tuk::create([
            'nama_tuk' => 'TUK Surabaya',
            'jenis_tuk' => 'tempat kerja',
            'deskripsi' => 'Tempat Uji Kompetensi di Surabaya',
            'foto' => null,
        ]);
    }
}
