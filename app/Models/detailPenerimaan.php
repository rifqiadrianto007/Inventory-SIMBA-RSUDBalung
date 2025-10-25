<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenerimaan extends Model
{
    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'id_detail_penerimaan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_penerimaan',
        'id_item',
        'id_category',
        'volume',
        'id_satuan',
        'harga',
        'is_layak'
    ];

    protected $casts = [
        'is_layak' => 'boolean',
        'volume' => 'decimal:2',
        'harga' => 'decimal:2'
    ];

    // 🔗 Relasi ke penerimaan
    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }

    // 🔗 Relasi ke item
    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item', 'id_item');
    }

    // 🔗 Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    // 🔗 Relasi ke satuan
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
