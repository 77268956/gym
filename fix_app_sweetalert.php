<?php
$f = 'c:/laragon/www/GymX/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

$c = preg_replace("/title:\s*'[^']+',\s*text:\s*\"[^\"]+\",/s", "title: '¿Estás seguro?',\n                text: 'Esta acción no se puede deshacer.',", $c);
$c = preg_replace("/confirmButtonText:\s*'<i[^>]+>\s*<\/i>[^']+',/s", "confirmButtonText: '<i class=\"fas fa-trash-alt\"></i> Sí, eliminar',", $c);

file_put_contents($f, $c);
echo "Fixed.\n";