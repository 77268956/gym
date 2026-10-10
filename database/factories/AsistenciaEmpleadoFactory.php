<?php

namespace Database\Factories;

use App\Models\AsistenciaEmpleado;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AsistenciaEmpleado>
 */
class AsistenciaEmpleadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'empleado_id' => Empleado::factory(),
            'fecha' => now()->toDateString(),
            'hora_entrada' => '08:00:00',
            'hora_salida' => '17:00:00',
            'horas_trabajadas' => 9.00,
            'tardanza' => false,
            'salida_temprana' => false,
            'salida_no_registrada' => false,
            'metodo_registro' => 'escaner',
        ];
    }

    public function conTardanza(): static
    {
        return $this->state(fn (array $attributes) => [
            'hora_entrada' => '08:15:00',
            'tardanza' => true,
        ]);
    }
}
