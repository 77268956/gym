<?php

namespace Database\Factories;

use App\Models\LandingConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LandingConfig>
 */
class LandingConfigFactory extends Factory
{
    protected $model = LandingConfig::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hero_title' => fake()->sentence(4),
            'hero_subtitle' => fake()->sentence(8),
            'hero_image' => null,
            'about_text' => fake()->paragraph(),
            'about_image' => null,
            'services' => [
                ['title' => 'Entrenamiento personalizado', 'desc' => 'Rutinas adaptadas a tu nivel.', 'icon' => 'fas fa-user-check'],
                ['title' => 'Clases funcionales', 'desc' => 'Sesiones dinámicas.', 'icon' => 'fas fa-fire'],
                ['title' => 'Acompañamiento constante', 'desc' => 'Seguimiento profesional.', 'icon' => 'fas fa-heartbeat'],
            ],
            'hero_slides' => [
                ['title' => 'Entrena con propósito', 'subtitle' => 'Alcanza tus metas.', 'image' => null],
            ],
            'equipment' => [
                ['title' => 'Zona de fuerza', 'desc' => 'Equipo completo.', 'icon' => 'fas fa-dumbbell', 'image' => null],
            ],
            'trainers' => [
                ['name' => 'Alex Rivera', 'role' => 'Fuerza', 'bio' => 'Entrenador certificado.', 'image' => null],
            ],
            'facilities' => [
                ['title' => 'Zona de entrenamiento', 'desc' => 'Amplio espacio.', 'image' => null],
            ],
            'equipment_heading' => 'Equipos',
            'equipment_intro' => 'Entrena con herramientas modernas.',
            'trainers_heading' => 'Entrenadores',
            'trainers_intro' => 'Profesionales a tu servicio.',
            'facilities_heading' => 'Instalaciones',
            'facilities_intro' => 'Espacio pensado para ti.',
            'contact_phone' => fake()->numerify('7###-####'),
            'contact_email' => fake()->safeEmail(),
            'contact_address' => fake()->streetAddress(),
            'contact_facebook' => 'https://facebook.com/gymx',
            'contact_instagram' => 'https://instagram.com/gymx',
            'contact_whatsapp' => fake()->numerify('5037###-####'),
            'primary_color' => '#2563EB',
            'secondary_color' => '#111214',
        ];
    }
}
