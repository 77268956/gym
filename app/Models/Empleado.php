<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Empleado extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'cedula',
        'usuario',
        'password_hash',
        'rol',
        'foto_referencia',
        'hora_entrada_turno',
        'hora_salida_turno',
        'tolerancia_minutos',
        'estado',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function asistenciasClientesValidadas()
    {
        return $this->hasMany(AsistenciaCliente::class, 'empleado_valida_id');
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaEmpleado::class);
    }

    public function canjesProcesados()
    {
        return $this->hasMany(CanjeEcogim::class, 'empleado_id');
    }
}
