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
