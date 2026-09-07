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
