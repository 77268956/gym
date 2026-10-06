<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaCliente;
use App\Models\Membresia;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        $filtros = $request->validate([
            'periodo' => 'nullable|in:mes,semana,rango',
            'mes' => 'nullable|date_format:Y-m',
            'semana' => 'nullable|date_format:Y-m-d',
            'desde' => 'required_if:periodo,rango|nullable|date_format:Y-m-d',
            'hasta' => 'required_if:periodo,rango|nullable|date_format:Y-m-d|after_or_equal:desde',
        ]);

        $periodo = $filtros['periodo'] ?? 'mes';
        $mesSeleccionado = $filtros['mes'] ?? $today->format('Y-m');
        $inicioMesSeleccionado = Carbon::createFromFormat('Y-m-d', $mesSeleccionado.'-01')->startOfMonth();
        $finMesSeleccionado = $inicioMesSeleccionado->copy()->endOfMonth();
        $mesesDisponibles = [];

        for ($mesesAtras = 23; $mesesAtras >= 0; $mesesAtras--) {
            $mes = $today->copy()->subMonths($mesesAtras)->startOfMonth();
            $mesesDisponibles[$mes->format('Y-m')] = ucfirst($mes->locale('es')->isoFormat('MMMM YYYY'));
        }

        $semanasDisponibles = [];
        $inicioSemana = $inicioMesSeleccionado->copy();

        while ($inicioSemana->lte($finMesSeleccionado)) {
            $finSemana = $inicioSemana->copy()->endOfWeek(Carbon::SUNDAY);
            if ($finSemana->gt($finMesSeleccionado)) {
                $finSemana = $finMesSeleccionado->copy();
            }

            $semanasDisponibles[] = [
                'inicio' => $inicioSemana->format('Y-m-d'),
                'fin' => $finSemana->format('Y-m-d'),
                'etiqueta' => $inicioSemana->format('j').'-'.$finSemana->format('j').' '.
                    $inicioSemana->locale('es')->isoFormat('MMM'),
            ];

            $inicioSemana = $finSemana->copy()->addDay()->startOfDay();
        }

        $semanaActiva = collect($semanasDisponibles)->first(function (array $semana) use ($today, $filtros): bool {
            if (isset($filtros['semana']) && $semana['inicio'] === $filtros['semana']) {
                return true;
            }

            return ! isset($filtros['semana'])
                && $today->greaterThanOrEqualTo(Carbon::parse($semana['inicio']))
                && $today->lessThanOrEqualTo(Carbon::parse($semana['fin']));
        });
        $semanaActiva ??= $semanasDisponibles[0];
        $semanaSeleccionada = $semanaActiva['inicio'];

        if ($periodo === 'semana') {
            $semana = collect($semanasDisponibles)->firstWhere('inicio', $semanaSeleccionada);
            $startDate = Carbon::parse($semana['inicio'])->startOfDay();
            $endDate = Carbon::parse($semana['fin'])->endOfDay();
            $labelPeriodo = 'de la semana';
        } elseif ($periodo === 'rango') {
            $startDate = Carbon::parse($filtros['desde'])->startOfDay();
            $endDate = Carbon::parse($filtros['hasta'])->endOfDay();
            $labelPeriodo = 'del periodo seleccionado';
        } else {
            $startDate = $inicioMesSeleccionado;
            $endDate = $finMesSeleccionado;
            if ($inicioMesSeleccionado->isSameMonth($today)) {
                $endDate = $today->copy()->endOfDay();
            }
            $labelPeriodo = 'de '.ucfirst($inicioMesSeleccionado->locale('es')->isoFormat('MMMM YYYY'));
            $periodo = 'mes';
        }

        $desdeSeleccionado = $filtros['desde'] ?? '';
        $hastaSeleccionado = $filtros['hasta'] ?? '';
        $rangoFechas = $startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y');

        // --- 1. Top KPIs ---
        // Ingresos filtrados por periodo
        $ingresosPeriodo = Pago::whereBetween('fecha_pago', [$startDate, $endDate])->sum('monto');

        // Ingresos de Hoy (siempre útil mostrar el de hoy fijo, o lo cambiamos según filtro)
        // La tarjeta dirá "Ingresos (Periodo)" y la de al lado "Asistencias (Periodo)"
        // Mejor adaptamos las tarjetas al filtro seleccionado.
        $asistenciasPeriodo = AsistenciaCliente::whereBetween('fecha', [$startDate, $endDate])
            ->where('exitoso', true)
            ->count();

        // Clientes Activos (excluyendo clientes eliminados)
        $clientesActivos = Membresia::whereHas('cliente') // <-- FIX: Solo clientes que no están en soft delete
            ->where('estado', 'activa')
            ->where('fecha_inicio', '<=', $today)
            ->where('fecha_vencimiento', '>=', $today)
            ->distinct('cliente_id')
            ->count('cliente_id');

        // --- 2. Gráficos ---

        // Ingresos por Método de Pago (Periodo)
        $pagosPorMetodo = Pago::whereBetween('fecha_pago', [$startDate, $endDate])
            ->selectRaw('metodo_pago, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago')
            ->toArray();

        // Preparar para Chart.js
        $chartMetodosLabels = array_keys($pagosPorMetodo);
        $chartMetodosData = array_values($pagosPorMetodo);

        // Asistencias por Hora (Periodo)
        // Agrupa las asistencias por hora para ver los horarios pico en ese periodo
        $asistenciasPorHora = AsistenciaCliente::whereBetween('fecha', [$startDate, $endDate])
            ->where('exitoso', true)
            ->select(DB::raw('HOUR(hora) as hora_dia'), DB::raw('count(*) as total'))
            ->groupBy('hora_dia')
            ->pluck('total', 'hora_dia')
            ->toArray();

        $chartHorasLabels = [];
        $chartHorasData = [];
        // Llenar desde las 5 AM hasta las 22 PM
        for ($i = 5; $i <= 22; $i++) {
            $chartHorasLabels[] = $i.':00';
            $chartHorasData[] = $asistenciasPorHora[$i] ?? 0;
        }

        // --- 3. Tablas ---

        // Vencimientos Próximos (próximos 7 días)
        $vencimientosProximos = Membresia::with(['cliente', 'tipoMembresia'])
            ->where('estado', 'activa')
            ->where('fecha_vencimiento', '>=', $today)
            ->where('fecha_vencimiento', '<=', $today->copy()->addDays(7))
            ->orderBy('fecha_vencimiento', 'asc')
            ->take(6)
            ->get();

        // Pagos Recientes
        $pagosRecientes = Pago::with(['cliente'])
            ->whereBetween('fecha_pago', [$startDate, $endDate])
            ->orderBy('fecha_pago', 'desc')
            ->get();

        return view('dashboard', compact(
            'periodo',
            'labelPeriodo',
            'mesesDisponibles',
            'mesSeleccionado',
            'semanasDisponibles',
            'semanaSeleccionada',
            'desdeSeleccionado',
            'hastaSeleccionado',
            'rangoFechas',
            'ingresosPeriodo',
            'clientesActivos',
            'asistenciasPeriodo',
            'chartMetodosLabels',
            'chartMetodosData',
            'chartHorasLabels',
            'chartHorasData',
            'vencimientosProximos',
            'pagosRecientes'
        ));
    }
}
