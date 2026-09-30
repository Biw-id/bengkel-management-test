<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormServiceAdvisor extends Model
{
    use HasFactory;

    protected $table = 'form_service_advisor';
    protected $primaryKey = 'id_form_sa';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id_form_sa',
        'id_customer',
        'waktu_form_sa',
        'keluhan_konsumen',
        'no_plat',
    ];

    protected $casts = [
        'waktu_form_sa' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(MasterCustomer::class, 'id_customer', 'id_customer');
    }

    public function workOrder(): HasOne
    {
        return $this->hasOne(WorkOrder::class, 'id_form_sa', 'id_form_sa');
    }
}
