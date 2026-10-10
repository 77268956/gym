<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Pago;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pago>
 */
class PagoFactory extends Factory
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
            'empleado_id' => Empleado::factory(),
            'membresia_id' => null,
            'tipo_pago' => 'membresia',
            'metodo_pago' => fake()->randomElement(['efectivo', 'tarjeta', 'transferencia']),
            'monto' => 45.00,
            'concepto' => 'Membresía',
            'estado' => 'pagado',
            'fecha_pago' => now(),
        ];
    }

    public function paseDiario(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_pago' => 'pase_diario',
            'membresia_id' => null,
            'monto' => 10.00,
            'concepto' => 'Pase del día',
        ]);
    }
}
