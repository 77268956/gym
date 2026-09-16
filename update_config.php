<?php
// 1. Update ConfiguracionController
$ctrl = file_get_contents('c:/laragon/www/GymX/app/Http/Controllers/ConfiguracionController.php');

$ctrl = str_replace(
    "use App\\Models\\ConfiguracionGeneral;",
    "use App\\Models\\ConfiguracionGeneral;\nuse App\\Models\\ConfiguracionPunto;"
, $ctrl);

$ctrl = str_replace(
    "return view('configuracion.index', compact('configuracion'));",
    "\$configuracionPuntos = ConfiguracionPunto::first();\n        return view('configuracion.index', compact('configuracion', 'configuracionPuntos'));"
, $ctrl);

$ctrl = str_replace(
    "\$configuracion->save();\n\n        // Limpiar cach",
    "\$configuracion->save();\n\n        // Puntos por visita\n        ConfiguracionPunto::updateOrCreate([], [\n            'puntos_por_visita' => (int) \$request->input('puntos_por_visita', 10),\n            'vigente_desde' => now()->toDateString(),\n        ]);\n\n        // Limpiar cach"
, $ctrl);

file_put_contents('c:/laragon/www/GymX/app/Http/Controllers/ConfiguracionController.php', $ctrl);
echo "Controller updated.\n";

// 2. Update AsistenciaController to use the dynamic points value
$asist = file_get_contents('c:/laragon/www/GymX/app/Http/Controllers/AsistenciaController.php');

$asist = str_replace(
    "use Carbon\\Carbon;",
    "use App\\Models\\ConfiguracionPunto;\nuse Carbon\\Carbon;"
, $asist);

$asist = str_replace(
    "\$cliente->increment('puntos_recompensa', 1);\n\n        // Crear registro del movimiento de puntos\n        \\App\\Models\\MovimientoPunto::create([\n            'cliente_id' => \$cliente->id,\n            'tipo_movimiento' => 'ganado',\n            'puntos' => 1,",
    "\$puntosConfig = ConfiguracionPunto::first();\n        \$puntosGanados = \$puntosConfig ? \$puntosConfig->puntos_por_visita : 10;\n        \$cliente->increment('puntos_recompensa', \$puntosGanados);\n\n        // Crear registro del movimiento de puntos\n        \\App\\Models\\MovimientoPunto::create([\n            'cliente_id' => \$cliente->id,\n            'tipo_movimiento' => 'ganado',\n            'puntos' => \$puntosGanados,"
, $asist);

// Also update response to include puntos ganados
$asist = str_replace(
    "'message' => '¡Bienvenido! Asistencia registrada exitosamente.',",
    "'message' => '¡Bienvenido! Asistencia registrada exitosamente.',\n            'puntos' => \$puntosGanados,"
, $asist);

file_put_contents('c:/laragon/www/GymX/app/Http/Controllers/AsistenciaController.php', $asist);
echo "AsistenciaController updated.\n";