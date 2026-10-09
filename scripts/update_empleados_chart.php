<?php
$f = 'c:/laragon/www/GymX/resources/views/empleados/index.blade.php';
$c = file_get_contents($f);

$jsOld = <<<EOD
            data: {
                labels: ['E','F','M','A','M','J'],
                datasets: [{
                    label: 'Registros',
                    data: [12, 18, 15, 20, 24, 30],
EOD;

$jsNew = <<<EOD
            data: {
                labels: {!! json_encode(\$mesesLabels ?? ['1','2','3','4','5','6']) !!},
                datasets: [{
                    label: 'Pagos Procesados',
                    data: {!! json_encode(\$mesesData ?? [0,0,0,0,0,0]) !!},
EOD;

$c = str_replace($jsOld, $jsNew, $c);
file_put_contents($f, $c);
echo "Chart JS updated.";