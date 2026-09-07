<?php

namespace App\Models;

use Database\Factories\MembresiaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membresia extends Model
{
    /** @use HasFactory<MembresiaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'tipo_membresia_id',
        'fecha_inicio',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tipoMembresia()
    {
        return $this->belongsTo(TipoMembresia::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
