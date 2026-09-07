<?php

namespace App\Models;

use Database\Factories\ProductoEcogimFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoEcogim extends Model
{
    /** @use HasFactory<ProductoEcogimFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'productos_ecogim';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'puntos_valor',
        'stock',
        'estado',
    ];

    public function canjes()
    {
        return $this->hasMany(CanjeEcogim::class, 'producto_id');
    }
}
