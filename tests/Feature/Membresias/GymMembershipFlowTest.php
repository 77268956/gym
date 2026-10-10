<?php

namespace Tests\Feature;

use App\Models\AsistenciaCliente;
use App\Models\Cliente;
use App\Models\ConfiguracionPunto;
use App\Models\Empleado;
use App\Models\MovimientoPunto;
use App\Models\PaseDiario;
use App\Models\TipoMembresia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GymMembershipFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_register_a_client_with_a_paid_membership(): void
    {
        $this->travelTo('2026-10-06 10:00:00');
        $staff = $this->createStaff();
        $plan = $this->createPlan();

        $response = $this->actingAs($staff)->post(route('clientes.store'), [
            'nombre' => 'Ana',
            'apellido' => 'García',
            'cedula' => '12345678-9',
            'telefono' => '7012-3456',
            'email' => 'ana@example.test',
            'fecha_nacimiento' => '1995-05-10',
            'direccion' => 'San Salvador',
            'tipo_membresia_id' => $plan->id,
            'fecha_inicio' => '2026-10-06',
            'metodo_pago' => 'tarjeta',
        ]);

        $response->assertRedirect(route('user'));
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Ana',
            'apellido' => 'García',
            'email' => 'ana@example.test',
            'direccion' => 'San Salvador',
        ]);
        $this->assertDatabaseHas('membresias', [
            'cliente_id' => 1,
            'fecha_inicio' => '2026-10-06 00:00:00',
            'fecha_vencimiento' => '2026-11-04 23:59:59',
            'estado' => 'activa',
        ]);
        $this->assertDatabaseHas('pagos', [
            'cliente_id' => 1,
            'monto' => '45.00',
            'metodo_pago' => 'tarjeta',
            'concepto' => 'Membresía: Mensual',
            'estado' => 'pagado',
        ]);
    }

    public function test_membership_payment_rejects_a_client_supplied_price(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();
        $plan = $this->createPlan();

        $response = $this->actingAs($staff)
            ->from(route('pagos.create'))
            ->post(route('pagos.store'), [
                'cliente_id' => $client->id,
                'tipo_pago' => 'membresia',
                'tipo_membresia_id' => $plan->id,
                'metodo_pago' => 'efectivo',
                'monto' => '1.00',
                'concepto' => 'Membresía mensual',
                'fecha_inicio' => now()->toDateString(),
                'fecha_pago' => now()->format('Y-m-d\\TH:i'),
            ]);

        $response->assertRedirect(route('pagos.create'));
        $response->assertSessionHasErrors('monto');
        $this->assertDatabaseCount('membresias', 0);
        $this->assertDatabaseCount('pagos', 0);
    }

    public function test_paid_daily_pass_grants_one_attendance_and_one_point(): void
    {
        $this->travelTo('2026-10-06 10:00:00');
        $staff = $this->createStaff();
        $client = $this->createClient();
        ConfiguracionPunto::create(['puntos_por_visita' => 1, 'vigente_desde' => today()]);

        $paymentResponse = $this->actingAs($staff)->post(route('pagos.store'), [
            'cliente_id' => $client->id,
            'tipo_pago' => 'pase_diario',
            'metodo_pago' => 'efectivo',
            'monto' => '10.00',
            'concepto' => 'Pase del día',
            'fecha_pago' => now()->format('Y-m-d\\TH:i'),
        ]);
        $paymentResponse->assertRedirect();

        $firstAttendanceResponse = $this->actingAs($staff)->postJson(route('asistencias.registrar'), ['cliente_id' => $client->id])
            ->assertOk()
            ->assertJsonPath('status', 'success');
        $firstAttendanceResponse->assertJsonPath('message', '¡Bienvenido! Asistencia registrada exitosamente.');
        $this->assertSame(1, AsistenciaCliente::where('cliente_id', $client->id)->where('exitoso', true)->count(), 'The first scan should create one attendance.');

        $secondAttendanceResponse = $this->actingAs($staff)->postJson(route('asistencias.registrar'), ['cliente_id' => $client->id])
            ->assertOk()
            ->assertJsonPath('status', 'success');
        $secondAttendanceResponse->assertJsonPath('message', '¡Bienvenido! Tu asistencia ya estaba registrada hoy.');

        $this->assertSame(1, AsistenciaCliente::where('cliente_id', $client->id)->where('exitoso', true)->count(), 'The client must receive one successful attendance per day.');
        $this->assertSame(1, MovimientoPunto::where('cliente_id', $client->id)->where('tipo_movimiento', 'ganado')->count(), 'A point movement must be recorded once.');
        $this->assertSame(1, (int) $client->fresh()->puntos_ecogim, 'One attendance must add one point.');
        $this->assertSame(1, PaseDiario::where('cliente_id', $client->id)->count(), 'The payment must create one valid daily pass.');
    }

    public function test_member_attendance_scanner_requires_authentication(): void
    {
        $this->get(route('asistencias.publico'))->assertRedirect(route('login'));
        $this->postJson(route('asistencias.publico.registrar'), ['cliente_id' => 1])->assertUnauthorized();
    }

    public function test_staff_can_log_in_with_email_and_username(): void
    {
        $staff = $this->createStaff();

        $this->post(route('login'), [
            'email' => '  STAFF@EXAMPLE.TEST  ',
            'password' => 'password123',
        ])->assertRedirect('dashboard');
        $this->assertAuthenticatedAs($staff);

        $this->post(route('logout'));
        $this->post(route('login'), [
            'email' => 'staff',
            'password' => 'password123',
        ])->assertRedirect('dashboard');
        $this->assertAuthenticatedAs($staff);
    }

    protected function createStaff(array $overrides = []): Empleado
    {
        return Empleado::create(array_merge([
            'nombre' => 'Personal de prueba',
            'cedula' => 'TEST-STAFF-1',
            'usuario' => 'staff',
            'email' => 'staff@example.test',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ], $overrides));
    }

    protected function createClient(array $overrides = []): Cliente
    {
        return Cliente::create(array_merge([
            'nombre' => 'Luis',
            'apellido' => 'Pérez',
            'cedula' => '23456789-0',
            'telefono' => '7654-3210',
            'email' => 'luis@example.test',
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'San Salvador',
            'estado' => 'activo',
            'puntos_ecogim' => 0,
        ], $overrides));
    }

    protected function createPlan(array $overrides = []): TipoMembresia
    {
        return TipoMembresia::create(array_merge([
            'nombre' => 'Mensual',
            'duracion_dias' => 30,
            'precio' => 45,
            'estado' => 'activo',
        ], $overrides));
    }
}
