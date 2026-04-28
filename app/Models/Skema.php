<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skema extends Model
{
    use HasFactory;

    protected $table = 'skema';

    protected $fillable = [
        'kode_skema',
        'nama_skema',
        'jenjang',
        'jenis_skema',
        'file_skema',
        'jumlah_unit',
        'deskripsi',
        'status'
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function asesors()
    {
        return $this->belongsToMany(Asesor::class, 'asesor_skema');
    }

    public function jadwalUjikom()
    {
        return $this->hasMany(JadwalUjikom::class);
    }

    public function muks()
    {
        return $this->hasMany(Muk::class);
    }
}
