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

        // Determinar el rango de fechas basado en el filtro
        $periodo = $request->input('periodo', 'mes');
        $startDate = $today->copy();
        $endDate = $today->copy()->endOfDay();
        $labelPeriodo = 'del Mes';

        switch ($periodo) {
            case 'dia':
                $startDate = $today->copy();
                $labelPeriodo = 'de Hoy';
                break;
            case 'semana':
                $startDate = $today->copy()->startOfWeek();
                $labelPeriodo = 'de la Semana';
                break;
            case 'ano':
                $startDate = $today->copy()->startOfYear();
                $labelPeriodo = 'del Año';
                break;
            case 'mes':
            default:
                $startDate = $today->copy()->startOfMonth();
                $labelPeriodo = 'del Mes';
                $periodo = 'mes';
                break;
        }

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
