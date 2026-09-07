<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_puntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->enum('tipo_movimiento', ['ganado', 'canjeado']);
            $table->integer('puntos');
            $table->string('origen_tabla', 30)->nullable();
            $table->unsignedBigInteger('origen_id')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
            $table->softDeletes()->index();

            $table->index(['origen_tabla', 'origen_id'], 'idx_movimiento_origen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_puntos');
    }
};
