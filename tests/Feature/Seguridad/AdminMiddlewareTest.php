<?php

namespace Tests\Feature;

use App\Models\Empleado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_recepcionista_no_puede_acceder_a_rutas_de_administracion(): void
    {
        $recepcionista = Empleado::factory()->create([
            'nombre' => 'Ana Recepcionista',
            'cedula' => '001-1111111-1',
            'usuario' => 'recepcion1',
            'password_hash' => bcrypt('123456'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        // Intentar acceder a empleados
        $this->actingAs($recepcionista)
            ->get(route('empleados'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Intentar acceder a configuración
        $this->actingAs($recepcionista)
            ->get(route('configuracion.index'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        // Intentar acceder a gestión de tienda
        $this->actingAs($recepcionista)
            ->get(route('admin.productos.index'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_recepcionista_puede_acceder_a_vistas_de_recepcion(): void
    {
        $recepcionista = Empleado::factory()->create([
            'nombre' => 'Ana Recepcionista',
            'cedula' => '001-1111111-2',
            'usuario' => 'recepcion2',
            'password_hash' => bcrypt('123456'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $this->actingAs($recepcionista)->get(route('dashboard'))->assertOk();
        $this->actingAs($recepcionista)->get(route('user'))->assertOk();
        $this->actingAs($recepcionista)->get(route('clientes.create'))->assertOk();
        $this->actingAs($recepcionista)->get(route('pagos.index'))->assertOk();
        $this->actingAs($recepcionista)->get(route('membresias.index'))->assertOk();
        $this->actingAs($recepcionista)->get(route('asistencias.escanear'))->assertOk();
        $this->actingAs($recepcionista)->get(route('tienda.index'))->assertOk();
    }

    public function test_administrador_puede_acceder_a_rutas_de_administracion(): void
    {
        $admin = Empleado::factory()->create([
            'nombre' => 'Admin Boss',
            'cedula' => '001-1111111-3',
            'usuario' => 'adminboss',
            'password_hash' => bcrypt('123456'),
            'rol' => 'admin',
            'estado' => 'activo',
        ]);

        $this->actingAs($admin)->get(route('empleados'))->assertOk();
        $this->actingAs($admin)->get(route('configuracion.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.productos.index'))->assertOk();
    }
}
