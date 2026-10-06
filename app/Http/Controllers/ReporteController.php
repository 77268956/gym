<?php

namespace App\Http\Controllers;

use App\Models\AlertaSistema;
use App\Models\AsistenciaCliente;
use App\Models\AsistenciaEmpleado;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Membresia;
use App\Models\Pago;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        return view('reportes.index', $this->datosReporte($request));
    }

    public function pdf(Request $request): View
    {
        return view('reportes.pdf', [...$this->datosReporte($request), 'modoImpresion' => true]);
    }

    /** @return array<string, mixed> */
    private function datosReporte(Request $request): array
    {
        $filtros = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:desde'],
        ]);

        $hoy = Carbon::today();
        $inicio = Carbon::parse($filtros['desde'] ?? $hoy->copy()->startOfMonth()->format('Y-m-d'))->startOfDay();
        $fin = Carbon::parse($filtros['hasta'] ?? $hoy->format('Y-m-d'))->endOfDay();
        $desde = $inicio->toDateString();
        $hasta = $fin->toDateString();

        $pagos = Pago::query()->whereBetween('fecha_pago', [$inicio, $fin]);
        $pagosPorMetodo = (clone $pagos)
            ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->get();

        $dias = collect(CarbonPeriod::create($inicio->copy()->startOfDay(), '1 day', $fin->copy()->startOfDay()))
            ->map(fn (Carbon $fecha): string => $fecha->format('Y-m-d'));
        $ingresosPorDia = (clone $pagos)
            ->selectRaw('DATE(fecha_pago) as fecha, SUM(monto) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');
        $clientesNuevosPorDia = Cliente::query()
            ->whereBetween('created_at', [$inicio, $fin])
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');
        $asistenciasClientes = AsistenciaCliente::query()
            ->whereBetween('fecha', [$desde, $hasta])
            ->where('exitoso', true);
        $visitasPorDia = (clone $asistenciasClientes)
            ->selectRaw('fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $asistenciasEmpleados = AsistenciaEmpleado::query()->whereBetween('fecha', [$desde, $hasta]);

        $alertas = AlertaSistema::query()->whereBetween('created_at', [$inicio, $fin]);
        $alertasPorTipo = (clone $alertas)
            ->selectRaw('tipo_alerta, COUNT(*) as cantidad')
            ->groupBy('tipo_alerta')
            ->orderByDesc('cantidad')
            ->get();

        $etiquetasDias = $dias->map(fn (string $dia): string => Carbon::parse($dia)->format('d/m'))->values();

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'rangoFechas' => $inicio->format('d/m/Y').' - '.$fin->format('d/m/Y'),
            'graficas' => [
                'etiquetasDias' => $etiquetasDias,
                'ingresosPorDia' => $dias->map(fn (string $dia): float => (float) ($ingresosPorDia[$dia] ?? 0))->values(),
                'clientesNuevosPorDia' => $dias->map(fn (string $dia): int => (int) ($clientesNuevosPorDia[$dia] ?? 0))->values(),
                'visitasPorDia' => $dias->map(fn (string $dia): int => (int) ($visitasPorDia[$dia] ?? 0))->values(),
                'metodosPago' => [
                    'labels' => $pagosPorMetodo->pluck('metodo_pago')->map(fn (string $metodo): string => ucfirst($metodo))->values(),
                    'valores' => $pagosPorMetodo->pluck('total')->map(fn ($total): float => (float) $total)->values(),
                ],
                'incidenciasEmpleados' => [
                    'labels' => ['Llegadas tarde', 'Salidas tempranas', 'Sin marcar salida'],
                    'valores' => [
                        (clone $asistenciasEmpleados)->where('tardanza', true)->count(),
                        (clone $asistenciasEmpleados)->where('salida_temprana', true)->count(),
                        (clone $asistenciasEmpleados)->where('salida_no_registrada', true)->count(),
                    ],
                ],
                'alertas' => [
                    'labels' => $alertasPorTipo->pluck('tipo_alerta')->map(fn (string $tipo): string => ucfirst(str_replace('_', ' ', $tipo)))->values(),
                    'valores' => $alertasPorTipo->pluck('cantidad')->map(fn ($cantidad): int => (int) $cantidad)->values(),
                ],
            ],
            'finanzas' => [
                'ingresos' => (clone $pagos)->sum('monto'),
                'transacciones' => (clone $pagos)->count(),
                'promedio' => (clone $pagos)->avg('monto') ?? 0,
                'porMetodo' => $pagosPorMetodo,
                'pagosRecientes' => (clone $pagos)->with(['cliente:id,nombre', 'empleado:id,nombre'])
                    ->latest('fecha_pago')->limit(10)->get(),
            ],
            'clientes' => [
                'nuevos' => Cliente::query()->whereBetween('created_at', [$inicio, $fin])->count(),
                'activos' => Cliente::query()->where('estado', 'activo')->count(),
                'membresiasPorVencer' => Membresia::query()->whereBetween('fecha_vencimiento', [$desde, $hasta])
                    ->where('estado', 'activa')->count(),
                'asistencias' => (clone $asistenciasClientes)->count(),
                'nuevosClientes' => Cliente::query()->select(['id', 'nombre', 'cedula', 'created_at'])
                    ->whereBetween('created_at', [$inicio, $fin])->latest()->limit(10)->get(),
            ],
            'empleados' => [
                'total' => Empleado::query()->where('estado', 'activo')->count(),
                'asistencias' => (clone $asistenciasEmpleados)->count(),
                'tardanzas' => (clone $asistenciasEmpleados)->where('tardanza', true)->count(),
                'salidasTempranas' => (clone $asistenciasEmpleados)->where('salida_temprana', true)->count(),
                'salidasSinMarcar' => (clone $asistenciasEmpleados)->where('salida_no_registrada', true)->count(),
                'incidencias' => (clone $asistenciasEmpleados)
                    ->with('empleado:id,nombre')
                    ->where(function (Builder $query): void {
                        $query->where('tardanza', true)
                            ->orWhere('salida_temprana', true)
                            ->orWhere('salida_no_registrada', true);
                    })
                    ->orderByDesc('fecha')->limit(12)->get(),
            ],
            'sistema' => [
                'total' => (clone $alertas)->count(),
                'pendientes' => (clone $alertas)->where('estado', 'pendiente')->count(),
                'porTipo' => $alertasPorTipo,
                'recientes' => (clone $alertas)->latest()->limit(10)->get(),
            ],
        ];
    }
}
