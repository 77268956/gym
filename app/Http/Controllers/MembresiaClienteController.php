<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\TipoMembresia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembresiaClienteController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::now();

        // KPIs (clientes únicos)
        $totalMembresias = Membresia::distinct('cliente_id')->count('cliente_id');
        $activas = Membresia::where('estado', 'activa')
            ->where('fecha_inicio', '<=', $today)
            ->where('fecha_vencimiento', '>=', $today)
            ->distinct('cliente_id')
            ->count('cliente_id');
        $recienCompradas = Membresia::where('estado', 'activa')
            ->where('fecha_inicio', '>=', $today->copy()->subDays(7))
            ->where('fecha_inicio', '<=', $today)
            ->where('fecha_vencimiento', '>=', $today)
            ->distinct('cliente_id')
            ->count('cliente_id');
        $porVencer = Membresia::where('estado', 'activa')
            ->where('fecha_vencimiento', '>=', $today)
            ->where('fecha_vencimiento', '<=', $today->copy()->addDays(7))
            ->distinct('cliente_id')
            ->count('cliente_id');
        $vencidas = Membresia::where(function ($q) use ($today) {
            $q->where('estado', 'vencida')
                ->orWhere(function ($q2) use ($today) {
                    $q2->where('estado', 'activa')
                        ->where('fecha_vencimiento', '<', $today);
                });
        })
            ->whereNotIn('cliente_id', function ($sub) use ($today) {
                $sub->select('cliente_id')
                    ->from('membresias')
                    ->where('estado', 'activa')
                    ->where('fecha_vencimiento', '>=', $today)
                    ->whereNull('deleted_at');
            })
            ->distinct('cliente_id')
            ->count('cliente_id');

        // Query base con relaciones
        // El filtro por estado ahora se maneja vía JS (DataTables) en la vista
        $estado = $request->input('estado', 'todas');

        // Traer todas las membresías y quedarnos con la más relevante por cliente:
        // Prioridad 0: Activa actualmente (ya inició y no ha vencido)
        // Prioridad 1: Activa en el futuro (aún no inicia)
        // Prioridad 2: Vencidas o inactivas
        $membresias = Membresia::with(['cliente', 'tipoMembresia'])
            ->orderByRaw("
                CASE 
                    WHEN estado = 'activa' AND fecha_inicio <= ? AND fecha_vencimiento >= ? THEN 0 
                    WHEN estado = 'activa' AND fecha_inicio > ? THEN 1 
                    ELSE 2 
                END
            ", [$today, $today, $today])
            ->orderBy('fecha_vencimiento', 'desc')
            ->get()
            ->unique('cliente_id')
            ->values()
            ->sortBy(function ($m) use ($today) {
                // 0 = Activa ahora, 1 = Futura, 2 = Vencida
                $isActivaAhora = $m->estado === 'activa' && $m->fecha_inicio <= $today && $m->fecha_vencimiento >= $today;
                $isFutura = $m->estado === 'activa' && $m->fecha_inicio > $today;

                if ($isActivaAhora) {
                    return [0, $m->fecha_vencimiento->timestamp]; // Activas: ordenadas por las que vencen más pronto
                } elseif ($isFutura) {
                    return [1, $m->fecha_inicio->timestamp]; // Futuras: ordenadas por las que inician más pronto
                } else {
                    return [2, -$m->fecha_vencimiento->timestamp]; // Vencidas: ordenadas por las más recientes primero
                }
            })
            ->values();

        // Tipos de membresía para el filtro
        $tiposMembresia = TipoMembresia::where('estado', 'activo')->get();

        // Vista (tabla o cards)
        $vista = $request->input('vista', 'tabla');

        return view('membresias-clientes.index', compact(
            'membresias',
            'totalMembresias',
            'activas',
            'recienCompradas',
            'porVencer',
            'vencidas',
            'tiposMembresia',
            'estado',
            'vista'
        ));
    }
}
