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

    /**
     * Kolom 'name'/'email'/'role' (bawaan Laravel) dan 'nama_lengkap'/'username'/'level'
     * (dipakai UserController) sengaja dibuat redundan. Sinkronisasi di sini supaya
     * controller mana pun yang dipakai untuk create/update tidak perlu mengisi keduanya.
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->nama_lengkap && !$user->isDirty('name')) {
                $user->name = $user->nama_lengkap;
            }

            if ($user->username && !$user->email) {
                $user->email = $user->username . '@lsp.local';
            }

            if ($user->level && in_array($user->level, ['administrator', 'asesor', 'asesi']) && !$user->isDirty('role')) {
                $user->role = $user->level === 'administrator' ? 'admin' : $user->level;
            }
        });
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