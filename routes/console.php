<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sincronizar estado de clientes cada día a medianoche
Schedule::command('clientes:sincronizar-estado')->dailyAt('00:00');

// Cerrar turnos sin salida y registrar ausencias cada noche a las 23:59
Schedule::command('asistencias:cerrar-turnos')->dailyAt('23:59');
