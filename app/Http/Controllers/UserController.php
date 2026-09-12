<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Membresia;

class UserController extends Controller
{
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
        }])->orderBy('id', 'desc')->get();

        $maintenanceItems = collect([]); // mock
        $classes = collect([]); // mock
        $transactions = collect([]); // mock
        $growthLabels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        $growthData = [1200, 1260, 1310, 1350, 1400, 1430, 1500, 1520, 1580, 1650, 1730, 1842];

        return view('user', compact(
            'nombre',
            'activeMembers',
            'totalClientes',
            'membresiasPorVencer',
            'monthlyRevenue',
            'attendanceRate',
            'newSignups',
            'maintenanceItems',
            'classes',
            'transactions',
            'growthLabels',
            'growthData',
            'clientes'
        ));
    }

    public function empleados()
    {
        $totalEmpleados = 24;
        $empleadosActivos = 22;
        $turnosPorCubrir = 3;
        $nuevosEsteMes = 2;

        $empleados = [
            (object) ['nombre' => 'Marcus Vance', 'cedula' => '0801-1985-12345', 'telefono' => '(504) 9811-2233', 'rol' => 'entrenador', 'experiencia' => '5 años', 'turno' => 'Mañana', 'estado' => 'activo'],
            (object) ['nombre' => 'Sarah Connor', 'cedula' => '0801-1990-54321', 'telefono' => '(504) 9922-3344', 'rol' => 'entrenador', 'experiencia' => '3 años', 'turno' => 'Tarde', 'estado' => 'activo'],
            (object) ['nombre' => 'John Kreese', 'cedula' => '0801-1980-98765', 'telefono' => '(504) 9833-4455', 'rol' => 'coordinador', 'experiencia' => '10 años', 'turno' => 'Completo', 'estado' => 'activo'],
            (object) ['nombre' => 'Amanda Waller', 'cedula' => '0801-1992-45678', 'telefono' => '(504) 9844-5566', 'rol' => 'recepcion', 'experiencia' => '1 año', 'turno' => 'Mañana', 'estado' => 'inactivo'],
        ];

        $topEmpleados = [
            (object) ['nombre' => 'Marcus Vance', 'dato' => '120 horas este mes', 'racha' => 'Puntualidad 100%', 'nivel' => 'active'],
            (object) ['nombre' => 'Sarah Connor', 'dato' => '115 horas este mes', 'racha' => 'Puntualidad 98%', 'nivel' => 'active'],
            (object) ['nombre' => 'John Kreese', 'dato' => '180 horas este mes', 'racha' => 'Puntualidad 95%', 'nivel' => 'warn'],
        ];

        $turnosProximos = [
            (object) ['nombre' => 'Marcus Vance', 'plan' => 'Tactical Conditioning', 'dias' => 1, 'nivel' => 'warn'],
            (object) ['nombre' => 'Sarah Connor', 'plan' => 'Iron Barbell Olympic', 'dias' => 1, 'nivel' => 'warn'],
            (object) ['nombre' => 'Amanda Waller', 'plan' => 'Recepción Principal', 'dias' => 2, 'nivel' => 'critical'],
        ];

        return view('empleados', compact(
            'totalEmpleados',
            'empleadosActivos',
            'turnosPorCubrir',
            'nuevosEsteMes',
            'empleados',
            'topEmpleados',
            'turnosProximos'
        ));
    }
}
