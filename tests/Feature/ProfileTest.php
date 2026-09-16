<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_authenticated_user_can_view_their_profile(): void
    {
        $user = new User([
            'name' => 'Ana Gimenez',
            'email' => 'ana@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('perfil'));

        $response->assertStatus(200);
        $response->assertSee('Ana Gimenez');
        $response->assertSee('ana@example.com');
    }
}
