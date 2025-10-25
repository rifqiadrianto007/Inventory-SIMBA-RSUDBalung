<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'category';
    protected $primaryKey = 'id_category';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['name'];

    // 🔗 Relasi ke item (1 kategori bisa punya banyak item)
    public function items()
    {
        return $this->hasMany(Item::class, 'id_category', 'id_category');
    }

    // 🔗 Relasi ke detail penerimaan (jika ingin akses langsung)
    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_category', 'id_category');
    }
}
