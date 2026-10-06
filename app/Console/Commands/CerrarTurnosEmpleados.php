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
                            {--dry-run : Solo muestra qué haría sin guardar nada}';

    protected $description = 'Cierra turnos sin salida y registra ausencias del día actual.';

    public function handle(): int
    {
        $fecha = $this->option('fecha')
            ? Carbon::parse($this->option('fecha'))->startOfDay()
            : Carbon::today()->startOfDay();

        $dryRun = $this->option('dry-run');

        $this->info("Procesando fecha: {$fecha->toDateString()} ".($dryRun ? '[DRY RUN]' : ''));
        $this->newLine();

        $empleadosActivos = Empleado::where('estado', 'activo')->get();

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

                $this->line("  [SIN SALIDA] {$empleado->nombre} — entró {$asistencia->hora_entrada}");

                if (! $dryRun) {
                    $horaEntrada = Carbon::parse((string) $asistencia->hora_entrada)->format('H:i:s');

                    $asistencia->update([
                        'hora_salida' => $salidaEsperada?->format('H:i:s'),
                        'salida_no_registrada' => true,
                        'salida_temprana' => false,
                        'horas_trabajadas' => $salidaEsperada
                            ? Carbon::parse($fecha->format('Y-m-d').' '.$horaEntrada)
                                ->diffInMinutes($salidaEsperada) / 60
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
            if (! $asistencia) {
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
