<?php
$f = 'c:/laragon/www/GymX/app/Http/Controllers/AsistenciaController.php';
$c = file_get_contents($f);

$c = str_replace("AsistenciaCliente::create([
            'cliente_id' => \$cliente->id,", "\$asistencia = AsistenciaCliente::create([
            'cliente_id' => \$cliente->id,", $c);

$movimiento = <<<'EOD'
        $cliente->increment('puntos_recompensa', 1);

        // Crear registro del movimiento de puntos
        \App\Models\MovimientoPunto::create([
            'cliente_id' => $cliente->id,
            'tipo_movimiento' => 'ganado',
            'puntos' => 1,
            'origen_tabla' => 'asistencias_clientes',
            'origen_id' => $asistencia->id,
            'fecha' => now(),
        ]);
EOD;

$c = str_replace('$cliente->increment(\'puntos_recompensa\', 1);', $movimiento, $c);

file_put_contents($f, $c);
echo "AsistenciaController updated for points.\n";