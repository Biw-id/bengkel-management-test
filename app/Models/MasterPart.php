<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterPart extends Model
{
    use HasFactory;

    protected $table = 'master_part';
    protected $primaryKey = 'part_number';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'part_number',
        'nama_part',
        'harga',
        'qty_stock',
        'qty_rfs',
        'qty_book',
    ];

    protected $casts = [
        'harga' => 'integer',
        'qty_stock' => 'integer',
        'qty_rfs' => 'integer',
        'qty_book' => 'integer',
    ];

    public function notaSukuCadangH3(): HasMany
    {
        return $this->hasMany(NotaSukuCadangH3::class, 'part_number', 'part_number');
    }
}
