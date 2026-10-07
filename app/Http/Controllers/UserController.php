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
        $clientesActivos = Cliente::where('estado', 'activo')->count();
        $nuevosClientes = Cliente::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalClientes = Cliente::count();
        $membresiasPorVencer = Membresia::where('estado', 'activa')
            ->where('fecha_vencimiento', '>=', today())
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
                $vencimiento = Carbon::parse($m->fecha_vencimiento);
                $horasRestantes = now()->diffInHours($vencimiento, false);
                $horasRestantes = max(0, (float) $horasRestantes);
                $horasRedondeadas = (int) ceil($horasRestantes);
                $diasRestantes = (int) ceil($horasRestantes / 24);

                return (object) [
                    'nombre' => $m->cliente->nombre_completo ?? 'Desconocido',
                    'plan' => $m->tipoMembresia ? $m->tipoMembresia->nombre : 'Membresía',
                    'dias' => $diasRestantes,
                    'tiempo' => $horasRestantes < 24
                        ? $horasRedondeadas.'h'
                        : $diasRestantes.' D',
                    'nivel' => $horasRestantes < 72 ? 'critical' : 'warn',
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
            'clientesActivos',
            'totalClientes',
            'membresiasPorVencer',
            'nuevosClientes',
            'clientes',
            'porVencer',
            'growthLabels',
            'growthData'
        ));
    }
}
