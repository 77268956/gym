<?php

namespace App\Http\Controllers;

use App\Models\AlertaSistema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'tipo' => 'nullable|in:intentos_escaner,entrada_tardia,salida_temprana,salida_no_registrada,exceso_salidas_no_registradas,ausencia,membresia_por_vencer,reconocimiento_fallido',
            'estado' => 'nullable|in:pendiente,atendida',
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
            'busqueda' => 'nullable|string|max:255',
        ]);

        $query = AlertaSistema::query()->latest();

        if (! empty($filtros['tipo'])) {
            $query->where('tipo_alerta', $filtros['tipo']);
        }

        if (! empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (! empty($filtros['desde'])) {
            $query->whereDate('created_at', '>=', $filtros['desde']);
        }

        if (! empty($filtros['hasta'])) {
            $query->whereDate('created_at', '<=', $filtros['hasta']);
        }

        if (! empty($filtros['busqueda'])) {
            $query->where('mensaje', 'like', '%'.$filtros['busqueda'].'%');
        }

        $alertas = $query->paginate(15)->withQueryString();
        $conteos = [
            'total' => AlertaSistema::count(),
            'pendientes' => AlertaSistema::where('estado', 'pendiente')->count(),
            'atendidas' => AlertaSistema::where('estado', 'atendida')->count(),
        ];
        $tiposAlerta = [
            'intentos_escaner' => 'Intentos de escaneo',
            'entrada_tardia' => 'Entrada tarde',
            'salida_temprana' => 'Salida temprana',
            'salida_no_registrada' => 'Salida no registrada',
            'exceso_salidas_no_registradas' => 'Salidas no registradas frecuentes',
            'ausencia' => 'Ausencia',
            'membresia_por_vencer' => 'Membresía por vencer',
            'reconocimiento_fallido' => 'Reconocimiento fallido',
        ];

        return view('notificaciones.index', compact('alertas', 'conteos', 'tiposAlerta'));
    }

    public function atender(AlertaSistema $alerta): RedirectResponse
    {
        $alerta->update(['estado' => 'atendida']);

        return back();
    }
}
