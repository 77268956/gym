<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Membresia;
use App\Models\Pago;
use App\Models\TipoMembresia;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        return view('clientes.create', compact('tiposMembresia'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:50|unique:clientes,cedula',
            'telefono' => 'nullable|string|max:20',
            'historial_medico' => 'nullable|string|max:2000',
            'foto' => 'nullable|image|max:2048',
            'tipo_membresia_id' => 'required|exists:tipos_membresia,id',
        ]);

        $fotoPath = null;
        $descriptorFacial = null;

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos_clientes', 'public');
        } elseif ($request->filled('foto_base64')) {
            $base64Image = $request->input('foto_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $ext = strtolower($type[1]);
                $fileName = 'fotos_clientes/webcam_'.uniqid().'.'.$ext;
                Storage::disk('public')->put($fileName, base64_decode($data));
                $fotoPath = $fileName;
            }
        }

        if ($request->filled('descriptor_facial')) {
            $descriptorFacial = $request->input('descriptor_facial');
        }

        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'foto_referencia' => $fotoPath,
            'descriptor_facial' => $descriptorFacial,
            'historial_medico' => $request->historial_medico,
            'puntos_ecogim' => 0,
            'estado' => 'activo',
            'ultima_actividad' => now(),
        ]);

        $tipo = TipoMembresia::findOrFail($request->tipo_membresia_id);
        $fechaInicio = Carbon::today();
        $fechaVencimiento = $fechaInicio->copy()->addDays($tipo->duracion_dias ?? 30);

        $membresia = Membresia::create([
            'cliente_id' => $cliente->id,
            'tipo_membresia_id' => $tipo->id,
            'fecha_inicio' => $fechaInicio,
            'fecha_vencimiento' => $fechaVencimiento,
            'estado' => 'activa',
        ]);

        // Registrar el pago automáticamente
        $empleadoId = Empleado::first()?->id ?? 1;
        Pago::create([
            'cliente_id' => $cliente->id,
            'empleado_id' => $empleadoId,
            'membresia_id' => $membresia->id,
            'tipo_pago' => 'membresia',
            'metodo_pago' => 'efectivo',
            'monto' => $tipo->precio,
            'fecha_pago' => Carbon::now(),
        ]);

        return redirect()->route('user')->with('success', "Cliente {$cliente->nombre} registrado con membresía {$tipo->nombre}.");
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['membresias.tipoMembresia', 'asistencias' => function ($q) {
            $q->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->take(30);
        }, 'canjes.producto']);

        $membresiaActiva = $cliente->membresias()->where('estado', 'activa')->latest()->first();

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
        $membresiaActiva = $cliente->membresias()->where('estado', 'activa')->latest()->first();

        return view('clientes.edit', compact('cliente', 'tiposMembresia', 'membresiaActiva'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:50|unique:clientes,cedula,'.$cliente->id,
            'telefono' => 'nullable|string|max:20',
            'historial_medico' => 'nullable|string|max:2000',
            'foto' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nombre' => $request->nombre,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'historial_medico' => $request->historial_medico,
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
                Storage::disk('public')->put($fileName, base64_decode(substr($base64Image, strpos($base64Image, ',') + 1)));
                $data['foto_referencia'] = $fileName;
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
