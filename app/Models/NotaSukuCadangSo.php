<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotaSukuCadangSo extends Model
{
    use HasFactory;

    protected $table = 'nota_suku_cadang_so';
    protected $primaryKey = 'no_nsc';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'no_nsc',
        'tgl_nsc',
        'id_customer',
        'amount',
        'id_wo',
        'diskon_part',
        'total_amount',
    ];

    protected $casts = [
        'tgl_nsc' => 'datetime',
        'amount' => 'integer',
        'diskon_part' => 'integer',
        'total_amount' => 'integer',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'id_wo', 'id_wo');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(MasterCustomer::class, 'id_customer', 'id_customer');
    }

    public function notaSukuCadangH3(): HasMany
    {
        return $this->hasMany(NotaSukuCadangH3::class, 'no_nsc', 'no_nsc');
    }
}
