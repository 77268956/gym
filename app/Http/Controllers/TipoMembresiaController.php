<?php

namespace App\Http\Controllers;

use App\Models\TipoMembresia;
use Illuminate\Http\Request;

class TipoMembresiaController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoMembresia::query();

        // Filtro por término de búsqueda (nombre o descripción)
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        // Filtro por estado (activo / inactivo)
        if ($request->filled('estado') && in_array($request->input('estado'), ['activo', 'inactivo'])) {
            $query->where('estado', $request->input('estado'));
        }

        $planes = $query->orderBy('duracion_dias', 'asc')->paginate(12)->withQueryString();

        // Métricas / Estadísticas ejecutivas
        $totalPlanes = TipoMembresia::count();
        $planesActivos = TipoMembresia::where('estado', 'activo')->count();
        $planesInactivos = TipoMembresia::where('estado', 'inactivo')->count();
        $precioPromedio = TipoMembresia::where('estado', 'activo')->avg('precio') ?? 0;

        return view('membresias.index', compact(
            'planes',
            'totalPlanes',
            'planesActivos',
            'planesInactivos',
            'precioPromedio'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'duracion_dias' => 'required|integer|min:1|max:3650',
            'precio' => 'required|numeric|min:0|max:999999.99',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre del plan es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres.',
            'duracion_dias.required' => 'La duración en días es obligatoria.',
            'duracion_dias.integer' => 'La duración debe ser un número entero.',
            'precio.required' => 'El precio del plan es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        TipoMembresia::create($validated);

        return redirect()->route('membresias.index')->with('success', 'Tipo de membresía creado exitosamente.');
    }

    public function update(Request $request, TipoMembresia $tipoMembresia)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'duracion_dias' => 'required|integer|min:1|max:3650',
            'precio' => 'required|numeric|min:0|max:999999.99',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre del plan es obligatorio.',
            'duracion_dias.required' => 'La duración en días es obligatoria.',
            'precio.required' => 'El precio del plan es obligatorio.',
        ]);

        $tipoMembresia->update($validated);

        return redirect()->route('membresias.index')->with('success', 'Tipo de membresía actualizado exitosamente.');
    }

    public function destroy(TipoMembresia $tipoMembresia)
    {
        $tipoMembresia->delete();

        return redirect()->route('membresias.index')->with('success', 'Tipo de membresía eliminado correctamente.');
    }

    public function toggleStatus(TipoMembresia $tipoMembresia)
    {
        $nuevoEstado = $tipoMembresia->estado === 'activo' ? 'inactivo' : 'activo';
        $tipoMembresia->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === 'activo' ? 'Plan activado exitosamente.' : 'Plan desactivado exitosamente.';

        return redirect()->route('membresias.index')->with('success', $mensaje);
    }
}
