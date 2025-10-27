<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    protected $table = 'detail_pemesanan';
    protected $primaryKey = 'id_detail_pemesanan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_pemesanan',
        'id_item',
        'id_satuan',
        'volume'
    ];

    protected $casts = [
        'volume' => 'decimal:2',
    ];

    // 🔗 Relasi ke pemesanan
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'id_pemesanan', 'id_pemesanan');
    }

    // 🔗 Relasi ke satuan
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    // 🔗 Relasi ke item
    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item', 'id_item');
    }
}
