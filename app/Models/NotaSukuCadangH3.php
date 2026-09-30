<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaSukuCadangH3 extends Model
{
    use HasFactory;

    protected $table = 'nota_suku_cadang_h3';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_nsc',
        'part_number',
        'qty',
        'harga',
        'jumlah_bayar',
        'id_wo',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga' => 'integer',
        'jumlah_bayar' => 'integer',
    ];

    public function notaSukuCadangSo(): BelongsTo
    {
        return $this->belongsTo(NotaSukuCadangSo::class, 'no_nsc', 'no_nsc');
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(MasterPart::class, 'part_number', 'part_number');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'id_wo', 'id_wo');
    }
}
