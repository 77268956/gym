<?php

namespace Tests\Feature\Notificaciones;

use App\Models\AlertaSistema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_notificaciones_index(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('notificaciones.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_filter_notificaciones(): void
    {
        $admin = $this->createAdmin();
        AlertaSistema::factory()->create(['estado' => 'pendiente']);

        $response = $this->actingAs($admin)->get(route('notificaciones.index', ['estado' => 'pendiente']));

        $response->assertStatus(200);
    }

    public function test_admin_can_mark_alert_as_attended(): void
    {
        $admin = $this->createAdmin();
        $alerta = AlertaSistema::factory()->create(['estado' => 'pendiente']);

        $response = $this->actingAs($admin)->patch(route('notificaciones.atender', $alerta));

        $response->assertRedirect();
        $this->assertEquals('atendida', $alerta->fresh()->estado);
    }
}
