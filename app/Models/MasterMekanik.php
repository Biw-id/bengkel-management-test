<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterMekanik extends Model
{
    use HasFactory;

    protected $table = 'master_mekanik';
    protected $primaryKey = 'id';

    protected $fillable = [
        'honda_id_mekanik',
        'nama_mekanik',
    ];

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'id_mekanik', 'honda_id_mekanik');
    }

    public function notaJasaBengkel(): HasMany
    {
        return $this->hasMany(NotaJasaBengkel::class, 'id_mekanik', 'honda_id_mekanik');
    }
}
