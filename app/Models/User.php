<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'nama_lengkap',
        'email',
        'username',
        'role',
        'level',
        'status',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->level === 'administrator';
    }

    public function isAsesor()
    {
        return $this->level === 'asesor';
    }

    public function isAsesi()
    {
        return $this->level === 'asesi';
    }

    public function isValidator()
    {
        return $this->level === 'validator';
    }
}