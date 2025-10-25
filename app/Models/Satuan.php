<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Satuan extends Model
{
    use SoftDeletes;

    protected $table = 'satuan';
    protected $primaryKey = 'id_satuan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['name'];

    // 🔗 Relasi ke item (satu satuan bisa digunakan banyak item)
    public function items()
    {
        return $this->hasMany(Item::class, 'id_unit', 'id_satuan');
    }

    // 🔗 Relasi ke detail penerimaan
    public function detailPenerimaan()
    {
        return $this->hasMany(DetailPenerimaan::class, 'id_satuan', 'id_satuan');
    }
}
