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
        Schema::table('asistencias_clientes', function (Blueprint $table) {
            $table->boolean('exitoso')->default(true)->after('metodo_registro');
            $table->string('motivo_rechazo')->nullable()->after('exitoso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias_clientes', function (Blueprint $table) {
            $table->dropColumn(['exitoso', 'motivo_rechazo']);
        });
    }
};
