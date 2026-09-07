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
                'reconocimiento_fallido',
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
