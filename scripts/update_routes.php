<?php
$f = 'c:/laragon/www/GymX/routes/web.php';
$c = file_get_contents($f);
$c = str_replace('});', "
    // Módulo de Asistencias (Escáner Facial)
    Route::get('/escanear', [\App\Http\Controllers\AsistenciaController::class, 'escanear'])->name('asistencias.escanear');
    Route::post('/escanear/registrar', [\App\Http\Controllers\AsistenciaController::class, 'registrarEscaneo'])->name('asistencias.registrar');
});", $c);
file_put_contents($f, $c);
echo "Routes updated.\n";