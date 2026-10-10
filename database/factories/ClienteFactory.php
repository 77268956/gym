<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'cedula' => fake()->unique()->numerify('########-#'),
            'telefono' => fake()->numerify('7###-####'),
            'email' => fake()->unique()->safeEmail(),
            'fecha_nacimiento' => fake()->date('Y-m-d', '-18 years'),
            'direccion' => fake()->address(),
            'foto_referencia' => null,
            'descriptor_facial' => null,
            'historial_medico' => null,
            'puntos_ecogim' => 0,
            'estado' => 'activo',
            'ultima_actividad' => null,
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

    public function conPuntos(int $puntos = 500): static
    {
        return $this->state(fn (array $attributes) => [
            'puntos_ecogim' => $puntos,
        ]);
    }
}
