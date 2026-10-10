<?php

namespace Database\Factories;

use App\Models\CanjeEcogim;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\ProductoEcogim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CanjeEcogim>
 */
class CanjeEcogimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'producto_id' => ProductoEcogim::factory(),
            'empleado_id' => Empleado::factory(),
            'puntos_utilizados' => 100,
            'periodo_canje' => now()->format('Y-m'),
            'fecha' => now(),
        ];
    }
}
