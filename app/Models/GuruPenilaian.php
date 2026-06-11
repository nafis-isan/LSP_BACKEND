<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruPenilaian extends Model
{
    protected $table = 'guru_penilaian';

    protected $fillable = [
        'asesor_id',
        'asesi_id',
        'jadwal_ujikom_id',
        'element_id',
        'nilai',
        'status',
        'komentar',
        'tanggal_penilaian',
    ];

    protected $casts = [
        'tanggal_penilaian' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function asesor()
    {
        return $this->belongsTo(Asesor::class);
    }

    public function asesi()
    {
        return $this->belongsTo(Asesi::class);
    }

    public function jadwalUjikom()
    {
        return $this->belongsTo(JadwalUjikom::class);
    }

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
