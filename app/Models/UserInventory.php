<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserInventory extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user_inventory';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sso_user_id',
        'username',
        'password',
        'role',
        'last_login',
        'synced_at'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'synced_at' => 'datetime',
    ];

    // 🔗 Relasi ke log aktivitas
    public function logs()
    {
        return $this->hasMany(LogActivity::class, 'sso_user_id', 'user_id');
    }

    // 🔗 Relasi ke notifikasi
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'sso_user_id', 'user_id');
    }

    // 🔗 Relasi ke penerimaan (jika user ini adalah PPK atau pengaju)
    public function penerimaan()
    {
        return $this->hasMany(Penerimaan::class, 'sso_user_id', 'user_id');
    }

    // 🔹 Helper: cek apakah user adalah admin gudang
    public function isGudang(): bool
    {
        return $this->role === 'gudang';
    }

    // 🔹 Helper: cek apakah user adalah kepala gudang
    public function isKepalaGudang(): bool
    {
        return $this->role === 'kepala_gudang';
    }

    // 🔹 Helper: cek apakah user adalah PPK
    public function isPPK(): bool
    {
        return $this->role === 'ppk';
    }
}
