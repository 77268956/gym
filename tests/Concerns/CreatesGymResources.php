<?php

namespace Tests\Concerns;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Pago;
use App\Models\TipoMembresia;
use Illuminate\Support\Facades\Hash;

trait CreatesGymResources
{
    protected function createStaff(array $overrides = []): Empleado
    {
        return Empleado::factory()->create(array_merge([
            'nombre' => 'Personal de prueba',
            'cedula' => fake()->unique()->numerify('STAFF-#####'),
            'usuario' => 'staff',
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ], $overrides));
    }

    protected function createAdmin(array $overrides = []): Empleado
    {
        return Empleado::factory()->create(array_merge([
            'nombre' => 'Admin',
            'cedula' => fake()->unique()->numerify('ADMIN-#####'),
            'usuario' => 'admin',
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => Hash::make('admin123'),
            'rol' => 'admin',
            'estado' => 'activo',
        ], $overrides));
    }

    protected function createClient(array $overrides = []): Cliente
    {
        return Cliente::factory()->create(array_merge([
            'estado' => 'activo',
            'puntos_ecogim' => 0,
        ], $overrides));
    }

    protected function createPlan(array $overrides = []): TipoMembresia
    {
        return TipoMembresia::factory()->create(array_merge([
            'nombre' => 'Mensual',
            'duracion_dias' => 30,
            'precio' => 45.00,
            'estado' => 'activo',
        ], $overrides));
    }

    protected function createMembershipForCliente(Cliente $cliente, TipoMembresia $plan, int $daysAdded = 30, int $startDaysAgo = 0): Pago
    {
        $start = now()->subDays($startDaysAgo);
        $end = $start->copy()->addDays($daysAdded - 1)->endOfDay();

        $membership = $cliente->membresias()->create([
            'tipo_membresia_id' => $plan->id,
            'fecha_inicio' => $start,
            'fecha_vencimiento' => $end,
            'estado' => $end >= now() ? 'activa' : 'vencida',
        ]);

        $empleado = Empleado::factory()->create(['rol' => 'empleado', 'estado' => 'activo']);

        return Pago::create([
            'cliente_id' => $cliente->id,
            'empleado_id' => $empleado->id,
            'membresia_id' => $membership->id,
            'tipo_pago' => 'membresia',
            'metodo_pago' => 'efectivo',
            'monto' => $plan->precio,
            'concepto' => 'Membresía: '.$plan->nombre,
            'estado' => 'pagado',
            'fecha_pago' => $start,
        ]);
    }
}
