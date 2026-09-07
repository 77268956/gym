<?php

namespace App\Models;

use Database\Factories\AlertaSistemaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlertaSistema extends Model
{
    /** @use HasFactory<AlertaSistemaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'alertas_sistema';

    protected $fillable = [
        'tipo_alerta',
        'referencia_tabla',
        'referencia_id',
        'mensaje',
        'estado',
    ];

    public function referencia()
    {
        return $this->morphTo(__FUNCTION__, 'referencia_tabla', 'referencia_id');
    }
}
