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
