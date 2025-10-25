<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penerimaan extends Model
{
    use SoftDeletes;

    protected $table = 'penerimaan';
    protected $primaryKey = 'id_penerimaan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'sso_user_id',
        'tanggal_penerimaan',
        'total_harga',
        'status'
    ];

    // 🔗 Relasi ke detail penerimaan
    public function details()
    {
        return $this->hasMany(detailPenerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    // 🔗 Relasi ke BAST (1 penerimaan → 1 BAST)
    public function bast()
    {
        return $this->hasOne(Bast::class, 'id_penerimaan', 'id_penerimaan');
    }

    // 🔗 Relasi opsional ke user_inventory (pengaju atau PPK)
    public function user()
    {
        return $this->belongsTo(UserInventory::class, 'sso_user_id', 'user_id');
    }

    // 🔹 Helper: cek apakah penerimaan sudah disetujui
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
