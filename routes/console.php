<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sincronizar estado de clientes cada día a medianoche
Schedule::command('clientes:sincronizar-estado')->dailyAt('00:00');

// Revisar salidas pendientes después de la hora de turno y registrar ausencias al cierre del día.
Schedule::command('asistencias:cerrar-turnos')
    ->everyMinute()
    ->withoutOverlapping(5);
