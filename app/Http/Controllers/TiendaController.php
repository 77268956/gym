<?php

namespace App\Http\Controllers;

use App\Models\CanjeEcogim;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\MovimientoPunto;
use App\Models\ProductoEcogim;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiendaController extends Controller
{
    public function index()
    {
        $productos = ProductoEcogim::where('estado', 'activo')
            ->where('stock', '>', 0)
            ->orderBy('categoria')
            ->orderBy('nombre')
            ->get();

        $categorias = $productos->pluck('categoria')->filter()->unique()->values();

        return view('tienda.index', compact('productos', 'categorias'));
    }

    /** AJAX: info del cliente para el modal de canje */
    public function clienteInfo(Cliente $cliente)
    {
        $membresiaActiva = $cliente->membresias()
            ->where('estado', 'activa')
            ->where('fecha_vencimiento', '>=', Carbon::today())
            ->latest()
            ->first();

        $periodoActual = Carbon::now()->format('Y-m');
        $yaCanjeEesteMes = CanjeEcogim::where('cliente_id', $cliente->id)
            ->where('periodo_canje', $periodoActual)
            ->exists();

        return response()->json([
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'cedula' => $cliente->cedula,
            'estado' => $cliente->estado,
            'puntos' => $cliente->puntos_ecogim,
            'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
            'tiene_membresia_activa' => (bool) $membresiaActiva,
            'membresia_vence' => $membresiaActiva ? Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y') : null,
            'ya_canje_este_mes' => $yaCanjeEesteMes,
        ]);
    }

    /** AJAX: buscar clientes para Select2 */
    public function buscarClientes(Request $request)
    {
        $term = $request->input('q', '');

        $query = Cliente::where('estado', 'activo')->with(['membresias' => function ($q) {
            $q->where('estado', 'activa')->where('fecha_vencimiento', '>=', Carbon::today())->latest();
        }]);

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'LIKE', '%' . $term . '%')
                    ->orWhere('cedula', 'LIKE', '%' . $term . '%');
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
                'text' => $c->nombre,
                'cedula' => $c->cedula,
                'puntos' => $c->puntos_ecogim,
                'estado' => $c->estado,
                'membresia_status' => $membStatus,
                'foto' => $c->foto_referencia ? asset('storage/'.$c->foto_referencia) : null,
            ];
        }

        return response()->json(['results' => $resultados]);
    }

    /** Procesa el canje de un producto */
    public function canjear(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'required|exists:productos_ecogim,id',
        ]);

        $cliente = Cliente::findOrFail($request->cliente_id);
        $producto = ProductoEcogim::findOrFail($request->producto_id);
        $periodoActual = Carbon::now()->format('Y-m');

        if ($cliente->estado !== 'activo') {
            return response()->json(['ok' => false, 'mensaje' => 'El cliente no está activo.'], 422);
        }

        $tieneMembresiaActiva = $cliente->membresias()
            ->where('estado', 'activa')
            ->where('fecha_vencimiento', '>=', Carbon::today())
            ->exists();

        if (! $tieneMembresiaActiva) {
            return response()->json(['ok' => false, 'mensaje' => 'El cliente no tiene una membresía activa y vigente.'], 422);
        }

        $yaCanjeEesteMes = CanjeEcogim::where('cliente_id', $cliente->id)
            ->where('periodo_canje', $periodoActual)
            ->exists();

        if ($yaCanjeEesteMes) {
            return response()->json(['ok' => false, 'mensaje' => 'Este cliente ya realizó un canje este mes. Podrá canjear el próximo mes.'], 422);
        }

        if ($producto->estado !== 'activo' || $producto->stock <= 0) {
            return response()->json(['ok' => false, 'mensaje' => 'El producto no está disponible o no tiene stock.'], 422);
        }

        if ($cliente->puntos_ecogim < $producto->puntos_valor) {
            return response()->json([
                'ok' => false,
                'mensaje' => "Puntos insuficientes. Necesita {$producto->puntos_valor} pts y el cliente tiene {$cliente->puntos_ecogim} pts.",
            ], 422);
        }

        DB::transaction(function () use ($cliente, $producto, $periodoActual) {
            $empleado = Empleado::first();

            $canje = CanjeEcogim::create([
                'cliente_id' => $cliente->id,
                'producto_id' => $producto->id,
                'empleado_id' => $empleado?->id ?? 1,
                'puntos_utilizados' => $producto->puntos_valor,
                'periodo_canje' => $periodoActual,
                'fecha' => now(),
            ]);

            $cliente->decrement('puntos_ecogim', $producto->puntos_valor);
            $producto->decrement('stock');

            MovimientoPunto::create([
                'cliente_id' => $cliente->id,
                'tipo_movimiento' => 'canjeado',
                'puntos' => $producto->puntos_valor,
                'origen_tabla' => 'canjes_ecogim',
                'origen_id' => $canje->id,
                'fecha' => now(),
            ]);
        });

        $cliente->refresh();

        return response()->json([
            'ok' => true,
            'mensaje' => "¡Canje exitoso! Se descontaron {$producto->puntos_valor} puntos a {$cliente->nombre}.",
            'puntos_restantes' => $cliente->puntos_ecogim,
        ]);
    }
}
