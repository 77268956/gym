<?php

namespace Tests\Feature\Dashboard;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_user_panel(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get(route('user'));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_perfil(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get(route('perfil'));

        $response->assertStatus(200);
    }
}
