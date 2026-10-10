<?php

namespace Tests\Feature\Productos;

use App\Models\ProductoEcogim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductoEcogimTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_productos_index(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.productos.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = $this->createAdmin();
        Storage::fake('public');

        $response = $this->actingAs($admin)->post(route('admin.productos.store'), [
            'nombre' => 'Proteína',
            'descripcion' => 'Proteína en polvo',
            'categoria' => 'suplemento',
            'puntos_valor' => 150,
            'stock' => 10,
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertDatabaseHas('productos_ecogim', [
            'nombre' => 'Proteína',
            'puntos_valor' => 150,
            'stock' => 10,
        ]);
    }

    public function test_admin_can_create_product_with_image(): void
    {
        $admin = $this->createAdmin();
        Storage::fake('public');
        $file = UploadedFile::fake()->create('producto.png', 100, 'image/png');

        $response = $this->actingAs($admin)->post(route('admin.productos.store'), [
            'nombre' => 'Barra',
            'descripcion' => 'Barra energética',
            'categoria' => 'comida',
            'puntos_valor' => 50,
            'stock' => 20,
            'estado' => 'activo',
            'imagen' => $file,
        ]);

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertDatabaseCount('productos_ecogim', 1);
        Storage::disk('public')->assertExists('productos_ecogim/'.$file->hashName());
    }

    public function test_admin_can_update_product(): void
    {
        $admin = $this->createAdmin();
        $producto = ProductoEcogim::factory()->create([
            'nombre' => 'Viejo',
            'puntos_valor' => 100,
            'stock' => 5,
            'estado' => 'activo',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.productos.update', $producto), [
            'nombre' => 'Nuevo',
            'descripcion' => 'Descripción',
            'categoria' => 'bebida',
            'puntos_valor' => 200,
            'stock' => 15,
            'estado' => 'activo',
        ]);

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertDatabaseHas('productos_ecogim', [
            'id' => $producto->id,
            'nombre' => 'Nuevo',
            'puntos_valor' => 200,
            'stock' => 15,
        ]);
    }

    public function test_admin_can_toggle_product_status(): void
    {
        $admin = $this->createAdmin();
        $producto = ProductoEcogim::factory()->create(['estado' => 'activo']);

        $response = $this->actingAs($admin)->patch(route('admin.productos.toggleStatus', $producto));

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertEquals('inactivo', $producto->fresh()->estado);
    }

    public function test_admin_can_soft_delete_product(): void
    {
        $admin = $this->createAdmin();
        $producto = ProductoEcogim::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.productos.destroy', $producto));

        $response->assertRedirect(route('admin.productos.index'));
        $this->assertSoftDeleted('productos_ecogim', ['id' => $producto->id]);
    }
}
