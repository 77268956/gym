<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaEmpleado;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsistenciaEmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $hoy = Carbon::today();

        // ── KPIs (siempre del día de hoy) ───────────────────────────────
        $asistenciasHoy = AsistenciaEmpleado::whereDate('fecha', $hoy)
            ->whereNotNull('hora_entrada')->count();
        $tardanzasHoy = AsistenciaEmpleado::whereDate('fecha', $hoy)
            ->where('tardanza', true)->count();
        $salidasTempranasHoy = AsistenciaEmpleado::whereDate('fecha', $hoy)
            ->where('salida_temprana', true)->count();
        $ausenciasHoy = AsistenciaEmpleado::whereDate('fecha', $hoy)
            ->where('metodo_registro', 'sistema')
            ->whereNull('hora_entrada')->count();

        // ── Filtros ─────────────────────────────────────────────────────
        $empleados = Empleado::orderBy('nombre')->get(['id', 'nombre']);

        $query = AsistenciaEmpleado::with('empleado');

        if ($request->filled('empleado_id')) {
            $query->where('empleado_id', $request->empleado_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('estado')) {
            match ($request->estado) {
                'ausente' => $query->where('metodo_registro', 'sistema')->whereNull('hora_entrada'),
                'tardanza' => $query->where('tardanza', true),
                'salida_temp' => $query->where('salida_temprana', true),
                'cerrado_auto' => $query->where('salida_no_registrada', true)->whereNotNull('hora_entrada'),
                'en_turno' => $query->whereNotNull('hora_entrada')->whereNull('hora_salida'),
                'completo' => $query->whereNotNull('hora_entrada')->whereNotNull('hora_salida')->where('salida_no_registrada', false),
                default => null,
            };
        }

        $asistencias = $query
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_entrada', 'desc')
            ->get();

        return view('asistencias_empleados.index', compact(
            'asistencias',
            'empleados',
            'asistenciasHoy',
            'tardanzasHoy',
            'salidasTempranasHoy',
            'ausenciasHoy'
        ));
    }

    public function escanear()
    {
        $empleados = Empleado::whereNotNull('descriptor_facial')
            ->where('estado', 'activo')
            ->get(['id', 'nombre', 'descriptor_facial', 'rol']);

        return view('asistencias_empleados.escanear', compact('empleados'));
    }

    public function registrarEscaneo(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
        ]);

        $empleado = Empleado::findOrFail($request->empleado_id);
        $hoy = Carbon::today();
        $ahora = Carbon::now();

        $asistencia = AsistenciaEmpleado::where('empleado_id', $empleado->id)
            ->whereDate('fecha', $hoy)
            ->first();

        if (! $asistencia) {
            $tolerancia = $empleado->tolerancia_minutos ?? 10;
            $horaEntradaTurno = $empleado->hora_entrada_turno
                ? Carbon::parse($hoy->format('Y-m-d').' '.$empleado->hora_entrada_turno)
                : null;

            $tardanza = false;
            if ($horaEntradaTurno && $ahora->copy()->subMinutes($tolerancia)->isAfter($horaEntradaTurno)) {
                $tardanza = true;
            }

            AsistenciaEmpleado::create([
                'empleado_id' => $empleado->id,
                'fecha' => $hoy->format('Y-m-d'),
                'hora_entrada' => $ahora->format('H:i:s'),
                'tardanza' => $tardanza,
                'metodo_registro' => 'facial',
            ]);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'tipo' => 'entrada',
                'tipo_registro' => 'entrada',
                'empleado' => $empleado->nombre,
                'tardanza' => $tardanza,
                'hora' => $ahora->format('H:i:s'),
                'foto' => $empleado->foto_referencia ? asset('storage/'.$empleado->foto_referencia) : null,
                'message' => 'Entrada registrada correctamente.',
            ]);
        }

        if ($asistencia && ! $asistencia->hora_salida) {
            $horaSalidaTurno = $empleado->hora_salida_turno
                ? Carbon::parse($hoy->format('Y-m-d').' '.$empleado->hora_salida_turno)
                : null;

            $salidaTemprana = false;
            if ($horaSalidaTurno && $ahora->isBefore($horaSalidaTurno)) {
                $salidaTemprana = true;
            }

            $horaEntrada = Carbon::parse((string) $asistencia->hora_entrada)->format('H:i:s');
            $entrada = Carbon::parse($asistencia->fecha->format('Y-m-d').' '.$horaEntrada);
            $horasTrabajadas = $entrada->diffInMinutes($ahora) / 60;

            $asistencia->update([
                'hora_salida' => $ahora->format('H:i:s'),
                'salida_temprana' => $salidaTemprana,
                'horas_trabajadas' => $horasTrabajadas,
            ]);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'tipo' => 'salida',
                'tipo_registro' => 'salida',
                'empleado' => $empleado->nombre,
                'salida_temprana' => $salidaTemprana,
                'hora' => $ahora->format('H:i:s'),
                'foto' => $empleado->foto_referencia ? asset('storage/'.$empleado->foto_referencia) : null,
                'message' => 'Salida registrada correctamente.',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'success' => true,
            'tipo' => 'completo',
            'tipo_registro' => 'completo',
            'empleado' => $empleado->nombre,
            'mensaje' => 'Ya completaste tu turno hoy.',
            'message' => 'Ya completaste tu turno hoy.',
            'hora' => $ahora->format('H:i:s'),
            'foto' => $empleado->foto_referencia ? asset('storage/'.$empleado->foto_referencia) : null,
        ]);
    }
}
