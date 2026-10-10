<?php

namespace Tests\Feature\Asistencias;

use App\Models\Empleado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsistenciaEmpleadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_empleados_asistencias(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('asistencias_empleados.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_empleados_escanear(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('asistencias_empleados.escanear'));

        $response->assertStatus(200);
    }

    public function test_record_employee_entry_and_exit(): void
    {
        $this->travelTo('2026-10-09 08:00:00');
        $admin = $this->createAdmin();
        $empleado = Empleado::factory()->create([
            'rol' => 'empleado',
            'estado' => 'activo',
            'hora_entrada_turno' => '08:00:00',
            'hora_salida_turno' => '17:00:00',
            'tolerancia_minutos' => 10,
        ]);

        $entry = $this->actingAs($admin)->postJson(route('asistencias_empleados.registrar'), [
            'empleado_id' => $empleado->id,
        ]);
        $entry->assertOk()->assertJsonPath('status', 'success');

        $this->travelTo('2026-10-09 17:00:00');
        $exit = $this->actingAs($admin)->postJson(route('asistencias_empleados.registrar'), [
            'empleado_id' => $empleado->id,
        ]);
        $exit->assertOk()->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('asistencias_empleados', [
            'empleado_id' => $empleado->id,
            'hora_salida' => '17:00:00',
        ]);
    }

    public function test_public_employee_scanner_requires_auth(): void
    {
        $this->get(route('asistencias_empleados.publico'))->assertRedirect(route('login'));
    }
}
