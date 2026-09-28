<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaCliente;
use App\Models\Cliente;
use App\Models\ConfiguracionPunto;
use App\Models\MovimientoPunto;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        // KPIs (Hoy)
        $hoy = Carbon::today();
        $accesosHoy = AsistenciaCliente::whereDate('fecha', $hoy)->count();
        $exitososHoy = AsistenciaCliente::whereDate('fecha', $hoy)->where('exitoso', true)->count();
        $fallidosHoy = $accesosHoy - $exitososHoy;

        // Obtener todos los registros (DataTables se encarga de paginar)
        $asistencias = AsistenciaCliente::with(['cliente' => function ($q) {
            $q->withTrashed();
        }])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        return view('asistencias.index', compact(
            'asistencias',
            'accesosHoy',
            'exitososHoy',
            'fallidosHoy'
        ));
    }

    public function escanear()
    {
        // Cargar todos los clientes que tengan descriptor facial grabado
        $clientes = Cliente::whereNotNull('descriptor_facial')
            ->get(['id', 'nombre', 'descriptor_facial', 'estado']);

        return view('asistencias.escanear', compact('clientes'));
    }

    public function registrarEscaneo(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        $cliente = Cliente::with([
            'membresias' => function ($q) {
                $q->where('estado', 'activa')
                    ->where('fecha_inicio', '<=', Carbon::today())
                    ->where('fecha_vencimiento', '>=', Carbon::today())
                    ->latest();
            },
        ])->findOrFail($request->cliente_id);

        if ($cliente->estado !== 'activo') {
            AsistenciaCliente::create([
                'cliente_id' => $cliente->id,
                'empleado_valida_id' => auth()->id() ?? null,
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'metodo_registro' => 'facial',
                'exitoso' => false,
                'motivo_rechazo' => 'Cliente inactivo',
                'puntos_otorgados' => false,
            ]);

            return response()->json([
                'status' => 'warning',
                'message' => 'El cliente está inactivo.',
                'cliente' => $cliente->nombre,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
            ]);
        }

        $membresiaActiva = $cliente->membresias->first();
        if (! $membresiaActiva) {
            AsistenciaCliente::create([
                'cliente_id' => $cliente->id,
                'empleado_valida_id' => auth()->id() ?? null,
                'fecha' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'metodo_registro' => 'facial',
                'exitoso' => false,
                'motivo_rechazo' => 'Sin membresía activa',
                'puntos_otorgados' => false,
            ]);

            return response()->json([
                'status' => 'warning',
                'message' => 'El cliente no tiene una membresía activa.',
                'cliente' => $cliente->nombre,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
            ]);
        }

        // Evitar duplicar asistencias exitosas durante el mismo día.
        $ultimaAsistencia = AsistenciaCliente::where('cliente_id', $cliente->id)
            ->where('fecha', date('Y-m-d'))
            ->where('exitoso', true)
            ->first();

        if ($ultimaAsistencia) {
            return response()->json([
                'status' => 'success',
                'message' => '¡Bienvenido! Tu asistencia ya estaba registrada hoy.',
                'cliente' => $cliente->nombre,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'membresia_vence' => Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y'),
                'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
            ]);
        }

        // Verificar si ya se le otorgaron puntos hoy
        $yaObtuvoPuntosHoy = AsistenciaCliente::where('cliente_id', $cliente->id)
            ->where('fecha', date('Y-m-d'))
            ->where('puntos_otorgados', true)
            ->exists();

        // Obtener la cantidad de puntos configurada
        $config = ConfiguracionPunto::first();
        $puntosAGanar = $config ? $config->puntos_por_visita : 0;

        $otorgarPuntos = ! $yaObtuvoPuntosHoy && $puntosAGanar > 0;

        $asistencia = AsistenciaCliente::create([
            'cliente_id' => $cliente->id,
            'empleado_valida_id' => auth()->id() ?? null,
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'metodo_registro' => 'facial',
            'exitoso' => true,
            'puntos_otorgados' => $otorgarPuntos,
        ]);

        if ($otorgarPuntos) {
            $cliente->increment('puntos_ecogim', $puntosAGanar);
            $cliente->refresh();

            // Crear registro del movimiento de puntos
            MovimientoPunto::create([
                'cliente_id' => $cliente->id,
                'tipo_movimiento' => 'ganado',
                'puntos' => $puntosAGanar,
                'origen_tabla' => 'asistencias_clientes',
                'origen_id' => $asistencia->id,
                'fecha' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => '¡Bienvenido! Asistencia registrada exitosamente.',
            'puntos' => $cliente->puntos_ecogim,
            'cliente' => $cliente->nombre,
            'membresia_vence' => Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y'),
            'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
        ]);
    }
}
