<?php

namespace App\Models;

use Database\Factories\ConfiguracionGeneralFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionGeneral extends Model
{
    /** @use HasFactory<ConfiguracionGeneralFactory> */
    use HasFactory;

    protected $table = 'configuraciones_generales';

    protected $fillable = [
        'nombre_gimnasio',
        'logo_path',
        'mensaje_whatsapp',
        'moneda',
        'simbolo_moneda',
        'codigo_moneda',
        'color_primario',
        'color_primario_hover',
        'color_sidebar',
        'color_sidebar_hover',
    ];
}
