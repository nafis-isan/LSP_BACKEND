<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSertifikasi extends Model
{
    use HasFactory;

    protected $table = 'permohonan_sertifikasi';

    protected $fillable = [
        'no_permohonan',
        'asesi_id',
        'jadwal_ujikom_id',
        'tipe_apl',
        'tanggal_permohonan',
        'status',
        'catatan',
        'dokumen_path'
    ];

    protected $casts = [
        'tanggal_permohonan' => 'date'
    ];

    public function asesi()
    {
        return $this->belongsTo(Asesi::class);
    }

    public function jadwalUjikom()
    {
        return $this->belongsTo(JadwalUjikom::class);
    }
}
