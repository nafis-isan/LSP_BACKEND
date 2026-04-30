<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'muk_id',
        'jenis_bukti',
        'persyaratan',
        'ukuran_file',
        'tipe_file'
    ];

    protected $casts = [
        'ukuran_file' => 'integer'
    ];

    public function muk()
    {
        return $this->belongsTo(Muk::class);
    }
}
