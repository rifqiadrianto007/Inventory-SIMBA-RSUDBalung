<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $table = 'item';
    protected $primaryKey = 'id_item';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'id_category',
        'stock_item',
        'id_unit'
    ];

    protected $casts = [
        'stock_item' => 'integer'
    ];

    // 🔗 Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    // 🔗 Relasi ke satuan (unit)
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_unit', 'id_satuan');
    }

    // 🔗 Relasi ke detail penerimaan (1 item bisa muncul di banyak detail penerimaan)
    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_item', 'id_item');
    }
}
