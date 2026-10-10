<?php

namespace Database\Factories;

use App\Models\TipoMembresia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoMembresia>
 */
class TipoMembresiaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dias = [30, 60, 90, 180, 365];
        $precio = [45, 80, 120, 220, 399];

        return [
            'nombre' => 'Plan '.fake()->unique()->randomNumber(3),
            'duracion_dias' => fake()->randomElement($dias),
            'precio' => fake()->randomElement($precio),
            'descripcion' => fake()->sentence(),
            'estado' => 'activo',
        ];
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activo',
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }
}
