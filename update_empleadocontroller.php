<?php
$f = 'c:/laragon/www/GymX/app/Http/Controllers/EmpleadoController.php';
$c = file_get_contents($f);

// Add use statements if missing
if (strpos($c, 'use App\Models\Pago;') === false) {
    $c = str_replace('use App\Models\Empleado;', "use App\Models\Empleado;\nuse App\Models\Pago;\nuse Carbon\Carbon;", $c);
}

$chartLogic = <<<'EOD'
        $totalEmpleados = Empleado::count();
        $empleadosActivos = Empleado::where('estado', 'activo')->count();
        $empleadosInactivos = Empleado::where('estado', 'inactivo')->count();
        $recepcionistasCount = Empleado::where('rol', 'empleado')->count();

        // -----------------------------------------------------
        // Datos para la gráfica de Productividad (Últimos 6 meses, empezando del anterior)
        // -----------------------------------------------------
        $mesesLabels = [];
        $mesesData = [];
        
        Carbon::setLocale('es'); // Para asegurar que los meses salgan en español

        for ($i = 6; $i >= 1; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mesNum = $date->format('n');
            $year = $date->format('Y');
            
            // Ejemplo: 'AGO'
            $mesesLabels[] = strtoupper(substr($date->translatedFormat('F'), 0, 3)); 

            // Contar pagos procesados en ese mes
            $count = Pago::whereYear('fecha_pago', $year)
                         ->whereMonth('fecha_pago', $mesNum)
                         ->count();
                         
            $mesesData[] = $count;
        }

        return view('empleados.index', compact(
            'empleados',
            'totalEmpleados',
            'empleadosActivos',
            'empleadosInactivos',
            'recepcionistasCount',
            'mesesLabels',
            'mesesData'
        ));
EOD;

$c = preg_replace("/\\\$totalEmpleados = Empleado::count\(\);.*?return view\('empleados\.index', compact\([^)]+\)\);/is", $chartLogic, $c);

file_put_contents($f, $c);
echo "Controller updated.";