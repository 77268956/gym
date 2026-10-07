<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('empleados', 'email')) {
            Schema::table('empleados', function (Blueprint $table) {
                $table->string('email')->nullable()->after('usuario');
            });
        }

        $employeeEmailsDuplicated = DB::table('empleados')
            ->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! Schema::hasIndex('empleados', ['email'], 'unique') && ! $employeeEmailsDuplicated) {
            Schema::table('empleados', function (Blueprint $table) {
                $table->unique('email');
            });
        }

        Schema::table('clientes', function (Blueprint $table): void {
            if (! Schema::hasColumn('clientes', 'apellido')) {
                $table->string('apellido', 100)->nullable()->after('nombre');
            }
            if (! Schema::hasColumn('clientes', 'email')) {
                $table->string('email')->nullable()->after('telefono');
            }
            if (! Schema::hasColumn('clientes', 'fecha_nacimiento')) {
                $table->date('fecha_nacimiento')->nullable()->after('email');
            }
            if (! Schema::hasColumn('clientes', 'direccion')) {
                $table->text('direccion')->nullable()->after('fecha_nacimiento');
            }
        });

        $clientEmailsDuplicated = DB::table('clientes')
            ->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! Schema::hasIndex('clientes', ['email'], 'unique') && ! $clientEmailsDuplicated) {
            Schema::table('clientes', function (Blueprint $table): void {
                $table->unique('email');
            });
        }

        Schema::table('pagos', function (Blueprint $table): void {
            if (! Schema::hasColumn('pagos', 'concepto')) {
                $table->string('concepto', 255)->nullable()->after('monto');
            }
            if (! Schema::hasColumn('pagos', 'estado')) {
                $table->string('estado', 20)->default('pagado')->after('concepto')->index();
            }
        });

        if (! Schema::hasColumn('asistencias_clientes', 'hora_salida')) {
            Schema::table('asistencias_clientes', function (Blueprint $table): void {
                $table->time('hora_salida')->nullable()->after('hora');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing manually provisioned columns are retained to avoid data loss on rollback.
    }
};
