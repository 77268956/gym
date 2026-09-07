<?php

namespace App\Models;

use Database\Factories\AsistenciaClienteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsistenciaCliente extends Model
{
    /** @use HasFactory<AsistenciaClienteFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'asistencias_clientes';

    protected $fillable = [
        'cliente_id',
        'empleado_valida_id',
        'fecha',
        'hora',
        'metodo_registro',
        'puntos_otorgados',
    ];

    protected $casts = [
        'fecha' => 'date',
        'puntos_otorgados' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleadoValida()
    {
        return $this->belongsTo(Empleado::class, 'empleado_valida_id');
    }

    public function movimientosPuntos()
    {
        return $this->morphMany(MovimientoPunto::class, 'origen');
    }
}
