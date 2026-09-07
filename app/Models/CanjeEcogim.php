<?php

namespace App\Models;

use Database\Factories\CanjeEcogimFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanjeEcogim extends Model
{
    /** @use HasFactory<CanjeEcogimFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'canjes_ecogim';

    protected $fillable = [
        'cliente_id',
        'producto_id',
        'empleado_id',
        'puntos_utilizados',
        'periodo_canje',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(ProductoEcogim::class, 'producto_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function movimientosPuntos()
    {
        return $this->morphMany(MovimientoPunto::class, 'origen');
    }
}
