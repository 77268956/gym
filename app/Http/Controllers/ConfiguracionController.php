<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionGeneral;
use App\Models\ConfiguracionPunto;
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

        $configuracionPuntos = ConfiguracionPunto::first();

        return view('configuracion.index', compact('configuracion', 'configuracionPuntos'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_gimnasio' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'color_primario' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_primario_hover' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_sidebar' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_sidebar_hover' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ], [
            'nombre_gimnasio.required' => 'El nombre del gimnasio es obligatorio.',
            'logo.image' => 'El archivo del logo debe ser una imagen válida.',
            'logo.max' => 'El tamaño máximo del logo es 2 MB.',
            '*.regex' => 'Los colores deben tener un formato hexadecimal válido.',
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
        $configuracion->color_primario = $request->input('color_primario', '#2563EB');
        $configuracion->color_primario_hover = $request->input('color_primario_hover', '#1D4ED8');
        $configuracion->color_sidebar = $request->input('color_sidebar', '#1E293B');
        $configuracion->color_sidebar_hover = $request->input('color_sidebar_hover', '#334155');

        $configuracion->save();

        // Puntos por visita
        ConfiguracionPunto::updateOrCreate([], [
            'puntos_por_visita' => (int) $request->input('puntos_por_visita', 10),
            'vigente_desde' => now()->toDateString(),
        ]);

        // Limpiar caché global
        Cache::forget('configuracion_general');

        return redirect()->route('configuracion.index')->with('success', 'Configuración del gimnasio actualizada con éxito.');
    }
}
