<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemesanan extends Model
{
    use SoftDeletes;

    protected $table = 'pemesanan';
    protected $primaryKey = 'id_pemesanan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sso_user_id',
        'asal_instalasi',
        'status',
        'pdf_path'
    ];

    // 🔗 Relasi ke detail pemesanan
    public function details()
    {
        return $this->hasMany(DetailPemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }

    // 🔗 Relasi ke user (pengaju / PPK)
    public function user()
    {
        return $this->belongsTo(UserInventory::class, 'sso_user_id', 'user_id');
    }

    // 🔹 Helper status
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
