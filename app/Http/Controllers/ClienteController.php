<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Membresia;
use App\Models\Pago;
use App\Models\TipoMembresia;
use App\Services\MembresiaPeriodService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    public function index()
    {
        // Handled by UserController
    }

    public function create()
    {
        $tiposMembresia = TipoMembresia::where('estado', 'activo')->orderBy('precio')->get();

        return view('clientes.create', [
            'cliente' => null,
            'membresiaActiva' => null,
            'tiposMembresia' => $tiposMembresia,
        ]);
    }

    public function store(StoreClienteRequest $request)
    {
        $validated = $request->validated();

        $fotoPath = null;
        $descriptorFacial = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos_clientes', 'public');
        } elseif ($request->filled('foto_base64')) {
            $base64Image = $request->input('foto_base64');
            if (preg_match('/^data:image\/(jpeg|png|webp);base64,/', $base64Image, $type)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $ext = strtolower($type[1]);
                $fileName = 'fotos_clientes/webcam_'.uniqid().'.'.$ext;
                $decodedImage = base64_decode($data, true);
                if ($decodedImage !== false) {
                    Storage::disk('public')->put($fileName, $decodedImage);
                    $fotoPath = $fileName;
                }
            }
        }

        if ($request->filled('descriptor_facial')) {
            $descriptorFacial = $request->input('descriptor_facial');
        }

        $tipo = TipoMembresia::where('estado', 'activo')->findOrFail($validated['tipo_membresia_id']);
        $fechaInicio = Carbon::parse($validated['fecha_inicio'])->startOfDay();
        $fechaVencimiento = $fechaInicio->copy()->addDays($tipo->duracion_dias - 1)->endOfDay();
        $cliente = DB::transaction(function () use ($validated, $fotoPath, $descriptorFacial, $tipo, $fechaInicio, $fechaVencimiento, $request): Cliente {
            $cliente = Cliente::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'cedula' => $validated['cedula'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'direccion' => $validated['direccion'],
                'foto_referencia' => $fotoPath,
                'descriptor_facial' => $descriptorFacial,
                'historial_medico' => $validated['historial_medico'] ?? null,
                'puntos_ecogim' => 0,
                'estado' => 'activo',
                'ultima_actividad' => now(),
            ]);

            $membresia = Membresia::create([
                'cliente_id' => $cliente->id,
                'tipo_membresia_id' => $tipo->id,
                'fecha_inicio' => $fechaInicio,
                'fecha_vencimiento' => $fechaVencimiento,
                'estado' => 'activa',
            ]);

            Pago::create([
                'cliente_id' => $cliente->id,
                'empleado_id' => $request->user()->getAuthIdentifier(),
                'membresia_id' => $membresia->id,
                'tipo_pago' => 'membresia',
                'metodo_pago' => $validated['metodo_pago'],
                'monto' => $tipo->precio,
                'concepto' => 'Membresía: '.$tipo->nombre,
                'estado' => 'pagado',
                'fecha_pago' => Carbon::now(),
            ]);

            return $cliente;
        });

        return redirect()->route('user')->with('success', "Cliente {$cliente->nombre} registrado con membresía {$tipo->nombre}.");
    }

    public function show(Cliente $cliente, MembresiaPeriodService $periodService)
    {
        $cliente->load([
            'membresias.tipoMembresia',
            'asistencias' => function ($query) {
                $query->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->take(30);
            },
            'asistencias.movimientosPuntos',
            'canjes.producto',
        ]);

        $now = now();
        $membresias = $cliente->membresias;
        $membresiaActiva = $membresias
            ->filter(fn (Membresia $membresia): bool => $membresia->estado === 'activa'
                && $membresia->fecha_inicio->copy()->startOfDay() <= $now
                && $membresia->fecha_vencimiento->copy()->endOfDay() >= $now)
            ->sortBy('fecha_inicio')
            ->first();

        if (! $membresiaActiva) {
            $membresiaActiva = $membresias
                ->filter(fn (Membresia $membresia): bool => $membresia->estado === 'activa'
                    && $membresia->fecha_inicio->copy()->startOfDay() > $now)
                ->sortBy('fecha_inicio')
                ->first();
        }

        $membresiaActiva ??= $membresias->sortByDesc('fecha_vencimiento')->first();

        if ($membresiaActiva) {
            $periodoAcumulado = $periodService->accumulatedPeriod($membresiaActiva, $membresias);
            $membresiaActiva->setAttribute('fecha_inicio_acumulada', $periodoAcumulado['fecha_inicio']);
            $membresiaActiva->setAttribute('fecha_vencimiento_acumulada', $periodoAcumulado['fecha_vencimiento']);
        }

        // Calculate attendance stats
        $mesActual = now()->month;
        $anioActual = now()->year;

        $asistenciasMes = $cliente->asistencias()
            ->whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $anioActual)
            ->count();

        $asistenciasSemana = $cliente->asistencias()
            ->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Attendance by day of week for the chart
        $asistenciasPorDia = $cliente->asistencias()
            ->selectRaw('DAYOFWEEK(fecha) as dia, count(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')->toArray();

        // Map MySQL DAYOFWEEK (1=Sunday, 2=Monday, etc.) to a more standard array [Mon, Tue, Wed, Thu, Fri, Sat, Sun]
        $diasChart = [];
        $diasMapping = [2 => 'Lun', 3 => 'Mar', 4 => 'Mié', 5 => 'Jue', 6 => 'Vie', 7 => 'Sáb', 1 => 'Dom'];
        foreach ($diasMapping as $mysqlDay => $name) {
            $diasChart[] = $asistenciasPorDia[$mysqlDay] ?? 0;
        }

        return view('clientes.show', compact(
            'cliente', 'membresiaActiva', 'asistenciasMes', 'asistenciasSemana', 'diasChart'
        ));
    }

    public function edit(Cliente $cliente)
    {
        $tiposMembresia = TipoMembresia::where('estado', 'activo')->orderBy('precio')->get();
        $membresiaActiva = $cliente->membresias()
            ->where('estado', 'activa')
            ->where('fecha_inicio', '<=', Carbon::now())
            ->where('fecha_vencimiento', '>=', Carbon::now())
            ->latest()
            ->first();

        return view('clientes.create', compact('cliente', 'tiposMembresia', 'membresiaActiva'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $validated = $request->validated();

        $data = [
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'cedula' => $validated['cedula'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'direccion' => $validated['direccion'],
            'historial_medico' => $validated['historial_medico'] ?? null,
            'estado' => $validated['estado'],
        ];

        if ($request->boolean('eliminar_foto')) {
            if ($cliente->foto_referencia) {
                Storage::disk('public')->delete($cliente->foto_referencia);
            }
            $data['foto_referencia'] = null;
            $data['descriptor_facial'] = null;
        } elseif ($request->hasFile('foto')) {
            if ($cliente->foto_referencia) {
                Storage::disk('public')->delete($cliente->foto_referencia);
            }
            $data['foto_referencia'] = $request->file('foto')->store('fotos_clientes', 'public');
            $data['descriptor_facial'] = null;
        } elseif ($request->filled('foto_base64')) {
            $base64Image = $request->input('foto_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                if ($cliente->foto_referencia) {
                    Storage::disk('public')->delete($cliente->foto_referencia);
                }
                $ext = strtolower($type[1]);
                $fileName = 'fotos_clientes/webcam_'.uniqid().'.'.$ext;
                $decodedImage = base64_decode(substr($base64Image, strpos($base64Image, ',') + 1), true);
                if ($decodedImage !== false) {
                    Storage::disk('public')->put($fileName, $decodedImage);
                    $data['foto_referencia'] = $fileName;
                }
            }
        }

        if ($request->filled('descriptor_facial')) {
            $data['descriptor_facial'] = $request->input('descriptor_facial');
        }

        $cliente->update($data);

        return redirect()->route('user')->with('success', "Cliente {$cliente->nombre} actualizado.");
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->foto_referencia) {
            Storage::disk('public')->delete($cliente->foto_referencia);
        }
        $cliente->delete();

        return redirect()->route('user')->with('success', "Cliente {$cliente->nombre} eliminado.");
    }

    public function toggleStatus(Cliente $cliente)
    {
        $cliente->update(['estado' => $cliente->estado === 'activo' ? 'inactivo' : 'activo']);

        return redirect()->route('user')->with('success', 'Estado del cliente actualizado.');
    }
}
