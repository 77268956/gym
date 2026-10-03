<?php

namespace App\Http\Controllers;

use App\Models\LandingConfig;
use App\Models\ProductoEcogim;
use App\Models\TipoMembresia;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    // Vista Pública
    public function index()
    {
        $config = LandingConfig::first() ?? new LandingConfig;

        // Fetch active plans ordered by price
        $planes = TipoMembresia::where('estado', 'activo')->orderBy('precio')->get();

        // Fetch active rewards
        $recompensas = ProductoEcogim::where('estado', 'activo')->where('stock', '>', 0)->take(4)->get();

        return view('landing.index', compact('config', 'planes', 'recompensas'));
    }

    // Panel Admin
    public function edit()
    {
        $config = LandingConfig::first() ?? new LandingConfig;

        return view('landing.admin', compact('config'));
    }

    public function update(Request $request)
    {
        $config = LandingConfig::first() ?? new LandingConfig;

        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'about_text' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:255',
            'contact_facebook' => 'nullable|url',
            'contact_instagram' => 'nullable|url',
            'contact_whatsapp' => 'nullable|string|max:50',
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'equipment_heading' => 'nullable|string|max:100',
            'equipment_intro' => 'nullable|string|max:1000',
            'trainers_heading' => 'nullable|string|max:100',
            'trainers_intro' => 'nullable|string|max:1000',
            'facilities_heading' => 'nullable|string|max:100',
            'facilities_intro' => 'nullable|string|max:1000',
            'services_title' => 'nullable|array',
            'services_title.*' => 'nullable|string|max:100',
            'services_desc' => 'nullable|array',
            'services_desc.*' => 'nullable|string|max:500',
            'services_icon' => 'nullable|array',
            'services_icon.*' => 'nullable|string|max:100',
            'hero_slides_title' => 'nullable|array',
            'hero_slides_title.*' => 'nullable|string|max:255',
            'hero_slides_subtitle' => 'nullable|array',
            'hero_slides_subtitle.*' => 'nullable|string|max:255',
            'hero_slides_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'equipment_title' => 'nullable|array',
            'equipment_title.*' => 'nullable|string|max:100',
            'equipment_desc' => 'nullable|array',
            'equipment_desc.*' => 'nullable|string|max:500',
            'equipment_icon' => 'nullable|array',
            'equipment_icon.*' => 'nullable|string|max:100',
            'equipment_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'trainers_name' => 'nullable|array',
            'trainers_name.*' => 'nullable|string|max:100',
            'trainers_role' => 'nullable|array',
            'trainers_role.*' => 'nullable|string|max:100',
            'trainers_bio' => 'nullable|array',
            'trainers_bio.*' => 'nullable|string|max:500',
            'trainers_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'facilities_title' => 'nullable|array',
            'facilities_title.*' => 'nullable|string|max:100',
            'facilities_desc' => 'nullable|array',
            'facilities_desc.*' => 'nullable|string|max:500',
            'facilities_image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request->file('hero_image')->store('landing', 'public');
        }
        if ($request->hasFile('about_image')) {
            $data['about_image'] = $request->file('about_image')->store('landing', 'public');
        }

        $data['services'] = $this->buildItems($request, 'services', [
            'desc' => 'services_desc',
            'icon' => 'services_icon',
        ]);
        $data['equipment'] = $this->buildItems($request, 'equipment', [
            'desc' => 'equipment_desc',
            'icon' => 'equipment_icon',
            'image' => 'equipment_existing_image',
        ]);
        $data['trainers'] = $this->buildItems($request, 'trainers', [
            'role' => 'trainers_role',
            'bio' => 'trainers_bio',
            'image' => 'trainers_existing_image',
        ], 'trainers_name', 'name');
        $data['facilities'] = $this->buildItems($request, 'facilities', [
            'desc' => 'facilities_desc',
            'image' => 'facilities_existing_image',
        ]);
        $data['equipment'] = $this->storeItemImages($request, $data['equipment'], 'equipment_image');
        $data['trainers'] = $this->storeItemImages($request, $data['trainers'], 'trainers_image');
        $data['facilities'] = $this->storeItemImages($request, $data['facilities'], 'facilities_image');

        $slides = [];
        foreach ($request->input('hero_slides_title', []) as $index => $title) {
            if (! empty($title)) {
                $slides[] = [
                    'title' => $title,
                    'subtitle' => $request->input("hero_slides_subtitle.$index", ''),
                    'image' => $request->file("hero_slides_image.$index")?->store('landing', 'public')
                        ?? $request->input("hero_slides_existing_image.$index"),
                ];
            }
        }
        $data['hero_slides'] = $slides;
        $data['hero_title'] = $slides[0]['title'] ?? $config->hero_title ?? 'Bienvenido a nuestro Gimnasio';
        $data['hero_subtitle'] = $slides[0]['subtitle'] ?? $config->hero_subtitle ?? 'Alcanza tus metas con nosotros';

        $config->fill($data);
        $config->save();

        return redirect()->route('admin.landing')->with('success', 'Landing page actualizada');
    }

    private function buildItems(
        Request $request,
        string $prefix,
        array $fields,
        ?string $titleField = null,
        string $titleKey = 'title'
    ): array {
        $titleField ??= $prefix.'_title';
        $items = [];

        foreach ($request->input($titleField, []) as $index => $title) {
            if (! empty($title)) {
                $item = [$titleKey => $title];
                foreach ($fields as $key => $field) {
                    $item[$key] = $request->input("$field.$index", '');
                }
                $items[] = $item;
            }
        }

        return $items;
    }

    private function storeItemImages(Request $request, array $items, string $field): array
    {
        foreach ($items as $index => $item) {
            $file = $request->file("$field.$index");
            if ($file) {
                $items[$index]['image'] = $file->store('landing', 'public');
            } else {
                $items[$index]['image'] = $item['image'] ?? '';
            }
        }

        return $items;
    }
}
