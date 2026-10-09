<?php
$f = 'c:/laragon/www/GymX/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);
// Fix the double title
$c = str_replace('title="Escanear Facialci' . chr(195) . chr(179) . 'n Facial"', 'title="Escanear Facial"', $c);
// fix span label too
$c = str_replace('>Recep' . chr(195) . chr(179) . 'n Facial<', '>Escanear Facial<', $c);
file_put_contents($f, $c);
echo "Fixed.\n";