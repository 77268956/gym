<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuraciones_generales', function (Blueprint $table) {
            $table->string('moneda', 50)->default('Lempira')->after('logo_path');
            $table->string('simbolo_moneda', 10)->default('L.')->after('moneda');
            $table->string('codigo_moneda', 5)->default('HNL')->after('simbolo_moneda');
        });
    }

    public function down(): void
    {
        Schema::table('configuraciones_generales', function (Blueprint $table) {
            $table->dropColumn(['moneda', 'simbolo_moneda', 'codigo_moneda']);
        });
    }
};