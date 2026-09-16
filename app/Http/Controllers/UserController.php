<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Membresia;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function profile(): View
    {
        return view('perfil.index', [
            'usuario' => auth()->user(),
        ]);
    }

    public function userget()
    {
        $nombre = 'Jose Perez';
        $activeMembers = Cliente::where('estado', 'activo')->count();
        $monthlyRevenue = 42390; // mock
        $attendanceRate = 84.2; // mock
        $newSignups = Cliente::whereMonth('created_at', now()->month)->count();
        $totalClientes = Cliente::count();
        $membresiasPorVencer = Membresia::where('estado', 'activa')
            ->where('fecha_vencimiento', '<=', now()->addDays(7))
            ->count();

        $clientes = Cliente::with(['membresias' => function ($q) {
            $q->where('estado', 'activa');
            $q->with('tipoMembresia');
        }])->orderBy('id', 'desc')->get();

        // 1. Membresías por vencer detallado
        $porVencer = Membresia::with(['cliente', 'tipoMembresia'])
            ->where('estado', 'activa')
            ->where('fecha_vencimiento', '>=', now())
            ->where('fecha_vencimiento', '<=', now()->addDays(7))
            ->orderBy('fecha_vencimiento', 'asc')
            ->take(5)
            ->get()
            ->map(function ($m) {
                $dias = now()->startOfDay()->diffInDays(Carbon::parse($m->fecha_vencimiento)->startOfDay(), false);

                return (object) [
                    'nombre' => $m->cliente->nombre ?? 'Desconocido',
                    'plan' => $m->tipoMembresia ? $m->tipoMembresia->nombre : 'Membresía',
                    'dias' => max(0, (int) $dias),
                    'nivel' => $dias <= 3 ? 'critical' : 'warn',
                ];
            });

        // 2. Gráfica de crecimiento (Últimos 6 meses, empieza del mes anterior)
        $mesesLabels = [];
        $mesesData = [];
        Carbon::setLocale('es');
        for ($i = 6; $i >= 1; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mesNum = $date->format('n');
            $year = $date->format('Y');

            $mesesLabels[] = strtoupper(substr($date->translatedFormat('F'), 0, 3));

            $count = Cliente::whereYear('created_at', $year)
                ->whereMonth('created_at', $mesNum)
                ->count();

            $mesesData[] = $count;
        }

        // Renombrar variables para compatibilidad con la vista
        $growthLabels = $mesesLabels;
        $growthData = $mesesData;

        return view('user', compact(
            'nombre',
            'activeMembers',
            'totalClientes',
            'membresiasPorVencer',
            'monthlyRevenue',
            'attendanceRate',
            'newSignups',
            'clientes',
            'porVencer',
            'growthLabels',
            'growthData'
        ));
    }
}
