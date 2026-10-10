<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_dashboard_with_periodo_rango_validates_hasta(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get(route('dashboard', [
            'periodo' => 'rango',
            'desde' => '2026-10-10',
            'hasta' => '2026-10-09',
        ]));

        $response->assertSessionHasErrors('hasta');
    }
}
