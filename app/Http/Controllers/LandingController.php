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
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:255',
            'about_text' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:255',
            'contact_facebook' => 'nullable|url',
            'contact_instagram' => 'nullable|url',
            'contact_whatsapp' => 'nullable|string|max:50',
            'primary_color' => 'required|string|max:20',
            'services_title' => 'nullable|array',
            'services_title.*' => 'nullable|string|max:100',
            'services_desc' => 'nullable|array',
            'services_desc.*' => 'nullable|string|max:500',
            'services_icon' => 'nullable|array',
            'services_icon.*' => 'nullable|string|max:100',
        ]);

        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $request->file('hero_image')->store('landing', 'public');
        }
        if ($request->hasFile('about_image')) {
            $data['about_image'] = $request->file('about_image')->store('landing', 'public');
        }

        // Servicios JSON
        $services = [];
        if ($request->has('services_title')) {
            foreach ($request->services_title as $index => $title) {
                if (! empty($title)) {
                    $services[] = [
                        'title' => $title,
                        'desc' => $request->services_desc[$index] ?? '',
                        'icon' => $request->services_icon[$index] ?? 'fas fa-check',
                    ];
                }
            }
        }
        $data['services'] = $services;

        $config->fill($data);
        $config->save();

        return redirect()->route('admin.landing')->with('success', 'Landing page actualizada');
    }
}
