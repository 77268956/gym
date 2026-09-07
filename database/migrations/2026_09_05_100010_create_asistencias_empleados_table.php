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
