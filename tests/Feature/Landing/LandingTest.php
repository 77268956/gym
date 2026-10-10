<?php

namespace Tests\Feature\Landing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_home(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_landing_edit(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.landing'));

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_landing_edit(): void
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get(route('admin.landing'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_update_landing_with_valid_data(): void
    {
        $admin = $this->createAdmin();
        Storage::fake('public');

        $response = $this->actingAs($admin)->post(route('admin.landing.update'), [
            'hero_title' => 'GymX Pro',
            'hero_subtitle' => 'Tu mejor versiÃ³n',
            'about_text' => 'DescripciÃ³n de la academia',
            'services_title' => ['Servicio 1'],
            'services_desc' => ['DescripciÃ³n'],
            'services_icon' => ['fas fa-star'],
            'hero_slides_title' => ['Slide 1'],
            'hero_slides_subtitle' => ['Sub 1'],
            'hero_slides_image' => [null],
            'equipment_title' => ['Equipo 1'],
            'equipment_desc' => ['Desc'],
            'equipment_icon' => ['fas fa-dumbbell'],
            'trainers_name' => ['Trainer 1'],
            'trainers_role' => ['Role'],
            'trainers_bio' => ['Bio'],
            'facilities_title' => ['Facility 1'],
            'facilities_desc' => ['Desc'],
            'equipment_heading' => 'Equipos',
            'equipment_intro' => 'Intro',
            'trainers_heading' => 'Entrenadores',
            'trainers_intro' => 'Intro',
            'facilities_heading' => 'Instalaciones',
            'facilities_intro' => 'Intro',
            'primary_color' => '#2563EB',
            'secondary_color' => '#111214',
            'contact_phone' => '12345678',
            'contact_email' => 'contact@gymx.test',
            'contact_address' => 'DirecciÃ³n',
            'contact_facebook' => 'https://facebook.com/gymx',
            'contact_instagram' => 'https://instagram.com/gymx',
            'contact_whatsapp' => '12345678',
        ]);

        $response->assertRedirect(route('admin.landing'));
        $this->assertDatabaseHas('landing_configs', [
            'hero_title' => 'GymX Pro',
            'primary_color' => '#2563EB',
            'secondary_color' => '#111214',
        ]);
    }

    public function test_landing_update_requires_valid_hex_colors(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->from(route('admin.landing'))
            ->post(route('admin.landing.update'), [
                'hero_title' => 'Test',
                'hero_subtitle' => 'Test',
                'about_text' => 'Test',
                'services_title' => [],
                'hero_slides_title' => [],
                'equipment_title' => [],
                'trainers_name' => [],
                'facilities_title' => [],
                'primary_color' => 'red',
                'secondary_color' => 'blue',
            ]);

        $response->assertRedirect(route('admin.landing'));
        $response->assertSessionHasErrors(['primary_color', 'secondary_color']);
    }

    public function test_landing_update_validates_image_files(): void
    {
        $admin = $this->createAdmin();
        Storage::fake('public');
        $invalid = UploadedFile::fake()->create('file.txt', 10, 'text/plain');

        $response = $this->actingAs($admin)
            ->from(route('admin.landing'))
            ->post(route('admin.landing.update'), [
                'hero_title' => 'Test',
                'hero_subtitle' => 'Test',
                'about_text' => 'Test',
                'services_title' => [],
                'hero_slides_title' => [],
                'equipment_title' => [],
                'trainers_name' => [],
                'facilities_title' => [],
                'primary_color' => '#2563EB',
                'secondary_color' => '#111214',
                'hero_image' => $invalid,
            ]);

        $response->assertRedirect(route('admin.landing'));
        $response->assertSessionHasErrors('hero_image');
    }
}
