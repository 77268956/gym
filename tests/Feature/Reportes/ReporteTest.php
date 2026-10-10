<?php

namespace Tests\Feature\Reportes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_reportes_index(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('reportes.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_reportes_pdf_view(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('reportes.pdf'));

        $response->assertStatus(200);
    }

    public function test_reportes_validation_hasta_after_desde(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('reportes.index', [
            'desde' => '2026-10-10',
            'hasta' => '2026-10-09',
        ]));

        $response->assertSessionHasErrors('hasta');
    }
}
