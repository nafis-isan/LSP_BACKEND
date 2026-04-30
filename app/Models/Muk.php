<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Muk extends Model
{
    use HasFactory;

    protected $table = 'muk';

    protected $fillable = [
        'skema_id',
        'kode_dokumen',
        'nama_dokumen',
        'keterangan'
    ];

    public function skema()
    {
        return $this->belongsTo(Skema::class);
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }
}
