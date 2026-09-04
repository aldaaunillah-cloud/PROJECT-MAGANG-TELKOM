<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'kode',
        'email',
        'telegram_id',
        'password',
        'role',
        'status',
        'divisi',
        'witel',
        'profile_photo_path',
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
     * Accessor untuk memastikan admin@telkom.com selalu ber-role pikol
     */
    public function getRoleAttribute($value)
    {
        if (in_array(strtolower($this->email ?? ''), ['admin@telkom.com', 'admin@telkom.co.id'])) {
            return 'pikol';
        }
        return $value ?: 'sa';
    }

    /**
     * Cek apakah pengguna memiliki hak akses PIKOL (Admin / Sinkronisasi)
     */
    public function isPikol(): bool
    {
        if (in_array(strtolower($this->email ?? ''), ['admin@telkom.com', 'admin@telkom.co.id'])) {
            return true;
        }

        return in_array(strtolower($this->role ?? ''), ['pikol', 'admin', 'supervisor']);
    }

    /**
     * Cek apakah pengguna adalah Sales Agency (SA)
     */
    public function isSa(): bool
    {
        return !$this->isPikol();
    }

    /**
     * Backward compatibility alias untuk isSa
     */
    public function isHotd(): bool
    {
        return $this->isSa();
    }

    /**
     * Cek apakah status pengguna aktif
     */
    public function isActive(): bool
    {
        return strtolower($this->status ?? 'aktif') === 'aktif';
    }
}