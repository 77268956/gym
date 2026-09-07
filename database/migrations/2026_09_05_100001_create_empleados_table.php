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
