<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Membresia;
use App\Models\Pago;
use App\Models\TipoMembresia;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['cliente', 'empleado', 'membresia.tipoMembresia'])
            ->orderBy('fecha_pago', 'desc')
            ->get();

        $totalIngresos = $pagos->sum('monto');
        $pagosHoy = Pago::whereDate('fecha_pago', Carbon::today())->sum('monto');

        // Para el modal de selección de clientes
        $clientes = Cliente::with(['membresias' => function ($q) {
            $q->where('estado', 'activa')->where('fecha_vencimiento', '>=', Carbon::today())->latest();
        }])->orderBy('nombre')->get();

        $tiposMembresia = TipoMembresia::where('estado', 'activo')->orderBy('precio')->get();

        return view('pagos.index', compact('pagos', 'totalIngresos', 'pagosHoy', 'clientes', 'tiposMembresia'));
    }

    public function create(Request $request)
    {
        $clientes = Cliente::where('estado', 'activo')->orderBy('nombre')->get();
        $tiposMembresia = TipoMembresia::where('estado', 'activo')->orderBy('precio')->get();
        $clienteSeleccionado = $request->cliente_id ?? null;

        return view('pagos.create', compact('clientes', 'tiposMembresia', 'clienteSeleccionado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_pago' => 'required|in:membresia,pase_diario',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'monto' => 'required|numeric|min:1',
            'tipo_membresia_id' => 'required_if:tipo_pago,membresia|nullable|exists:tipos_membresia,id',
        ]);

        $empleado = Empleado::first();
        $empleado_id = $empleado ? $empleado->id : 1;

        $membresia_id = null;

        if ($request->tipo_pago === 'membresia') {
            $tipoMembresia = TipoMembresia::findOrFail($request->tipo_membresia_id);
            $fechaInicio = Carbon::today();
            $fechaVencimiento = $fechaInicio->copy()->addDays($tipoMembresia->duracion_dias ?? 30);

            // Marcar las membresías anteriores como vencidas
            Membresia::where('cliente_id', $request->cliente_id)
                ->where('estado', 'activa')
                ->update(['estado' => 'vencida']);

            // Crear nueva membresía
            $membresia = Membresia::create([
                'cliente_id' => $request->cliente_id,
                'tipo_membresia_id' => $tipoMembresia->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => 'activa',
            ]);

            $membresia_id = $membresia->id;

            // Activar el cliente inmediatamente
            Cliente::where('id', $request->cliente_id)->update(['estado' => 'activo']);
        }

        Pago::create([
            'cliente_id' => $request->cliente_id,
            'empleado_id' => $empleado_id,
            'membresia_id' => $membresia_id,
            'tipo_pago' => $request->tipo_pago,
            'metodo_pago' => $request->metodo_pago,
            'monto' => $request->monto,
            'fecha_pago' => Carbon::now(),
        ]);

        return redirect()->route('pagos.index')->with('success', 'Pago procesado exitosamente.');
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
                'dias_restantes' => (int) $diasRestantes,
                'vencida' => $vencida,
            ];
        }

        return response()->json($data);
    }
}
