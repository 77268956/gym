<?php

namespace Tests\Feature\Pagos;

use App\Models\Pago;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagoTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_membership_payment(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();
        $plan = $this->createPlan(['precio' => 45.00]);

        $response = $this->actingAs($staff)->post(route('pagos.store'), [
            'cliente_id' => $client->id,
            'tipo_pago' => 'membresia',
            'tipo_membresia_id' => $plan->id,
            'metodo_pago' => 'efectivo',
            'monto' => '45.00',
            'concepto' => 'Membresía mensual',
            'fecha_inicio' => now()->toDateString(),
            'fecha_pago' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect(route('pagos.ticket', Pago::first()));
        $this->assertDatabaseCount('membresias', 1);
        $this->assertDatabaseCount('pagos', 1);
    }

    public function test_can_store_daily_pass_payment(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();

        $response = $this->actingAs($staff)->post(route('pagos.store'), [
            'cliente_id' => $client->id,
            'tipo_pago' => 'pase_diario',
            'metodo_pago' => 'efectivo',
            'monto' => '10.00',
            'concepto' => 'Pase del día',
            'fecha_pago' => now()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('pases_diarios', 1);
        $this->assertDatabaseCount('pagos', 1);
    }

    public function test_rejects_wrong_amount_for_membership(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();
        $plan = $this->createPlan(['precio' => 45.00]);

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
                'fecha_pago' => now()->format('Y-m-d\TH:i'),
            ]);

        $response->assertRedirect(route('pagos.create'));
        $response->assertSessionHasErrors('monto');
        $this->assertDatabaseCount('membresias', 0);
    }

    public function test_can_access_pagos_index(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get(route('pagos.index'));

        $response->assertStatus(200);
    }

    public function test_cliente_info_and_buscar_clientes(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();

        $info = $this->actingAs($staff)->get(route('pagos.clienteInfo', $client));

        $info->assertStatus(200);
        $info->assertJsonStructure(['estado_cliente', 'membresia', 'fecha_inicio_sugerida']);

        $buscar = $this->actingAs($staff)->get(route('pagos.buscarClientes', ['q' => $client->nombre]));

        $buscar->assertStatus(200);
        $buscar->assertJsonStructure(['results']);
    }
}
