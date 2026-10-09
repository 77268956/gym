<?php

$migrations = [
    '2026_09_05_100001_create_empleados_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('cedula', 20)->unique('uq_empleados_cedula');
            $table->string('usuario', 50)->unique('uq_empleados_usuario');
            $table->string('password_hash', 255);
            $table->enum('rol', ['admin', 'empleado']);
            $table->string('foto_referencia', 255)->nullable();
            
            $table->time('hora_entrada_turno')->nullable()->comment('Hora esperada de entrada (ej: 08:00:00)');
            $table->time('hora_salida_turno')->nullable()->comment('Hora esperada de salida (ej: 16:00:00)');
            $table->integer('tolerancia_minutos')->default(10);
            
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
EOT,

    '2026_09_05_100002_create_clientes_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('cedula', 20)->unique('uq_clientes_cedula');
            $table->string('telefono', 20)->nullable();
            $table->string('foto_referencia', 255)->nullable()->comment('foto para reconocimiento facial');
            $table->text('historial_medico')->nullable();
            $table->integer('puntos_ecogim')->default(0)->comment('saldo acumulado, se recalcula desde movimientos_puntos');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->index()->comment('pasa a inactivo tras 60 dias sin pago/asistencia');
            $table->timestamp('ultima_actividad')->nullable()->index()->comment('ultimo pago o asistencia, para calcular inactivacion');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
EOT,

    '2026_09_05_100003_create_tipos_membresia_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_membresia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->comment('1 Mes, 3 Meses, 6 Meses, 1 Año');
            $table->integer('duracion_dias');
            $table->decimal('precio', 10, 2);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_membresia');
    }
};
EOT,

    '2026_09_05_100004_create_configuracion_puntos_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_puntos', function (Blueprint $table) {
            $table->id();
            $table->integer('puntos_por_visita')->default(10);
            $table->date('vigente_desde')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_puntos');
    }
};
EOT,

    '2026_09_05_100005_create_productos_ecogim_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_ecogim', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('categoria', 50)->nullable()->index()->comment('suplementos, toallas, termos, etc.');
            $table->integer('puntos_valor');
            $table->integer('stock')->default(0);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_ecogim');
    }
};
EOT,

    '2026_09_05_100006_create_membresias_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membresias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('tipo_membresia_id')->constrained('tipos_membresia');
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento')->index('idx_membresias_vencimiento')->comment('clave para alerta preventiva');
            $table->enum('estado', ['activa', 'vencida'])->default('activa')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
EOT,

    '2026_09_05_100007_create_pagos_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('empleado_id')->constrained('empleados')->comment('quién cobró');
            $table->foreignId('membresia_id')->nullable()->constrained('membresias');
            $table->enum('tipo_pago', ['membresia', 'pase_diario'])->index();
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia']);
            $table->decimal('monto', 10, 2);
            $table->timestamp('fecha_pago')->useCurrent()->index('idx_pagos_fecha');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
EOT,

    '2026_09_05_100008_create_pases_diarios_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pases_diarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('pago_id')->constrained('pagos');
            $table->date('fecha');
            $table->boolean('otorga_asistencia')->default(false)->comment('asistencia opcional sin activar plan');
            $table->timestamps();
            $table->softDeletes()->index();
            
            $table->index(['cliente_id', 'fecha'], 'idx_pase_cliente_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pases_diarios');
    }
};
EOT,

    '2026_09_05_100009_create_asistencias_clientes_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('empleado_valida_id')->nullable()->constrained('empleados');
            $table->date('fecha');
            $table->time('hora');
            $table->string('metodo_registro', 20)->default('facial');
            $table->boolean('puntos_otorgados')->default(false);
            $table->timestamps();
            $table->softDeletes()->index();
            
            $table->index(['cliente_id', 'fecha'], 'idx_asistencia_cliente_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias_clientes');
    }
};
EOT,

    '2026_09_05_100010_create_asistencias_empleados_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->decimal('horas_trabajadas', 5, 2)->nullable();
            
            $table->boolean('tardanza')->default(false);
            $table->boolean('salida_temprana')->default(false);
            $table->boolean('salida_no_registrada')->default(false);
            
            $table->string('metodo_registro', 20)->default('facial');
            $table->timestamps();
            $table->softDeletes()->index();
            
            $table->unique(['empleado_id', 'fecha'], 'uq_asistencia_empleado_dia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias_empleados');
    }
};
EOT,

    '2026_09_05_100011_create_movimientos_puntos_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_puntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->enum('tipo_movimiento', ['ganado', 'canjeado']);
            $table->integer('puntos');
            $table->string('origen_tabla', 30)->nullable();
            $table->unsignedBigInteger('origen_id')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
            $table->softDeletes()->index();
            
            $table->index(['origen_tabla', 'origen_id'], 'idx_movimiento_origen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_puntos');
    }
};
EOT,

    '2026_09_05_100012_create_canjes_ecogim_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canjes_ecogim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos_ecogim');
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->integer('puntos_utilizados');
            $table->char('periodo_canje', 7);
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
            $table->softDeletes()->index();
            
            $table->unique(['cliente_id', 'periodo_canje'], 'uq_canje_cliente_mes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canjes_ecogim');
    }
};
EOT,

    '2026_09_05_100013_create_alertas_sistema_table.php' => <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_sistema', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_alerta', [
                'salida_no_registrada',
                'exceso_salidas_no_registradas',
                'membresia_por_vencer',
                'reconocimiento_fallido'
            ]);
            $table->string('referencia_tabla', 30)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->string('mensaje', 255);
            $table->enum('estado', ['pendiente', 'atendida'])->default('pendiente')->index();
            $table->timestamps();
            $table->softDeletes()->index();
            
            $table->index(['referencia_tabla', 'referencia_id'], 'idx_alerta_origen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_sistema');
    }
};
EOT,
];

foreach ($migrations as $file => $content) {
    file_put_contents(__DIR__.'/database/migrations/'.$file, $content);
}

echo 'Migraciones generadas con exito.\\n';
