<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use App\Models\TipoMembresia;
use App\Services\MembresiaPeriodService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MembresiaClienteController extends Controller
{
    public function index(Request $request, MembresiaPeriodService $periodService): View|string
    {
        $availableStates = ['todas', 'activas', 'recien_compradas', 'por_vencer', 'vencidas'];
        $filters = $request->validate([
            'estado' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($availableStates): void {
                    $requestedStates = preg_split('/\s+/', trim($value)) ?: [];

                    foreach (array_filter($requestedStates) as $requestedState) {
                        if (! in_array($requestedState, $availableStates, true)) {
                            $fail('El filtro de estado seleccionado no es válido.');

                            return;
                        }
                    }
                },
            ],
            'tipo_membresia' => ['nullable', 'integer', Rule::exists('tipos_membresia', 'id')->where('estado', 'activo')],
            'fecha_desde' => ['nullable', 'date_format:Y-m-d'],
            'fecha_hasta' => ['nullable', 'date_format:Y-m-d'],
            'busqueda' => ['nullable', 'string', 'max:150'],
            'vista' => ['nullable', Rule::in(['tabla', 'cards'])],
        ]);

        $today = Carbon::now();

        // KPIs (clientes únicos)
        $totalMembresias = Membresia::distinct('cliente_id')->count('cliente_id');
        $activas = Membresia::where('estado', 'activa')
            ->where('fecha_inicio', '<=', $today)
            ->where('fecha_vencimiento', '>=', $today)
            ->distinct('cliente_id')
            ->count('cliente_id');
        $recienCompradas = Membresia::where('estado', 'activa')
            ->where('fecha_inicio', '>=', $today->copy()->subDays(7))
            ->where('fecha_inicio', '<=', $today)
            ->where('fecha_vencimiento', '>=', $today)
            ->distinct('cliente_id')
            ->count('cliente_id');
        $porVencer = Membresia::where('estado', 'activa')
            ->where('fecha_inicio', '<=', $today)
            ->whereDate('fecha_vencimiento', '>=', $today->toDateString())
            ->whereDate('fecha_vencimiento', '<=', $today->copy()->addDays(7)->toDateString())
            ->distinct('cliente_id')
            ->count('cliente_id');
        $vencidas = Membresia::where(function ($q) use ($today) {
            $q->where('estado', 'vencida')
                ->orWhere(function ($q2) use ($today) {
                    $q2->where('estado', 'activa')
                        ->where('fecha_vencimiento', '<', $today);
                });
        })
            ->whereNotIn('cliente_id', function ($sub) use ($today) {
                $sub->select('cliente_id')
                    ->from('membresias')
                    ->where('estado', 'activa')
                    ->where('fecha_vencimiento', '>=', $today)
                    ->whereNull('deleted_at');
            })
            ->distinct('cliente_id')
            ->count('cliente_id');

        // Traer todas las membresías y quedarnos con la más relevante por cliente:
        // Prioridad 0: Activa actualmente (ya inició y no ha vencido)
        // Prioridad 1: Activa en el futuro (aún no inicia)
        // Prioridad 2: Vencidas o inactivas
        $membresias = Membresia::with(['cliente', 'tipoMembresia'])
            ->matchingOverviewFilters($filters, $today)
            ->orderByRaw("
                CASE 
                    WHEN estado = 'activa' AND fecha_inicio <= ? AND fecha_vencimiento >= ? THEN 0 
                    WHEN estado = 'activa' AND fecha_inicio > ? THEN 1 
                    ELSE 2 
                END
            ", [$today, $today, $today])
            ->orderBy('fecha_vencimiento', 'desc')
            ->get()
            ->unique('cliente_id')
            ->values()
            ->sortBy(function ($m) use ($today) {
                // 0 = Activa ahora, 1 = Futura, 2 = Vencida
                $isActivaAhora = $m->estado === 'activa' && $m->fecha_inicio <= $today && $m->fecha_vencimiento >= $today;
                $isFutura = $m->estado === 'activa' && $m->fecha_inicio > $today;

                if ($isActivaAhora) {
                    return [0, $m->fecha_vencimiento->timestamp]; // Activas: ordenadas por las que vencen más pronto
                } elseif ($isFutura) {
                    return [1, $m->fecha_inicio->timestamp]; // Futuras: ordenadas por las que inician más pronto
                } else {
                    return [2, -$m->fecha_vencimiento->timestamp]; // Vencidas: ordenadas por las más recientes primero
                }
            })
            ->values();

        $periodosPorCliente = Membresia::query()
            ->whereIn('cliente_id', $membresias->pluck('cliente_id')->unique())
            ->whereIn('estado', ['activa', 'vencida'])
            ->get(['cliente_id', 'fecha_inicio', 'fecha_vencimiento', 'estado'])
            ->groupBy('cliente_id');

        $membresias = $membresias->map(function (Membresia $membresia) use ($periodService, $periodosPorCliente): Membresia {
            $periodoAcumulado = $periodService->accumulatedPeriod(
                $membresia,
                $periodosPorCliente->get($membresia->cliente_id, collect())
            );

            $membresia->setAttribute('fecha_inicio_acumulada', $periodoAcumulado['fecha_inicio']);
            $membresia->setAttribute('fecha_vencimiento_acumulada', $periodoAcumulado['fecha_vencimiento']);

            return $membresia;
        });

        $requestedStates = preg_split('/\s+/', trim((string) ($filters['estado'] ?? 'todas'))) ?: [];

        if (count($requestedStates) === 1 && $requestedStates[0] === 'por_vencer') {
            $fechaLimite = $today->copy()->addDays(7)->endOfDay();

            $membresias = $membresias
                ->filter(fn (Membresia $membresia): bool => $membresia->fecha_vencimiento_acumulada->between($today, $fechaLimite))
                ->values();
        }

        // Tipos de membresía para el filtro
        $tiposMembresia = TipoMembresia::where('estado', 'activo')->get();

        // Vista (tabla o cards)
        $vista = $filters['vista'] ?? 'tabla';

        return view('membresias-clientes.index', compact(
            'membresias',
            'totalMembresias',
            'activas',
            'recienCompradas',
            'porVencer',
            'vencidas',
            'tiposMembresia',
            'filters',
            'vista'
        ))->fragmentIf(
            $request->header('X-Fragment-Name') === 'membership-results',
            'membership-results'
        );
    }
}
