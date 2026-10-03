<?php

namespace Database\Seeders;

use App\Models\LandingConfig;
use Illuminate\Database\Seeder;

class LandingConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $config = LandingConfig::query()->first() ?? new LandingConfig;
        $existingHeroImage = $config->hero_image;
        $existingAboutImage = $config->about_image;

        $config->fill([
            'hero_title' => 'Transforma tu vida',
            'hero_subtitle' => 'Entrena con propósito, supera tus límites y alcanza una mejor versión de ti.',
            'about_text' => 'En GymX creamos un espacio para que entrenes con energía, acompañamiento y objetivos claros. Combina equipo de calidad, entrenadores preparados y una comunidad que te impulsa a seguir avanzando.',
            'services' => [
                ['title' => 'Entrenamiento personalizado', 'desc' => 'Rutinas adaptadas a tu nivel, objetivos y ritmo de progreso.', 'icon' => 'fas fa-user-check'],
                ['title' => 'Clases funcionales', 'desc' => 'Sesiones dinámicas para mejorar fuerza, movilidad y resistencia.', 'icon' => 'fas fa-fire'],
                ['title' => 'Acompañamiento constante', 'desc' => 'Consejos y seguimiento para que entrenes con seguridad y constancia.', 'icon' => 'fas fa-heartbeat'],
            ],
            'hero_slides' => [
                ['title' => 'Entrena con propósito', 'subtitle' => 'Cada sesión es una oportunidad para acercarte a tu mejor versión.', 'image' => null],
                ['title' => 'Tu progreso comienza hoy', 'subtitle' => 'Encuentra el equipo, el ambiente y el acompañamiento que necesitas.', 'image' => null],
                ['title' => 'Hazlo parte de tu vida', 'subtitle' => 'Construye hábitos fuertes en una comunidad que te motiva.', 'image' => null],
            ],
            'equipment' => [
                ['title' => 'Zona de fuerza', 'desc' => 'Equipamiento para desarrollar fuerza y trabajar cada grupo muscular con técnica.', 'icon' => 'fas fa-dumbbell', 'image' => null],
                ['title' => 'Área cardiovascular', 'desc' => 'Espacio para mejorar tu resistencia, ritmo y condición física.', 'icon' => 'fas fa-person-running', 'image' => null],
                ['title' => 'Entrenamiento funcional', 'desc' => 'Accesorios y estaciones para movimientos completos y entrenamientos dinámicos.', 'icon' => 'fas fa-bolt', 'image' => null],
            ],
            'trainers' => [
                ['name' => 'Alex Rivera', 'role' => 'Fuerza y acondicionamiento', 'bio' => 'Te ayuda a construir una base sólida con técnica, progresión y objetivos medibles.', 'image' => null],
                ['name' => 'Sofía Martínez', 'role' => 'Entrenamiento funcional', 'bio' => 'Diseña sesiones dinámicas para mejorar tu movilidad, resistencia y confianza.', 'image' => null],
                ['name' => 'Diego Torres', 'role' => 'Acondicionamiento físico', 'bio' => 'Te acompaña a crear hábitos sostenibles y disfrutar cada etapa de tu progreso.', 'image' => null],
            ],
            'facilities' => [
                ['title' => 'Zona de entrenamiento', 'desc' => 'Un espacio amplio y organizado para entrenar con libertad y concentración.', 'image' => null],
                ['title' => 'Área funcional', 'desc' => 'Un ambiente versátil para circuitos, movilidad y sesiones de alto rendimiento.', 'image' => null],
                ['title' => 'Zona de recuperación', 'desc' => 'Espacios pensados para complementar tu entrenamiento y recuperarte mejor.', 'image' => null],
            ],
            'equipment_heading' => 'Equipos para cada objetivo',
            'equipment_intro' => 'Entrena con herramientas modernas y una distribución pensada para que aproveches cada sesión.',
            'trainers_heading' => 'Entrenadores que te acompañan',
            'trainers_intro' => 'Recibe orientación, motivación y seguimiento de profesionales comprometidos con tu progreso.',
            'facilities_heading' => 'Un espacio para superarte',
            'facilities_intro' => 'Conoce ambientes cómodos, funcionales y preparados para que cada visita sea una experiencia completa.',
            'primary_color' => '#2563EB',
            'secondary_color' => '#111214',
        ]);

        $config->hero_image = $existingHeroImage;
        $config->about_image = $existingAboutImage;
        $config->save();
    }
}
