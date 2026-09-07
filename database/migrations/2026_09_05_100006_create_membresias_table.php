<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membresias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('tipo_membresia_id')->constrained('tipos_membresia');
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento')->index('idx_membresias_vencimiento')->comment('clave para alerta preventiva');
            $table->enum('estado', ['activa', 'vencida'])->default('activa')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
