<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    use HasFactory;

    protected $table = 'work_order';
    protected $primaryKey = 'id_wo';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_wo',
        'id_form_sa',
        'waktu_wo',
        'status_wo',
        'id_mekanik',
        'total_bayar_jasa',
        'total_bayar_part',
        'total_bayar_keseluruhan',
    ];

    protected $casts = [
        'waktu_wo' => 'datetime',
        'total_bayar_jasa' => 'integer',
        'total_bayar_part' => 'integer',
        'total_bayar_keseluruhan' => 'integer',
    ];

    public function formServiceAdvisor(): BelongsTo
    {
        return $this->belongsTo(FormServiceAdvisor::class, 'id_form_sa', 'id_form_sa');
    }

    public function mekanik(): BelongsTo
    {
        return $this->belongsTo(MasterMekanik::class, 'id_mekanik', 'honda_id_mekanik');
    }

    public function detailWorkOrders(): HasMany
    {
        return $this->hasMany(DetailWorkOrder::class, 'id_wo', 'id_wo');
    }

    public function notaJasaBengkel(): HasMany
    {
        return $this->hasMany(NotaJasaBengkel::class, 'id_wo', 'id_wo');
    }

    public function notaSukuCadangSo(): HasMany
    {
        return $this->hasMany(NotaSukuCadangSo::class, 'id_wo', 'id_wo');
    }

    public function notaSukuCadangH3(): HasMany
    {
        return $this->hasMany(NotaSukuCadangH3::class, 'id_wo', 'id_wo');
    }
}
