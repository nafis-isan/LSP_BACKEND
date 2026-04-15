<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tuk extends Model
{
    use HasFactory;

    protected $table = 'tuks';

    protected $fillable = [
    'foto',
    'nama_tuk',
    'jenis_tuk',
    'deskripsi',
];

    public function jadwalUjikom()
    {
        return $this->hasMany(JadwalUjikom::class);
    }
}