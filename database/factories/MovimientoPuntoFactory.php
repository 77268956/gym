<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\MovimientoPunto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MovimientoPunto>
 */
class MovimientoPuntoFactory extends Factory
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
            'tipo_movimiento' => 'ganado',
            'puntos' => 1,
            'origen_tabla' => 'asistencias_clientes',
            'origen_id' => fake()->randomNumber(5),
            'fecha' => now(),
        ];
    }

    public function ganado(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_movimiento' => 'ganado',
            'puntos' => 1,
        ]);
    }

    public function canjeado(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_movimiento' => 'canjeado',
            'puntos' => -fake()->numberBetween(50, 500),
        ]);
    }
}
