<?php

namespace App\Http\Controllers;

use App\Models\AlertaSistema;
use App\Models\AsistenciaCliente;
use App\Models\Cliente;
use App\Models\ConfiguracionPunto;
use App\Models\IntentoEscaner;
use App\Models\MovimientoPunto;
use App\Models\PaseDiario;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function registrarIntentoDesconocido(): JsonResponse
    {
        $intento = IntentoEscaner::create();

        if (IntentoEscaner::where('created_at', '>=', now()->subMinute())->count() === 6) {
            AlertaSistema::registrar(
                'intentos_escaner',
                'intentos_escaner',
                $intento->id,
                'Se detectaron más de 5 intentos fallidos de reconocimiento facial en un minuto.'
            );
        }

        return response()->json(['status' => 'recorded']);
    }

    public function index(Request $request)
    {
        // KPIs (Hoy)
        $hoy = Carbon::now();
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

        $fechaActual = now()->toDateString();
        $horaActual = now()->format('H:i:s');

        $cliente = Cliente::with([
            'membresias' => function ($q) {
                $q->where('estado', 'activa')
                    ->where('fecha_inicio', '<=', Carbon::now())
                    ->where('fecha_vencimiento', '>=', Carbon::now())
                    ->latest();
            },
        ])->findOrFail($request->cliente_id);

        if ($cliente->estado !== 'activo') {
            $intento = AsistenciaCliente::create([
                'cliente_id' => $cliente->id,
                'empleado_valida_id' => auth()->id() ?? null,
                'fecha' => $fechaActual,
                'hora' => $horaActual,
                'metodo_registro' => 'facial',
                'exitoso' => false,
                'motivo_rechazo' => 'Cliente inactivo',
                'puntos_otorgados' => false,
            ]);
            $this->alertarIntentosFallidos($cliente, $intento);

            return response()->json([
                'status' => 'warning',
                'message' => 'El cliente está inactivo.',
                'cliente' => $cliente->nombre_completo,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
            ]);
        }

        $membresiaActiva = $cliente->membresias->first();
        $paseDiarioValido = PaseDiario::where('cliente_id', $cliente->id)
            ->whereDate('fecha', today())
            ->whereHas('pago', fn ($query) => $query->where('estado', 'pagado'))
            ->exists();

        if (! $membresiaActiva && ! $paseDiarioValido) {
            $intento = AsistenciaCliente::create([
                'cliente_id' => $cliente->id,
                'empleado_valida_id' => auth()->id() ?? null,
                'fecha' => $fechaActual,
                'hora' => $horaActual,
                'metodo_registro' => 'facial',
                'exitoso' => false,
                'motivo_rechazo' => 'Sin membresía activa',
                'puntos_otorgados' => false,
            ]);
            $this->alertarIntentosFallidos($cliente, $intento);

            return response()->json([
                'status' => 'warning',
                'message' => 'El cliente no tiene una membresía activa.',
                'cliente' => $cliente->nombre_completo,
                'puntos' => $cliente->puntos_ecogim ?? 0,
                'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
            ]);
        }

        return DB::transaction(function () use ($cliente, $membresiaActiva, $fechaActual, $horaActual): JsonResponse {
            $cliente = Cliente::whereKey($cliente->id)->lockForUpdate()->firstOrFail();

            $ultimaAsistencia = AsistenciaCliente::where('cliente_id', $cliente->id)
                ->whereDate('fecha', $fechaActual)
                ->where('exitoso', true)
                ->first();

            if ($ultimaAsistencia) {
                return response()->json([
                    'status' => 'success',
                    'message' => '¡Bienvenido! Tu asistencia ya estaba registrada hoy.',
                    'cliente' => $cliente->nombre_completo,
                    'puntos' => $cliente->puntos_ecogim ?? 0,
                    'membresia_vence' => $membresiaActiva ? Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y') : null,
                    'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
                ]);
            }

            $yaObtuvoPuntosHoy = AsistenciaCliente::where('cliente_id', $cliente->id)
                ->whereDate('fecha', $fechaActual)
                ->where('puntos_otorgados', true)
                ->exists();

            $config = ConfiguracionPunto::first();
            $puntosAGanar = $config ? $config->puntos_por_visita : 1;

            $otorgarPuntos = ! $yaObtuvoPuntosHoy && $puntosAGanar > 0;

            $asistencia = AsistenciaCliente::create([
                'cliente_id' => $cliente->id,
                'empleado_valida_id' => auth()->id() ?? null,
                'fecha' => $fechaActual,
                'hora' => $horaActual,
                'metodo_registro' => 'facial',
                'exitoso' => true,
                'puntos_otorgados' => $otorgarPuntos,
            ]);

            if ($otorgarPuntos) {
                $cliente->increment('puntos_ecogim', $puntosAGanar);
                $cliente->refresh();

                MovimientoPunto::create([
                    'cliente_id' => $cliente->id,
                    'tipo_movimiento' => 'ganado',
                    'puntos' => $puntosAGanar,
                    'origen_tabla' => 'asistencias_clientes',
                    'origen_id' => $asistencia->id,
                    'fecha' => now(),
                ]);
            }

            $cliente->update(['ultima_actividad' => now(), 'estado' => 'activo']);

            return response()->json([
                'status' => 'success',
                'message' => '¡Bienvenido! Asistencia registrada exitosamente.',
                'puntos' => $cliente->puntos_ecogim,
                'cliente' => $cliente->nombre_completo,
                'membresia_vence' => $membresiaActiva ? Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y') : null,
                'foto' => $cliente->foto_referencia ? asset('storage/'.$cliente->foto_referencia) : null,
            ]);
        });
    }

    private function alertarIntentosFallidos(Cliente $cliente, AsistenciaCliente $intento): void
    {
        $intentosUltimoMinuto = AsistenciaCliente::where('cliente_id', $cliente->id)
            ->where('created_at', '>=', now()->subMinute())
            ->where('exitoso', false)
            ->count();

        if ($intentosUltimoMinuto === 6) {
            AlertaSistema::registrar(
                'intentos_escaner',
                'asistencias_clientes',
                $intento->id,
                "{$cliente->nombre} acumula más de 5 intentos fallidos en el escáner en un minuto."
            );
        }
    }
}
