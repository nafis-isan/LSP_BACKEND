<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'unit';

    protected $fillable = [
        'skema_id',
        'kode_unit',
        'nama_unit',
        'jenis_standar',
        'jumlah_elemen',
        'deskripsi',
        'status'
    ];

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function elements()
    {
        return $this->hasMany(Element::class);
    }
}
