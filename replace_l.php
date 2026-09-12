<?php
$files = [
    'c:/laragon/www/GymX/resources/views/clientes/create.blade.php',
    'c:/laragon/www/GymX/resources/views/membresias/index.blade.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        $content = str_replace('L. {{', '{{ $gymConfig->simbolo_moneda }} {{', $content);
        $content = str_replace('text-success">L.</span', 'text-success">{{ $gymConfig->simbolo_moneda }}</span', $content);
        file_put_contents($f, $content);
    }
}
echo "Replaced safely.\n";