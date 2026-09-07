<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_puntos', function (Blueprint $table) {
            $table->id();
            $table->integer('puntos_por_visita')->default(10);
            $table->date('vigente_desde')->index();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_puntos');
    }
};
