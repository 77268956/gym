<?php

namespace Tests\Feature\Seguridad;

use App\Models\Empleado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_email_redirects_to_dashboard(): void
    {
        Empleado::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_with_username_redirects_to_dashboard(): void
    {
        Empleado::factory()->create([
            'usuario' => 'staff1',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'staff1',
            'password' => 'password123',
        ]);

        $response->assertRedirect('dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_credentials(): void
    {
        Empleado::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('password123'),
            'rol' => 'empleado',
            'estado' => 'activo',
        ]);

        $response = $this->from('/login')->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_already_authenticated_user_redirected_from_login(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/login');

        $response->assertRedirect('dashboard');
    }

    public function test_logout_clears_authentication(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
