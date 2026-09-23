<?php

namespace Tests\Feature;

use App\Models\Empleado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmpleadoTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): Empleado
    {
        return Empleado::factory()->create([
            'nombre' => 'Admin Boss',
            'cedula' => '0801-0000-00000',
            'usuario' => 'admin_'.uniqid(),
            'password_hash' => Hash::make('password123'),
            'rol' => 'admin',
            'estado' => 'activo',
        ]);
    }

    public function test_user_can_view_empleados_list(): void
    {
        $admin = $this->createAdmin();
        Empleado::create([
            'nombre' => 'Empleado Test',
            'cedula' => '0801-1999-99999',
            'usuario' => 'emptest',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)->get(route('empleados'));

        $response->assertStatus(200);
        $response->assertSee('Empleado Test');
        $response->assertSee('0801-1999-99999');
    }

    public function test_user_can_access_create_empleado_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('empleados.create'));

        $response->assertStatus(200);
        $response->assertSee('Nuevo Empleado');
    }

    public function test_user_can_create_empleado(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('empleados.store'), [
            'nombre' => 'Nuevo Recepcionista',
            'cedula' => '0801-2000-11223',
            'usuario' => 'nrecep',
            'password' => 'secret123',
            'rol' => 'empleado',
            'hora_entrada_turno' => '08:00',
            'hora_salida_turno' => '16:00',
            'tolerancia_minutos' => 15,
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('empleados'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('empleados', [
            'nombre' => 'Nuevo Recepcionista',
            'cedula' => '0801-2000-11223',
            'usuario' => 'nrecep',
            'rol' => 'empleado',
        ]);
    }

    public function test_user_can_access_edit_empleado_page(): void
    {
        $admin = $this->createAdmin();
        $emp = Empleado::create([
            'nombre' => 'Empleado a Editar',
            'cedula' => '0801-1991-88888',
            'usuario' => 'empedit',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)->get(route('empleados.edit', $emp));

        $response->assertStatus(200);
        $response->assertSee('Editar Empleado');
    }

    public function test_user_can_update_empleado(): void
    {
        $admin = $this->createAdmin();
        $emp = Empleado::create([
            'nombre' => 'Empleado Nombre Viejo',
            'cedula' => '0801-1992-77777',
            'usuario' => 'empviejo',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)->put(route('empleados.update', $emp), [
            'nombre' => 'Empleado Nombre Nuevo',
            'cedula' => '0801-1992-77777',
            'usuario' => 'empnuevo',
            'rol' => 'admin',
            'hora_entrada_turno' => '09:00',
            'hora_salida_turno' => '17:00',
            'tolerancia_minutos' => 10,
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('empleados'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('empleados', [
            'id' => $emp->id,
            'nombre' => 'Empleado Nombre Nuevo',
            'usuario' => 'empnuevo',
            'rol' => 'admin',
        ]);
    }

    public function test_user_can_toggle_empleado_status(): void
    {
        $admin = $this->createAdmin();
        $emp = Empleado::create([
            'nombre' => 'Empleado Toggle',
            'cedula' => '0801-1993-66666',
            'usuario' => 'emptoggle',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)->patch(route('empleados.toggleStatus', $emp));

        $response->assertRedirect(route('empleados'));
        $this->assertEquals('inactivo', $emp->fresh()->estado);
    }

    public function test_user_can_delete_empleado(): void
    {
        $admin = $this->createAdmin();
        $emp = Empleado::create([
            'nombre' => 'Empleado Borrar',
            'cedula' => '0801-1994-55555',
            'usuario' => 'empborrar',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)->delete(route('empleados.destroy', $emp));

        $response->assertRedirect(route('empleados'));
        $this->assertSoftDeleted('empleados', ['id' => $emp->id]);
    }
}
