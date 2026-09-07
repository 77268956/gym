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
