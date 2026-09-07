<?php

namespace App\Models;

use Database\Factories\AsistenciaEmpleadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsistenciaEmpleado extends Model
{
    /** @use HasFactory<AsistenciaEmpleadoFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'asistencias_empleados';

    protected $fillable = [
        'empleado_id',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'horas_trabajadas',
        'tardanza',
        'salida_temprana',
        'salida_no_registrada',
        'metodo_registro',
    ];

    protected $casts = [
        'fecha' => 'date',
        'horas_trabajadas' => 'decimal:2',
        'tardanza' => 'boolean',
        'salida_temprana' => 'boolean',
        'salida_no_registrada' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
