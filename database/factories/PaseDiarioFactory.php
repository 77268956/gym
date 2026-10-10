<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\PaseDiario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaseDiario>
 */
class PaseDiarioFactory extends Factory
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
            'pago_id' => Pago::factory(),
            'fecha' => now()->toDateString(),
            'otorga_asistencia' => true,
        ];
    }
}
