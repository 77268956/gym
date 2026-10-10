<?php

namespace Database\Factories;

use App\Models\ProductoEcogim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductoEcogim>
 */
class ProductoEcogimFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Producto '.fake()->unique()->randomNumber(3),
            'descripcion' => fake()->sentence(),
            'categoria' => fake()->randomElement(['bebida', 'comida', 'suplemento', 'accesorio']),
            'puntos_valor' => fake()->numberBetween(50, 500),
            'stock' => fake()->numberBetween(10, 100),
            'imagen' => null,
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
