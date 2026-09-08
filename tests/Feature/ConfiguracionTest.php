<?php

namespace Tests\Feature;

use App\Models\ConfiguracionGeneral;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConfiguracionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_configuracion_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('configuracion.index'));

        $response->assertStatus(200);
        $response->assertSee('Configuración del Gimnasio');
    }

    public function test_can_update_gym_name_and_logo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $logoFile = UploadedFile::fake()->create('custom_logo.png', 100, 'image/png');

        $response = $this->actingAs($user)->post(route('configuracion.update'), [
            'nombre_gimnasio' => 'Titan Gym Pro',
            'logo' => $logoFile,
        ]);

        $response->assertRedirect(route('configuracion.index'));
        $response->assertSessionHas('success');

        $config = ConfiguracionGeneral::first();
        $this->assertEquals('Titan Gym Pro', $config->nombre_gimnasio);
        $this->assertNotNull($config->logo_path);
        Storage::disk('public')->assertExists($config->logo_path);
    }

    public function test_gym_name_is_reflected_in_login_page(): void
    {
        ConfiguracionGeneral::create([
            'nombre_gimnasio' => 'PowerGym Elite',
            'logo_path' => null,
        ]);

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('PowerGym Elite');
    }
}
