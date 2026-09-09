<?php

namespace App\Models;

use Database\Factories\TipoMembresiaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoMembresia extends Model
{
    /** @use HasFactory<TipoMembresiaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_membresia';

    protected $fillable = [
        'nombre',
        'duracion_dias',
        'precio',
        'descripcion',
        'estado',
    ];

    public function membresias()
    {
        return $this->hasMany(Membresia::class);
    }
}
