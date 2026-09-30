<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterJasa extends Model
{
    use HasFactory;

    protected $table = 'master_jasa';
    protected $primaryKey = 'id_job';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_job',
        'kode_motor',
        'keterangan',
        'harga',
    ];

    protected $casts = [
        'harga' => 'integer',
    ];
}
