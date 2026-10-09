<?php
$f = 'c:/laragon/www/GymX/app/Http/Controllers/PagoController.php';
$c = file_get_contents($f);

$oldCode = <<<EOD
        \$totalIngresos = \$pagos->sum('monto');
        \$pagosHoy = Pago::whereDate('fecha_pago', Carbon::today())->sum('monto');
EOD;

$newCode = <<<EOD
        \$totalIngresos = \$pagos->sum('monto');
        \$pagosHoy = Pago::whereDate('fecha_pago', Carbon::today())->sum('monto');
        \$pagosMes = Pago::whereYear('fecha_pago', Carbon::now()->year)
                        ->whereMonth('fecha_pago', Carbon::now()->month)
                        ->sum('monto');
EOD;

$c = str_replace($oldCode, $newCode, $c);

$c = str_replace(
    "compact('pagos', 'totalIngresos', 'pagosHoy', 'clientes', 'tiposMembresia')", 
    "compact('pagos', 'totalIngresos', 'pagosHoy', 'pagosMes', 'clientes', 'tiposMembresia')", 
    $c
);

file_put_contents($f, $c);
echo "PagoController updated.";