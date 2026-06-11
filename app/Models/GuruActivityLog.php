<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruActivityLog extends Model
{
    protected $table = 'guru_activity_logs';

    protected $fillable = [
        'asesor_id',
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function asesor()
    {
        return $this->belongsTo(Asesor::class);
    }
}
