<?php

namespace Database\Factories;

use App\Models\AsistenciaCliente;
use App\Models\Cliente;
use App\Models\Empleado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AsistenciaCliente>
 */
class AsistenciaClienteFactory extends Factory
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
            'empleado_valida_id' => Empleado::factory(),
            'fecha' => now()->toDateString(),
            'hora' => now()->format('H:i:s'),
            'hora_salida' => null,
            'metodo_registro' => 'escaner',
            'puntos_otorgados' => true,
            'exitoso' => true,
            'motivo_rechazo' => null,
        ];
    }

    public function exitosa(): static
    {
        return $this->state(fn (array $attributes) => [
            'exitoso' => true,
            'puntos_otorgados' => true,
            'motivo_rechazo' => null,
        ]);
    }

    public function fallida(): static
    {
        return $this->state(fn (array $attributes) => [
            'exitoso' => false,
            'puntos_otorgados' => false,
            'motivo_rechazo' => 'Sin membresía activa',
        ]);
    }
}
