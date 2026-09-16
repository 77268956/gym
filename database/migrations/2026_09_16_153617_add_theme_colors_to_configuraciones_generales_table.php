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
        Schema::table('configuraciones_generales', function (Blueprint $table) {
            $table->string('color_primario', 7)->default('#2563EB')->after('codigo_moneda');
            $table->string('color_primario_hover', 7)->default('#1D4ED8')->after('color_primario');
            $table->string('color_sidebar', 7)->default('#1E293B')->after('color_primario_hover');
            $table->string('color_sidebar_hover', 7)->default('#334155')->after('color_sidebar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuraciones_generales', function (Blueprint $table) {
            $table->dropColumn([
                'color_primario',
                'color_primario_hover',
                'color_sidebar',
                'color_sidebar_hover',
            ]);
        });
    }
};
