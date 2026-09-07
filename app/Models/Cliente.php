<?php

namespace App\Models;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'cedula',
        'telefono',
        'foto_referencia',
        'historial_medico',
        'puntos_ecogim',
        'estado',
        'ultima_actividad',
    ];

    protected $casts = [
        'ultima_actividad' => 'datetime',
    ];

    public function membresias()
    {
        return $this->hasMany(Membresia::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function pasesDiarios()
    {
        return $this->hasMany(PaseDiario::class);
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaCliente::class);
    }

    public function movimientosPuntos()
    {
        return $this->hasMany(MovimientoPunto::class);
    }

    public function canjes()
    {
        return $this->hasMany(CanjeEcogim::class);
    }
}
