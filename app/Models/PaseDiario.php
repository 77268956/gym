<?php

namespace App\Models;

use Database\Factories\PaseDiarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaseDiario extends Model
{
    /** @use HasFactory<PaseDiarioFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'pases_diarios';

    protected $fillable = [
        'cliente_id',
        'pago_id',
        'fecha',
        'otorga_asistencia',
    ];

    protected $casts = [
        'fecha' => 'date',
        'otorga_asistencia' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
