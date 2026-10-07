<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\Pago;
use App\Models\PaseDiario;
use App\Models\TipoMembresia;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['cliente', 'empleado', 'membresia.tipoMembresia'])
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $totalIngresos = Pago::where('estado', 'pagado')->sum('monto');
        $pagosHoy = Pago::where('estado', 'pagado')->whereDate('fecha_pago', Carbon::today())->sum('monto');
        $pagosMes = Pago::where('estado', 'pagado')->whereYear('fecha_pago', Carbon::now()->year)
            ->whereMonth('fecha_pago', Carbon::now()->month)
            ->sum('monto');

        // Para el modal de selección de clientes
        $clientes = Cliente::with(['membresias' => function ($q) {
            $q->where('estado', 'activa')
                ->where('fecha_inicio', '<=', Carbon::now())
                ->where('fecha_vencimiento', '>=', Carbon::now())
                ->latest();
        }])->orderBy('nombre')->get();

        $tiposMembresia = TipoMembresia::where('estado', 'activo')->orderBy('precio')->get();

        return view('pagos.index', compact('pagos', 'totalIngresos', 'pagosHoy', 'pagosMes', 'clientes', 'tiposMembresia'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::where('estado', 'activo')->orderBy('nombre')->get();
        $tiposMembresia = TipoMembresia::where('estado', 'activo')->orderBy('precio')->get();
        $clienteSeleccionado = $request->cliente_id ?? null;

        return view('pagos.create', compact('clientes', 'tiposMembresia', 'clienteSeleccionado'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_pago' => 'required|in:membresia,pase_diario',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto' => 'required|numeric|min:1',
            'tipo_membresia_id' => 'required_if:tipo_pago,membresia|nullable|exists:tipos_membresia,id',
            'concepto' => 'required|string|max:255',
            'fecha_inicio' => 'required_if:tipo_pago,membresia|nullable|date_format:Y-m-d|after_or_equal:today',
            'fecha_pago' => 'required|date_format:Y-m-d\TH:i|before_or_equal:now',
        ]);

        $empleadoId = (int) $request->user()->getAuthIdentifier();
        $cliente = Cliente::findOrFail($validated['cliente_id']);
        $tipoMembresia = $validated['tipo_pago'] === 'membresia'
            ? TipoMembresia::where('estado', 'activo')->findOrFail($validated['tipo_membresia_id'])
            : null;
        $monto = $tipoMembresia ? (float) $tipoMembresia->precio : (float) $validated['monto'];

        if ($tipoMembresia && round((float) $validated['monto'], 2) !== round($monto, 2)) {
            return back()->withErrors(['monto' => 'El monto debe coincidir con el precio vigente del plan.'])->withInput();
        }

        $pago = DB::transaction(function () use ($validated, $cliente, $empleadoId, $tipoMembresia, $monto): Pago {
            $membresia = null;

            if ($tipoMembresia) {
                $fechaInicio = Carbon::parse($validated['fecha_inicio'])->startOfDay();
                $membresiaSolapada = Membresia::where('cliente_id', $cliente->id)
                    ->where('estado', 'activa')
                    ->where('fecha_vencimiento', '>=', $fechaInicio)
                    ->lockForUpdate()
                    ->exists();

                if ($membresiaSolapada) {
                    throw ValidationException::withMessages([
                        'fecha_inicio' => 'La nueva membresía debe comenzar después del vencimiento de la membresía vigente o programada.',
                    ]);
                }

                $fechaVencimiento = $fechaInicio->copy()->addDays($tipoMembresia->duracion_dias - 1)->endOfDay();

                $membresia = Membresia::create([
                    'cliente_id' => $cliente->id,
                    'tipo_membresia_id' => $tipoMembresia->id,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado' => 'activa',
                ]);

                $cliente->update(['estado' => 'activo', 'ultima_actividad' => now()]);
            }

            $pago = Pago::create([
                'cliente_id' => $cliente->id,
                'empleado_id' => $empleadoId,
                'membresia_id' => $membresia?->id,
                'tipo_pago' => $validated['tipo_pago'],
                'metodo_pago' => $validated['metodo_pago'],
                'monto' => $monto,
                'concepto' => $validated['concepto'],
                'estado' => 'pagado',
                'fecha_pago' => Carbon::createFromFormat('Y-m-d\\TH:i', $validated['fecha_pago']),
            ]);

            if (! $tipoMembresia) {
                PaseDiario::create([
                    'cliente_id' => $cliente->id,
                    'pago_id' => $pago->id,
                    'fecha' => Carbon::createFromFormat('Y-m-d\\TH:i', $validated['fecha_pago'])->toDateString(),
                    'otorga_asistencia' => false,
                ]);
            }

            return $pago;
        });

        return redirect()->route('pagos.ticket', $pago)->with('success', 'Pago procesado exitosamente.');
    }

    public function ticket(Pago $pago): View
    {
        $pago->load([
            'cliente:id,nombre,cedula,telefono',
            'empleado:id,nombre',
            'membresia.tipoMembresia:id,nombre,duracion_dias',
        ]);

        return view('pagos.ticket', compact('pago'));
    }

    /** AJAX: devuelve info de membresía del cliente seleccionado */
    public function clienteInfo(Cliente $cliente)
    {
        $membresia = $cliente->membresias()
            ->with('tipoMembresia')
            ->latest('fecha_inicio')
            ->first();

        $data = [
            'estado_cliente' => $cliente->estado,
            'membresia' => null,
        ];

        if ($membresia) {
            $vencida = $membresia->fecha_vencimiento < now()->toDateString();
            $diasRestantes = now()->diffInDays($membresia->fecha_vencimiento, false);

            $data['membresia'] = [
                'plan' => $membresia->tipoMembresia->nombre ?? 'Desconocido',
                'estado' => $membresia->estado,
                'fecha_vencimiento' => Carbon::parse($membresia->fecha_vencimiento)->format('d/m/Y'),
                'fecha_vencimiento_raw' => $membresia->fecha_vencimiento,
                'dias_restantes' => (int) $diasRestantes,
                'vencida' => $vencida,
                'tiene_activa' => ! $vencida && $membresia->estado === 'activa',
            ];
        }

        return response()->json($data);
    }

    /** AJAX: devuelve clientes buscados para Select2 */
    public function buscarClientes(Request $request)
    {
        $term = $request->input('q');

        $query = Cliente::with(['membresias' => function ($q) {
            $q->where('estado', 'activa')
                ->where('fecha_inicio', '<=', Carbon::now())
                ->where('fecha_vencimiento', '>=', Carbon::now())
                ->latest();
        }]);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'LIKE', '%'.$term.'%')
                    ->orWhere('apellido', 'LIKE', '%'.$term.'%')
                    ->orWhere('email', 'LIKE', '%'.$term.'%')
                    ->orWhere('cedula', 'LIKE', '%'.$term.'%');
            });
        }

        $clientes = $query->limit(20)->get();

        $resultados = [];
        foreach ($clientes as $c) {
            $membActiva = $c->membresias->first();

            if ($membActiva) {
                $membStatus = 'ACTIVA';
            } else {
                $tieneVencida = $c->membresias()->where('estado', 'vencida')->exists();
                $membStatus = $tieneVencida ? 'VENCIDA' : 'SIN MEMBRESÍA';
            }

            $resultados[] = [
                'id' => $c->id,
                'text' => $c->nombre_completo,
                'cedula' => $c->cedula,
                'estado' => $c->estado,
                'membresia_status' => $membStatus,
                'foto' => $c->foto_referencia ? asset('storage/'.$c->foto_referencia) : null,
            ];
        }

        return response()->json(['results' => $resultados]);
    }
}
