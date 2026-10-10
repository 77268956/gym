<?php

namespace Tests\Feature\Clientes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_client(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->post(route('clientes.store'), [
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'cedula' => '12345678-9',
            'telefono' => '7012-3456',
            'email' => 'juan@example.test',
            'fecha_nacimiento' => '1995-05-10',
            'direccion' => 'San Salvador',
            'tipo_membresia_id' => $this->createPlan()->id,
            'fecha_inicio' => now()->toDateString(),
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertRedirect(route('user'));
        $this->assertDatabaseHas('clientes', [
            'email' => 'juan@example.test',
            'estado' => 'activo',
        ]);
    }

    public function test_admin_can_update_and_toggle_client(): void
    {
        $admin = $this->createAdmin();
        $client = $this->createClient(['estado' => 'activo']);

        $response = $this->actingAs($admin)->put(route('clientes.update', $client), [
            'nombre' => 'Juan Updated',
            'apellido' => 'Pérez',
            'cedula' => '12345678-9',
            'telefono' => '7012-3456',
            'email' => 'juan@example.test',
            'fecha_nacimiento' => '1995-05-10',
            'direccion' => 'Updated address',
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('clientes.show', $client));
        $this->assertDatabaseHas('clientes', [
            'id' => $client->id,
            'nombre' => 'Juan Updated',
        ]);

        $toggle = $this->actingAs($admin)->patch(route('clientes.toggleStatus', $client->fresh()));
        $toggle->assertRedirect();
        $this->assertEquals('inactivo', $client->fresh()->estado);
    }

    public function test_admin_can_delete_client(): void
    {
        $admin = $this->createAdmin();
        $client = $this->createClient();

        $response = $this->actingAs($admin)->delete(route('clientes.destroy', $client));

        $response->assertRedirect(route('user'));
        $this->assertSoftDeleted('clientes', ['id' => $client->id]);
    }

    public function test_can_show_client(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();

        $response = $this->actingAs($staff)->get(route('clientes.show', $client));

        $response->assertStatus(200);
    }
}
