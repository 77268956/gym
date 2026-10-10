<?php

namespace Database\Factories;

use App\Models\AlertaSistema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlertaSistema>
 */
class AlertaSistemaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo_alerta' => 'entrada_tardia',
            'referencia_tabla' => 'asistencias_empleados',
            'referencia_id' => fake()->randomNumber(5),
            'mensaje' => 'Alerta de entrada tardía',
            'estado' => 'pendiente',
        ];
    }

    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
        ]);
    }

    public function atendida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'atendida',
        ]);
    }
}
