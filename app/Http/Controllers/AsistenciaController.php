<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\AsistenciaCliente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{

/*
    public function escanear()
    {
        // Traer solo clientes activos que tengan un descriptor facial configurado
        $clientes = Cliente::whereNotNull('descriptor_facial')
                           ->where('estado', 'activo')
                           ->get(['id', 'nombre', 'descriptor_facial', 'estado']);

        return view('asistencias.escanear', compact('clientes'));

    }

    */



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
            'cliente_id' => 'required|exists:clientes,id'
        ]);

        $cliente = Cliente::with(['membresias' => function($q) {
            $q->where('estado', 'activa')->latest();
        }])->findOrFail($request->cliente_id);

        if ($cliente->estado !== 'activo') {
            return response()->json([
                'status' => 'warning',
                'message' => 'El cliente está inactivo.',
                'cliente' => $cliente->nombre,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'foto' => $cliente->foto_referencia ? asset('storage/' . $cliente->foto_referencia) : null
            ]);
        }

        $membresiaActiva = $cliente->membresias->first();
        if (!$membresiaActiva) {
            return response()->json([
                'status' => 'warning',
                'message' => 'El cliente no tiene una membresía activa.',
                'cliente' => $cliente->nombre,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'foto' => $cliente->foto_referencia ? asset('storage/' . $cliente->foto_referencia) : null
            ]);
        }

        // Evitar doble escaneo en los últimos 30 minutos
        $ultimaAsistencia = AsistenciaCliente::where('cliente_id', $cliente->id)
            ->where('fecha', date('Y-m-d'))
            ->where('hora', '>=', Carbon::now()->subMinutes(30)->format('H:i:s'))
            ->first();

        if ($ultimaAsistencia) {
             return response()->json([
                'status' => 'warning',
                'message' => 'Asistencia ya registrada hace unos momentos.',
                'cliente' => $cliente->nombre,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'membresia_vence' => Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y'),
                'foto' => $cliente->foto_referencia ? asset('storage/' . $cliente->foto_referencia) : null
            ]);
        }

        // Puntos otorgados (boolean según DB)
        // Regla: 1 asistencia = 1 punto? La tabla dice booleano, pero los puntos_recompensa son un contador.
        // Voy a asmir que sí se otorgaron puntos
        $asistencia = AsistenciaCliente::create([
            'cliente_id' => $cliente->id,
            'empleado_valida_id' => auth()->id() ?? null,
            'fecha' => date('Y-m-d'),
            'hora' => date('H:i:s'),
            'metodo_registro' => 'facial',
            'puntos_otorgados' => true
        ]);

                $cliente->increment('puntos_ecogim', 1);
                $cliente->refresh();

        // Crear registro del movimiento de puntos
        \App\Models\MovimientoPunto::create([
            'cliente_id' => $cliente->id,
            'tipo_movimiento' => 'ganado',
            'puntos' => 1,
            'origen_tabla' => 'asistencias_clientes',
            'origen_id' => $asistencia->id,
            'fecha' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '¡Bienvenido! Asistencia registrada exitosamente.',
            'cliente' => $cliente->nombre,
            'membresia_vence' => Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y'),
            'puntos' => $cliente->puntos_ecogim,
            'foto' => $cliente->foto_referencia ? asset('storage/' . $cliente->foto_referencia) : null
        ]);
    }
}