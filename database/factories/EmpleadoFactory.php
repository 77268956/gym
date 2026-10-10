<?php

namespace Database\Factories;

use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Empleado>
 */
class EmpleadoFactory extends Factory
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
            'cedula' => fake()->unique()->numerify('######-#'),
            'usuario' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'foto_referencia' => null,
            'descriptor_facial' => null,
            'hora_entrada_turno' => '08:00:00',
            'hora_salida_turno' => '17:00:00',
            'tolerancia_minutos' => 10,
            'estado' => 'activo',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'admin',
        ]);
    }

    public function empleado(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'empleado',
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }
}
