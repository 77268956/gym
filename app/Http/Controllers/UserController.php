<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function userget()
    {
        $nombre = 'Jose Perez';
        $activeMembers = 1842;
        $monthlyRevenue = 42390;
        $attendanceRate = 84.2;
        $newSignups = 158;
        $maintenanceItems = collect([
            (object) ['code' => 'EQ-09', 'name' => 'Treadmill Zone 2', 'issue' => 'Drive Belt Slipping', 'level' => 'critical', 'time' => '55m ago'],
            (object) ['code' => 'EQ-44', 'name' => 'Cable Crossover A', 'issue' => 'Frayed Pulley Cable', 'level' => 'warn', 'time' => '1h ago'],
            (object) ['code' => 'EQ-12', 'name' => 'Leg Press Station', 'issue' => 'Hydraulic Cylinder Leak', 'level' => 'warn', 'time' => '3d ago'],
        ]);
        $classes = collect([
            (object) ['time' => '06:00 AM', 'class' => 'Tactical Conditioning', 'trainer' => 'Marcus Vance', 'attendance' => '24/25', 'status' => 'full'],
            (object) ['time' => '08:30 AM', 'class' => 'Iron Barbell Olympic', 'trainer' => 'Sarah Connor', 'attendance' => '12/15', 'status' => 'active'],
            (object) ['time' => '12:00 PM', 'class' => 'Combat HIIT Conditioning', 'trainer' => 'John Kreese', 'attendance' => '18/20', 'status' => 'active'],
            (object) ['time' => '05:30 PM', 'class' => 'Power-Lifting Method', 'trainer' => 'Marcus Vance', 'attendance' => '08/12', 'status' => 'pending'],
        ]);
        $transactions = collect([
            (object) ['name' => 'Logan Cole', 'plan' => 'Black Pass Annual · VISA', 'amount' => '149.00', 'time' => '09:42 AM'],
            (object) ['name' => 'Diana Prince', 'plan' => 'Standard Monthly · APPLE PAY', 'amount' => '79.00', 'time' => '09:11 AM'],
            (object) ['name' => 'Roy Harper', 'plan' => 'Performance Tier · MASTERCARD', 'amount' => '119.00', 'time' => '08:50 AM'],
            (object) ['name' => 'Selina Kyle', 'plan' => 'Black Pass Monthly · VISA', 'amount' => '149.00', 'time' => '07:22 AM'],
        ]);
        $growthLabels = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        $growthData = [1200, 1260, 1310, 1350, 1400, 1430, 1500, 1520, 1580, 1650, 1730, 1842];

        return view('user', compact(
            'nombre',
            'activeMembers',
            'monthlyRevenue',
            'attendanceRate',
            'newSignups',
            'maintenanceItems',
            'classes',
            'transactions',
            'growthLabels',
            'growthData',
        ));
    }
    public function empleados()
    {
        $totalEmpleados = 24;
        $empleadosActivos = 22;
        $turnosPorCubrir = 3;
        $nuevosEsteMes = 2;
        
        $empleados = [
            (object)['nombre' => 'Marcus Vance', 'cedula' => '0801-1985-12345', 'telefono' => '(504) 9811-2233', 'rol' => 'entrenador', 'experiencia' => '5 años', 'turno' => 'Mañana', 'estado' => 'activo'],
            (object)['nombre' => 'Sarah Connor', 'cedula' => '0801-1990-54321', 'telefono' => '(504) 9922-3344', 'rol' => 'entrenador', 'experiencia' => '3 años', 'turno' => 'Tarde', 'estado' => 'activo'],
            (object)['nombre' => 'John Kreese', 'cedula' => '0801-1980-98765', 'telefono' => '(504) 9833-4455', 'rol' => 'coordinador', 'experiencia' => '10 años', 'turno' => 'Completo', 'estado' => 'activo'],
            (object)['nombre' => 'Amanda Waller', 'cedula' => '0801-1992-45678', 'telefono' => '(504) 9844-5566', 'rol' => 'recepcion', 'experiencia' => '1 año', 'turno' => 'Mañana', 'estado' => 'inactivo'],
        ];

        $topEmpleados = [
            (object)['nombre' => 'Marcus Vance', 'dato' => '120 horas este mes', 'racha' => 'Puntualidad 100%', 'nivel' => 'active'],
            (object)['nombre' => 'Sarah Connor', 'dato' => '115 horas este mes', 'racha' => 'Puntualidad 98%', 'nivel' => 'active'],
            (object)['nombre' => 'John Kreese', 'dato' => '180 horas este mes', 'racha' => 'Puntualidad 95%', 'nivel' => 'warn'],
        ];

        $turnosProximos = [
            (object)['nombre' => 'Marcus Vance', 'plan' => 'Tactical Conditioning', 'dias' => 1, 'nivel' => 'warn'],
            (object)['nombre' => 'Sarah Connor', 'plan' => 'Iron Barbell Olympic', 'dias' => 1, 'nivel' => 'warn'],
            (object)['nombre' => 'Amanda Waller', 'plan' => 'Recepción Principal', 'dias' => 2, 'nivel' => 'critical'],
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
