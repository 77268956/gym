<?php

namespace App\Console\Commands;

use App\Models\AlertaSistema;
use App\Models\AsistenciaEmpleado;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CerrarTurnosEmpleados extends Command
{
    protected $signature = 'asistencias:cerrar-turnos
                            {--fecha= : Fecha a procesar (Y-m-d). Por defecto: hoy}
                            {--empleado= : Nombre exacto para procesar solo un empleado}
                            {--dry-run : Solo muestra qué haría sin guardar nada}';

    protected $description = 'Cierra turnos sin salida y registra ausencias del día actual.';

    public function handle(): int
    {
        $fecha = $this->option('fecha')
            ? Carbon::parse($this->option('fecha'))->startOfDay()
            : Carbon::today()->startOfDay();

        $dryRun = $this->option('dry-run');
        $ahora = Carbon::now();
        $registrarAusencias = $fecha->lt($ahora->copy()->startOfDay())
            || ($fecha->isSameDay($ahora) && $ahora->format('H:i') >= '23:59');

        $this->info("Procesando fecha: {$fecha->toDateString()} ".($dryRun ? '[DRY RUN]' : ''));
        $this->newLine();

        $empleadosQuery = Empleado::where('estado', 'activo');

        if ($this->option('empleado')) {
            $empleadosQuery->where('nombre', $this->option('empleado'));
        }

        $empleadosActivos = $empleadosQuery->get();

        $turnosCerrados = 0;
        $ausenciasRegistradas = 0;

        foreach ($empleadosActivos as $empleado) {
            $asistencia = AsistenciaEmpleado::where('empleado_id', $empleado->id)
                ->whereDate('fecha', $fecha)
                ->first();

            // ── Caso 1: Tiene entrada pero no tiene salida ──────────────────
            if ($asistencia && $asistencia->hora_entrada && ! $asistencia->hora_salida) {
                $salidaEsperada = $empleado->hora_salida_turno
                    ? Carbon::parse($fecha->format('Y-m-d').' '.$empleado->hora_salida_turno)
                    : null;

                $momentoLimite = $salidaEsperada
                    ? $salidaEsperada->copy()->addMinutes($empleado->tolerancia_minutos ?? 10)
                    : $fecha->copy()->endOfDay();

                if ($ahora->lessThan($momentoLimite)) {
                    continue;
                }

                $this->line("  [SIN SALIDA] {$empleado->nombre} — entró {$asistencia->hora_entrada}");

                if (! $dryRun) {
                    $horaEntrada = Carbon::parse((string) $asistencia->hora_entrada)->format('H:i:s');
                    $momentoEntrada = Carbon::parse($fecha->format('Y-m-d').' '.$horaEntrada);
                    $salidaRegistrada = $salidaEsperada && $salidaEsperada->lessThan($momentoEntrada)
                        ? $momentoEntrada
                        : $salidaEsperada;

                    $asistencia->update([
                        'hora_salida' => $salidaRegistrada?->format('H:i:s'),
                        'salida_no_registrada' => true,
                        'salida_temprana' => false,
                        'horas_trabajadas' => $salidaRegistrada
                            ? $momentoEntrada->diffInMinutes($salidaRegistrada) / 60
                        : null,
                    ]);

                    AlertaSistema::registrar(
                        'salida_no_registrada',
                        'asistencias_empleados',
                        $asistencia->id,
                        "{$empleado->nombre} no registró su salida el {$fecha->format('d/m/Y')}."
                    );
                }

                $turnosCerrados++;

                continue;
            }

            // ── Caso 2: No tiene ningún registro → ausencia ─────────────────
            if (! $asistencia && $registrarAusencias) {
                $this->line("  [AUSENTE]    {$empleado->nombre}");

                if (! $dryRun) {
                    $asistencia = AsistenciaEmpleado::create([
                        'empleado_id' => $empleado->id,
                        'fecha' => $fecha->format('Y-m-d'),
                        'hora_entrada' => null,
                        'hora_salida' => null,
                        'horas_trabajadas' => 0,
                        'tardanza' => false,
                        'salida_temprana' => false,
                        'salida_no_registrada' => false,
                        'metodo_registro' => 'sistema',
                    ]);

                    AlertaSistema::registrar(
                        'ausencia',
                        'asistencias_empleados',
                        $asistencia->id,
                        "{$empleado->nombre} no registró asistencia el {$fecha->format('d/m/Y')}."
                    );
                }

                $ausenciasRegistradas++;
            }
        }

        $this->newLine();
        $this->info("✔ Turnos cerrados (sin salida): {$turnosCerrados}");
        $this->info("✔ Ausencias registradas:        {$ausenciasRegistradas}");

        return self::SUCCESS;
    }
}
