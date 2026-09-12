<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionGeneral extends Model
{
    protected $table = 'configuraciones_generales';

    protected $fillable = [
        'nombre_gimnasio',
        'logo_path',
        'moneda',
        'simbolo_moneda',
        'codigo_moneda',
    ];
}