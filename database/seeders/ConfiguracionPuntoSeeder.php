<?php

namespace Database\Seeders;

use App\Models\ConfiguracionPunto;
use Illuminate\Database\Seeder;

class ConfiguracionPuntoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ConfiguracionPunto::firstOrCreate([], [
            'puntos_por_visita' => 1,
            'vigente_desde' => now()->toDateString(),
        ]);
    }
}
