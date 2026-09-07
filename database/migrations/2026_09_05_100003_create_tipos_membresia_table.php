<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_membresia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->comment('1 Mes, 3 Meses, 6 Meses, 1 Año');
            $table->integer('duracion_dias');
            $table->decimal('precio', 10, 2);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_membresia');
    }
};
