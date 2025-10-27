<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBast extends Model
{
    protected $table = 'detail_bast';
    protected $primaryKey = 'id_detail_bast';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_bast',
        'id_item',
        'id_satuan',
        'volume',
        'keterangan'
    ];

    public function bast()
    {
        return $this->belongsTo(Bast::class, 'id_bast', 'id_bast');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item', 'id_item');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
