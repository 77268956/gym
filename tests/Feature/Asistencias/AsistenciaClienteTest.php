<?php

namespace Tests\Feature\Asistencias;

use App\Models\AsistenciaCliente;
use App\Models\ConfiguracionPunto;
use App\Models\MovimientoPunto;
use App\Models\Pago;
use App\Models\PaseDiario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsistenciaClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_scanner_requires_authentication(): void
    {
        $this->get(route('asistencias.publico'))->assertRedirect(route('login'));
        $this->postJson(route('asistencias.publico.registrar'), ['cliente_id' => 1])->assertUnauthorized();
    }

    public function test_successful_attendance_grants_point_once(): void
    {
        $this->travelTo('2026-10-09 12:00:00');
        $staff = $this->createStaff();
        $client = $this->createClient();
        ConfiguracionPunto::factory()->create(['puntos_por_visita' => 1, 'vigente_desde' => '2026-10-09']);

        $pago = Pago::factory()->create([
            'cliente_id' => $client->id,
            'empleado_id' => $staff->id,
            'tipo_pago' => 'pase_diario',
            'monto' => 10.00,
            'concepto' => 'Pase del día',
            'estado' => 'pagado',
            'fecha_pago' => '2026-10-09 12:00:00',
        ]);

        PaseDiario::factory()->create([
            'cliente_id' => $client->id,
            'pago_id' => $pago->id,
            'fecha' => '2026-10-09',
            'otorga_asistencia' => true,
        ]);

        $first = $this->actingAs($staff)->postJson(route('asistencias.registrar'), ['cliente_id' => $client->id]);
        $first->assertOk()->assertJsonPath('status', 'success');

        $second = $this->actingAs($staff)->postJson(route('asistencias.registrar'), ['cliente_id' => $client->id]);
        $second->assertOk()->assertJsonPath('status', 'success');

        $this->assertSame(1, AsistenciaCliente::where('cliente_id', $client->id)->where('exitoso', true)->count());
        $this->assertSame(1, MovimientoPunto::where('cliente_id', $client->id)->where('tipo_movimiento', 'ganado')->count());
        $this->assertSame(1, (int) $client->fresh()->puntos_ecogim);
    }

    public function test_inactive_client_rejected(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient(['estado' => 'inactivo']);

        $response = $this->actingAs($staff)->postJson(route('asistencias.registrar'), ['cliente_id' => $client->id]);

        $response->assertOk()->assertJsonPath('status', 'warning');
        $this->assertDatabaseHas('asistencias_clientes', [
            'cliente_id' => $client->id,
            'exitoso' => false,
        ]);
    }

    public function test_no_membership_rejected(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();

        $response = $this->actingAs($staff)->postJson(route('asistencias.registrar'), ['cliente_id' => $client->id]);

        $response->assertOk()->assertJsonPath('status', 'warning');
        $this->assertDatabaseHas('asistencias_clientes', [
            'cliente_id' => $client->id,
            'exitoso' => false,
        ]);
    }
}
