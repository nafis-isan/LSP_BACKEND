<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KriteriaKerja extends Model
{
    use HasFactory;

    protected $table = 'kriteria_kerja';

    protected $fillable = [
        'element_id',
        'kode_kriteria',
        'uraian_kriteria',
        'status'
    ];

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
