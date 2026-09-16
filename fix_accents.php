<?php
$f = 'c:/laragon/www/GymX/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/Estǭs seguro\?/', '¿Estás seguro?', $c);
$c = preg_replace('/Esta accin no se puede deshacer./', 'Esta acción no se puede deshacer.', $c);
$c = preg_replace('/S, eliminar/', 'Sí, eliminar', $c);
file_put_contents($f, $c);
echo "Fixed accents.\n";