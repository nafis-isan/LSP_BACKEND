<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    use HasFactory;

    protected $table = 'element';

    protected $fillable = [
        'unit_id',
        'judul_elemen',
        'kriteria_unjuk_kerja',
        'status'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function kriteriaKerjas()
    {
        return $this->hasMany(KriteriaKerja::class);
    }
}
