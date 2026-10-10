<?php

namespace Tests\Feature\Membresias;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembresiaClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_access_membresias_clientes_index(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get(route('membresias-clientes.index'));

        $response->assertStatus(200);
    }

    public function test_can_filter_by_estado(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();
        $plan = $this->createPlan();

        $this->createMembershipForCliente($client, $plan, 30, 0);

        $response = $this->actingAs($staff)->get(route('membresias-clientes.index', ['estado' => 'activas']));

        $response->assertStatus(200);
    }

    public function test_returns_fragment_when_requested(): void
    {
        $staff = $this->createStaff();
        $client = $this->createClient();
        $plan = $this->createPlan();

        $this->createMembershipForCliente($client, $plan, 30, 0);

        $response = $this->actingAs($staff)
            ->withHeaders(['X-Fragment-Name' => 'membership-results'])
            ->get(route('membresias-clientes.index'));

        $response->assertStatus(200);
    }
}
