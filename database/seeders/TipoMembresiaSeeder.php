<?php

namespace Database\Seeders;

use App\Models\TipoMembresia;
use Illuminate\Database\Seeder;

class TipoMembresiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $planes = [
            [
                'nombre' => '1 Mes',
                'duracion_dias' => 30,
                'precio' => 45.00,
                'descripcion' => 'Acceso libre a área de pesas, cardio y clases grupales básicas por 30 días.',
                'estado' => 'activo',
            ],
            [
                'nombre' => '3 Meses (Trimestral)',
                'duracion_dias' => 90,
                'precio' => 120.00,
                'descripcion' => 'Ahorro del 11%. Incluye evaluación física inicial, área de cardio y pesas.',
                'estado' => 'activo',
            ],
            [
                'nombre' => '6 Meses (Semestral)',
                'duracion_dias' => 180,
                'precio' => 220.00,
                'descripcion' => 'Ahorro del 18%. Incluye acceso a lockers, entrenamiento guiado y clases VIP.',
                'estado' => 'activo',
            ],
            [
                'nombre' => '1 Año (Anual Pass)',
                'duracion_dias' => 365,
                'precio' => 399.00,
                'descripcion' => 'Pase ilimitado anual. Incluye nutricionista, sauna, invitados gratis y 500 pts EcoGim.',
                'estado' => 'activo',
            ],
        ];

        foreach ($planes as $plan) {
            TipoMembresia::firstOrCreate(
                ['nombre' => $plan['nombre']],
                $plan
            );
        }
    }
}
