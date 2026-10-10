<?php

namespace Tests\Feature;

use App\Models\TipoMembresia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TipoMembresiaTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_tipos_membresia_list(): void
    {
        $user = User::factory()->create();
        TipoMembresia::create([
            'nombre' => 'Plan Mensual Test',
            'duracion_dias' => 30,
            'precio' => 50.00,
            'descripcion' => 'Descripción de prueba',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($user)->get(route('membresias.index'));

        $response->assertStatus(200);
        $response->assertSee('Plan Mensual Test');
        $response->assertSee('50.00');
    }

    public function test_user_can_create_tipo_membresia(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('membresias.store'), [
            'nombre' => 'Plan Trimestral Pro',
            'duracion_dias' => 90,
            'precio' => 135.50,
            'descripcion' => 'Beneficios VIP',
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('membresias.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tipos_membresia', [
            'nombre' => 'Plan Trimestral Pro',
            'duracion_dias' => 90,
            'precio' => 135.50,
        ]);
    }

    public function test_user_can_update_tipo_membresia(): void
    {
        $user = User::factory()->create();
        $plan = TipoMembresia::create([
            'nombre' => 'Plan Inicial',
            'duracion_dias' => 15,
            'precio' => 20.00,
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($user)->put(route('membresias.update', $plan), [
            'nombre' => 'Plan Inicial Actualizado',
            'duracion_dias' => 20,
            'precio' => 25.00,
            'descripcion' => 'Nueva descripción',
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('membresias.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tipos_membresia', [
            'id' => $plan->id,
            'nombre' => 'Plan Inicial Actualizado',
            'precio' => 25.00,
        ]);
    }

    public function test_user_can_toggle_tipo_membresia_status(): void
    {
        $user = User::factory()->create();
        $plan = TipoMembresia::create([
            'nombre' => 'Plan Toggle',
            'duracion_dias' => 30,
            'precio' => 40.00,
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($user)->patch(route('membresias.toggleStatus', $plan));

        $response->assertRedirect(route('membresias.index'));
        $this->assertEquals('inactivo', $plan->fresh()->estado);
    }

    public function test_user_can_delete_tipo_membresia(): void
    {
        $user = User::factory()->create();
        $plan = TipoMembresia::create([
            'nombre' => 'Plan a Eliminar',
            'duracion_dias' => 30,
            'precio' => 30.00,
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($user)->delete(route('membresias.destroy', $plan));

        $response->assertRedirect(route('membresias.index'));
        $this->assertSoftDeleted('tipos_membresia', ['id' => $plan->id]);
    }
}
