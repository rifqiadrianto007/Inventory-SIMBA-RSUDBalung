<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
    protected $table = 'bast';
    protected $primaryKey = 'id_bast';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'no_surat',
        'id_penerimaan',
        'deskripsi',
        'file_path'
    ];

    // 🔗 Relasi ke penerimaan
    public function penerimaan()
    {
        return $this->belongsTo(Penerimaan::class, 'id_penerimaan', 'id_penerimaan');
    }
}
