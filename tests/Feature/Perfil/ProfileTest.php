<?php

namespace Tests\Feature\Perfil;

use App\Models\Empleado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_their_profile(): void
    {
        $admin = Empleado::factory()->create([
            'nombre' => 'Ana Gimenez',
            'email' => 'ana@example.com',
            'rol' => 'admin',
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)->get(route('perfil'));

        $response->assertStatus(200);
        $response->assertSee('Ana Gimenez');
        $response->assertSee('ana@example.com');
    }
}
