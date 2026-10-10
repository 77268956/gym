<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\TipoMembresia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Membresia>
 */
class MembresiaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inicio = now()->subDays(fake()->numberBetween(0, 60));
        $vencimiento = (clone $inicio)->addDays(fake()->numberBetween(30, 90));

        return [
            'cliente_id' => Cliente::factory(),
            'tipo_membresia_id' => TipoMembresia::factory(),
            'fecha_inicio' => $inicio,
            'fecha_vencimiento' => $vencimiento,
            'estado' => 'activa',
        ];
    }

    public function activa(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activa',
            'fecha_inicio' => now()->subDays(5),
            'fecha_vencimiento' => now()->addDays(25),
        ]);
    }

    public function vencida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'vencida',
            'fecha_inicio' => now()->subDays(60),
            'fecha_vencimiento' => now()->subDays(1),
        ]);
    }

    public function fechas(int $duracionDias = 30, int $diasAtras = 0): static
    {
        $inicio = now()->subDays($diasAtras);

        return $this->state(fn (array $attributes) => [
            'fecha_inicio' => $inicio,
            'fecha_vencimiento' => $inicio->copy()->addDays($duracionDias - 1)->endOfDay(),
        ]);
    }
}
