<?php

namespace App\Models;

use Database\Factories\PagoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    /** @use HasFactory<PagoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'empleado_id',
        'membresia_id',
        'tipo_pago',
        'metodo_pago',
        'monto',
        'fecha_pago',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function membresia()
    {
        return $this->belongsTo(Membresia::class);
    }

    public function paseDiario()
    {
        return $this->hasOne(PaseDiario::class);
    }
}
