<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_ecogim', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('categoria', 50)->nullable()->index()->comment('suplementos, toallas, termos, etc.');
            $table->integer('puntos_valor');
            $table->integer('stock')->default(0);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_ecogim');
    }
};
