<?php
$f = 'c:/laragon/www/GymX/app/Http/Controllers/UserController.php';
$c = file_get_contents($f);

// Add Carbon if missing
if (strpos($c, 'use Carbon\Carbon;') === false) {
    $c = str_replace('use App\Models\Membresia;', "use App\Models\Membresia;\nuse Carbon\Carbon;", $c);
}

$chartLogic = <<<'EOD'
        $membresiasPorVencer = Membresia::where('estado', 'activa')
            ->where('fecha_vencimiento', '<=', now()->addDays(7))
            ->count();
            
        $clientes = Cliente::with(['membresias' => function ($q) {
            $q->where('estado', 'activa');
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
                return (object)[
                    'nombre' => $m->cliente->nombre ?? 'Desconocido',
                    'plan' => $m->tipoMembresia ? $m->tipoMembresia->nombre : 'Membresía',
                    'dias' => max(0, (int)$dias),
                    'nivel' => $dias <= 3 ? 'critical' : 'warn'
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
EOD;

$c = preg_replace("/\\\$membresiasPorVencer = Membresia::where.*?\}\n/is", $chartLogic . "\n", $c);

file_put_contents($f, $c);
echo "UserController updated.";