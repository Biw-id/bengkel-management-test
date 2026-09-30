<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailWorkOrder extends Model
{
    use HasFactory;

    protected $table = 'detail_work_order';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_wo',
        'jenis_penanganan',
        'biaya',
        'deskripsi',
        'qty',
    ];

    protected $casts = [
        'biaya' => 'integer',
        'qty' => 'integer',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'id_wo', 'id_wo');
    }
}
