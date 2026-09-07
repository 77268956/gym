<?php

namespace App\Models;

use Database\Factories\ConfiguracionPuntoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionPunto extends Model
{
    /** @use HasFactory<ConfiguracionPuntoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'puntos_por_visita',
        'vigente_desde',
    ];

    protected $casts = [
        'vigente_desde' => 'date',
    ];
}
