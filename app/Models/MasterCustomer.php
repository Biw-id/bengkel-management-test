<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterCustomer extends Model
{
    use HasFactory;

    protected $table = 'master_customer';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_customer',
        'nama_customer',
        'no_plat',
    ];

    public function formServiceAdvisors(): HasMany
    {
        return $this->hasMany(FormServiceAdvisor::class, 'id_customer', 'id_customer');
    }

    public function notaSukuCadangSo(): HasMany
    {
        return $this->hasMany(NotaSukuCadangSo::class, 'id_customer', 'id_customer');
    }
}
