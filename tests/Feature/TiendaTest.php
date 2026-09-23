<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\ProductoEcogim;
use App\Models\TipoMembresia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TiendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_puede_canjear_producto(): void
    {
        $user = User::factory()->create();

        $cliente = Cliente::factory()->create([
            'nombre' => 'Juan Perez',
            'cedula' => '001-0000000-1',
            'estado' => 'activo',
            'puntos_ecogim' => 500,
        ]);

        $tipoMembresia = TipoMembresia::factory()->create([
            'nombre' => 'Plan Mensual',
            'duracion_dias' => 30,
            'precio' => 1000,
        ]);

        Membresia::factory()->create([
            'cliente_id' => $cliente->id,
            'tipo_membresia_id' => $tipoMembresia->id,
            'estado' => 'activa',
            'fecha_inicio' => now(),
            'fecha_vencimiento' => now()->addDays(30),
        ]);

        $producto = ProductoEcogim::factory()->create([
            'nombre' => 'Proteina Whey',
            'estado' => 'activo',
            'stock' => 10,
            'puntos_valor' => 100,
        ]);

        $response = $this->actingAs($user)->postJson(route('tienda.canjear'), [
            'cliente_id' => $cliente->id,
            'producto_id' => $producto->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'puntos_restantes' => 400,
            ]);

        $this->assertDatabaseHas('canjes_ecogim', [
            'cliente_id' => $cliente->id,
            'producto_id' => $producto->id,
            'puntos_utilizados' => 100,
        ]);

        $this->assertDatabaseHas('movimientos_puntos', [
            'cliente_id' => $cliente->id,
            'tipo_movimiento' => 'canjeado',
            'puntos' => 100,
        ]);

        $this->assertEquals(400, $cliente->fresh()->puntos_ecogim);
        $this->assertEquals(9, $producto->fresh()->stock);
    }
}
