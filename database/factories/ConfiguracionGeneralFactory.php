<?php

namespace Database\Factories;

use App\Models\ConfiguracionGeneral;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConfiguracionGeneral>
 */
class ConfiguracionGeneralFactory extends Factory
{
    protected $model = ConfiguracionGeneral::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_gimnasio' => 'GymX',
            'logo_path' => null,
            'mensaje_whatsapp' => '¡Bienvenido a GymX! Agenda tu clase hoy.',
            'moneda' => 'USD',
            'simbolo_moneda' => '$',
            'codigo_moneda' => 'USD',
            'color_primario' => '#2563EB',
            'color_primario_hover' => '#1d4ed8',
            'color_sidebar' => '#111214',
            'color_sidebar_hover' => '#1f2937',
        ];
    }
}
