<?php

$models = [
    'Empleado.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    /** @use HasFactory<\Database\Factories\EmpleadoFactory> */
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
EOT,

    'Cliente.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'cedula',
        'telefono',
        'foto_referencia',
        'historial_medico',
        'puntos_ecogim',
        'estado',
        'ultima_actividad',
    ];

    protected $casts = [
        'ultima_actividad' => 'datetime',
    ];

    public function membresias()
    {
        return $this->hasMany(Membresia::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function pasesDiarios()
    {
        return $this->hasMany(PaseDiario::class);
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaCliente::class);
    }

    public function movimientosPuntos()
    {
        return $this->hasMany(MovimientoPunto::class);
    }

    public function canjes()
    {
        return $this->hasMany(CanjeEcogim::class);
    }
}
EOT,

    'TipoMembresia.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoMembresia extends Model
{
    /** @use HasFactory<\Database\Factories\TipoMembresiaFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_membresia';

    protected $fillable = [
        'nombre',
        'duracion_dias',
        'precio',
        'estado',
    ];

    public function membresias()
    {
        return $this->hasMany(Membresia::class);
    }
}
EOT,

    'Membresia.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membresia extends Model
{
    /** @use HasFactory<\Database\Factories\MembresiaFactory> */
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
EOT,

    'Pago.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    /** @use HasFactory<\Database\Factories\PagoFactory> */
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
EOT,

    'PaseDiario.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaseDiario extends Model
{
    /** @use HasFactory<\Database\Factories\PaseDiarioFactory> */
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
EOT,

    'AsistenciaCliente.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsistenciaCliente extends Model
{
    /** @use HasFactory<\Database\Factories\AsistenciaClienteFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'asistencias_clientes';

    protected $fillable = [
        'cliente_id',
        'empleado_valida_id',
        'fecha',
        'hora',
        'metodo_registro',
        'puntos_otorgados',
    ];

    protected $casts = [
        'fecha' => 'date',
        'puntos_otorgados' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function empleadoValida()
    {
        return $this->belongsTo(Empleado::class, 'empleado_valida_id');
    }

    public function movimientosPuntos()
    {
        return $this->morphMany(MovimientoPunto::class, 'origen');
    }
}
EOT,

    'AsistenciaEmpleado.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsistenciaEmpleado extends Model
{
    /** @use HasFactory<\Database\Factories\AsistenciaEmpleadoFactory> */
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
EOT,

    'ConfiguracionPunto.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionPunto extends Model
{
    /** @use HasFactory<\Database\Factories\ConfiguracionPuntoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'puntos_por_visita',
        'vigente_desde',
    ];

    protected $casts = [
        'vigente_desde' => 'date',
    ];
}
EOT,

    'MovimientoPunto.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoPunto extends Model
{
    /** @use HasFactory<\Database\Factories\MovimientoPuntoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'tipo_movimiento',
        'puntos',
        'origen_tabla',
        'origen_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function origen()
    {
        return $this->morphTo(__FUNCTION__, 'origen_tabla', 'origen_id');
    }
}
EOT,

    'ProductoEcogim.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoEcogim extends Model
{
    /** @use HasFactory<\Database\Factories\ProductoEcogimFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'productos_ecogim';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'puntos_valor',
        'stock',
        'estado',
    ];

    public function canjes()
    {
        return $this->hasMany(CanjeEcogim::class, 'producto_id');
    }
}
EOT,

    'CanjeEcogim.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CanjeEcogim extends Model
{
    /** @use HasFactory<\Database\Factories\CanjeEcogimFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'canjes_ecogim';

    protected $fillable = [
        'cliente_id',
        'producto_id',
        'empleado_id',
        'puntos_utilizados',
        'periodo_canje',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(ProductoEcogim::class, 'producto_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function movimientosPuntos()
    {
        return $this->morphMany(MovimientoPunto::class, 'origen');
    }
}
EOT,

    'AlertaSistema.php' => <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlertaSistema extends Model
{
    /** @use HasFactory<\Database\Factories\AlertaSistemaFactory> */
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
EOT,
];

foreach ($models as $file => $content) {
    file_put_contents(__DIR__.'/app/Models/'.$file, $content);
}

echo 'Modelos actualizados con éxito.\\n';
