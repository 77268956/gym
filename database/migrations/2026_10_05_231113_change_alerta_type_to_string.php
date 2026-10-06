<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alertas_sistema', function (Blueprint $table): void {
            $table->string('tipo_alerta')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertas_sistema', function (Blueprint $table): void {
            $table->enum('tipo_alerta', [
                'salida_no_registrada',
                'exceso_salidas_no_registradas',
                'membresia_por_vencer',
                'reconocimiento_fallido',
                'intentos_escaner',
                'entrada_tardia',
                'salida_temprana',
                'ausencia',
            ])->change();
        });
    }
};
