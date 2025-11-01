<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use SoftDeletes;

    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';
    public $timestamps = true;

    protected $fillable = [
        'nama_jabatan',
    ];
}
