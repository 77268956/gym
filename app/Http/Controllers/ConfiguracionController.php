<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionGeneral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $configuracion = ConfiguracionGeneral::firstOrCreate([], [
            'nombre_gimnasio' => 'EcoGim',
            'logo_path' => null,
        ]);

        return view('configuracion.index', compact('configuracion'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_gimnasio' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'nombre_gimnasio.required' => 'El nombre del gimnasio es obligatorio.',
            'logo.image' => 'El archivo del logo debe ser una imagen válida.',
            'logo.max' => 'El tamaño máximo del logo es 2 MB.',
        ]);

        $configuracion = ConfiguracionGeneral::firstOrCreate([], [
            'nombre_gimnasio' => 'EcoGim',
            'logo_path' => null,
        ]);

        $configuracion->nombre_gimnasio = $request->input('nombre_gimnasio');

        // Eliminar logo si el usuario lo solicitó
        if ($request->boolean('eliminar_logo')) {
            if ($configuracion->logo_path && Storage::disk('public')->exists($configuracion->logo_path)) {
                Storage::disk('public')->delete($configuracion->logo_path);
            }
            $configuracion->logo_path = null;
        }

        // Subir nuevo logo si se adjuntó
        if ($request->hasFile('logo')) {
            if ($configuracion->logo_path && Storage::disk('public')->exists($configuracion->logo_path)) {
                Storage::disk('public')->delete($configuracion->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $configuracion->logo_path = $path;
        }

        // Moneda
        $configuracion->moneda = $request->input('moneda', 'Lempira');
        $configuracion->simbolo_moneda = $request->input('simbolo_moneda', 'L.');
        $configuracion->codigo_moneda = $request->input('codigo_moneda', 'HNL');

        $configuracion->save();

        // Limpiar caché global
        Cache::forget('configuracion_general');

        return redirect()->route('configuracion.index')->with('success', 'Configuración del gimnasio actualizada con éxito.');
    }
}
