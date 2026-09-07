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
