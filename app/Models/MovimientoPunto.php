<?php

namespace App\Models;

use Database\Factories\MovimientoPuntoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoPunto extends Model
{
    /** @use HasFactory<MovimientoPuntoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'tipo_movimiento',
        'puntos',
        'origen_tabla',
        'origen_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function origen()
    {
        return $this->morphTo(__FUNCTION__, 'origen_tabla', 'origen_id');
    }
}
