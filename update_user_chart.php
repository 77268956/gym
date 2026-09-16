<?php
$f = 'c:/laragon/www/GymX/resources/views/user.blade.php';
$c = file_get_contents($f);

// 1. Fix list fallback so if it is empty, it actually shows empty message
$oldListFallback = <<<EOD
                    @php
                        \$listaVencer = \$porVencer ?? [
                            (object)['nombre' => 'Logan Cole',   'plan' => 'Black Pass', 'dias' => 2, 'nivel' => 'critical'],
                            (object)['nombre' => 'Diana Prince', 'plan' => 'Standard',   'dias' => 3, 'nivel' => 'critical'],
                            (object)['nombre' => 'Roy Harper',   'plan' => 'Performance', 'dias' => 5, 'nivel' => 'warn'],
                            (object)['nombre' => 'Selina Kyle',  'plan' => 'Black Pass', 'dias' => 5, 'nivel' => 'warn'],
                        ];
                    @endphp
EOD;

$newListFallback = <<<EOD
                    @php
                        // Si no hay \$porVencer o está vacío y estamos en pruebas
                        \$listaVencer = isset(\$porVencer) ? \$porVencer : [];
                    @endphp
EOD;

$c = str_replace($oldListFallback, $newListFallback, $c);


// 2. Fix JS Chart data
$oldJs = <<<EOD
            data: {
                labels: ['E','F','M','A','M','J','J','A','S','O','N','D'],
                datasets: [{
                    data: [120,128,131,135,141,148,152,156,161,168,175,184],
EOD;

$newJs = <<<EOD
            data: {
                labels: {!! json_encode(\$growthLabels ?? ['1','2','3','4','5','6']) !!},
                datasets: [{
                    data: {!! json_encode(\$growthData ?? [0,0,0,0,0,0]) !!},
EOD;

$c = str_replace($oldJs, $newJs, $c);

file_put_contents($f, $c);
echo "User chart updated.";