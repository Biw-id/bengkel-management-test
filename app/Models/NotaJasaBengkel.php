<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaJasaBengkel extends Model
{
    use HasFactory;

    protected $table = 'nota_jasa_bengkel';
    protected $primaryKey = 'id_njb';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_njb',
        'id_wo',
        'waktu_njb',
        'id_mekanik',
        'tipe_unit',
    ];

    protected $casts = [
        'waktu_njb' => 'datetime',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'id_wo', 'id_wo');
    }

    public function mekanik(): BelongsTo
    {
        return $this->belongsTo(MasterMekanik::class, 'id_mekanik', 'honda_id_mekanik');
    }
}
