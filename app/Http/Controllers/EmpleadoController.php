<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Empleado::query();

        // Filtro de búsqueda (nombre, cédula, usuario)
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('cedula', 'like', "%{$buscar}%")
                    ->orWhere('usuario', 'like', "%{$buscar}%");
            });
        }

        // Filtro por rol
        if ($request->filled('rol') && in_array($request->input('rol'), ['admin', 'empleado'])) {
            $query->where('rol', $request->input('rol'));
        }

        // Filtro por estado
        if ($request->filled('estado') && in_array($request->input('estado'), ['activo', 'inactivo'])) {
            $query->where('estado', $request->input('estado'));
        }

        $empleados = $query->orderBy('nombre', 'asc')->paginate(12)->withQueryString();

        // Estadísticas ejecutivas
        $totalEmpleados = Empleado::count();
        $empleadosActivos = Empleado::where('estado', 'activo')->count();
        $empleadosInactivos = Empleado::where('estado', 'inactivo')->count();
        $recepcionistasCount = Empleado::where('rol', 'empleado')->count();

        return view('empleados.index', compact(
            'empleados',
            'totalEmpleados',
            'empleadosActivos',
            'empleadosInactivos',
            'recepcionistasCount'
        ));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'cedula' => 'required|string|max:20|unique:empleados,cedula',
            'usuario' => 'required|string|max:50|unique:empleados,usuario',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:admin,empleado',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'hora_entrada_turno' => 'nullable|date_format:H:i',
            'hora_salida_turno' => 'nullable|date_format:H:i',
            'tolerancia_minutos' => 'nullable|integer|min:0|max:120',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre del empleado es obligatorio.',
            'cedula.required' => 'La cédula de identidad es obligatoria.',
            'cedula.unique' => 'Esta cédula ya se encuentra registrada.',
            'usuario.required' => 'El usuario de acceso es obligatorio.',
            'usuario.unique' => 'Este nombre de usuario ya está ocupado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'foto.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'foto.max' => 'La foto no debe superar los 2 MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('fotos_empleados', 'public');
        } elseif ($request->filled('foto_base64')) {
            $base64Image = $request->input('foto_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                if ($data !== false) {
                    $filename = 'webcam_'.uniqid().'.'.($type === 'jpeg' ? 'jpg' : $type);
                    $fotoPath = 'fotos_empleados/'.$filename;
                    Storage::disk('public')->put($fotoPath, $data);
                }
            }
        }

        Empleado::create([
            'nombre' => $validated['nombre'],
            'cedula' => $validated['cedula'],
            'usuario' => $validated['usuario'],
            'password_hash' => Hash::make($validated['password']),
            'rol' => $validated['rol'],
            'foto_referencia' => $fotoPath,
            'descriptor_facial' => $request->input('descriptor_facial'),
            'hora_entrada_turno' => $validated['hora_entrada_turno'] ?? '08:00',
            'hora_salida_turno' => $validated['hora_salida_turno'] ?? '16:00',
            'tolerancia_minutos' => $validated['tolerancia_minutos'] ?? 10,
            'estado' => $validated['estado'],
        ]);

        return redirect()->route('empleados')->with('success', 'Empleado creado exitosamente.');
    }

    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'cedula' => ['required', 'string', 'max:20', Rule::unique('empleados', 'cedula')->ignore($empleado->id)],
            'usuario' => ['required', 'string', 'max:50', Rule::unique('empleados', 'usuario')->ignore($empleado->id)],
            'password' => 'nullable|string|min:6',
            'rol' => 'required|in:admin,empleado',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'eliminar_foto' => 'nullable|boolean',
            'hora_entrada_turno' => 'nullable|date_format:H:i',
            'hora_salida_turno' => 'nullable|date_format:H:i',
            'tolerancia_minutos' => 'nullable|integer|min:0|max:120',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre del empleado es obligatorio.',
            'cedula.required' => 'La cédula de identidad es obligatoria.',
            'cedula.unique' => 'Esta cédula ya se encuentra registrada por otro empleado.',
            'usuario.required' => 'El usuario de acceso es obligatorio.',
            'usuario.unique' => 'Este nombre de usuario ya está asignado a otro empleado.',
        ]);

        $data = [
            'nombre' => $validated['nombre'],
            'cedula' => $validated['cedula'],
            'usuario' => $validated['usuario'],
            'rol' => $validated['rol'],
            'hora_entrada_turno' => $validated['hora_entrada_turno'] ?? $empleado->hora_entrada_turno,
            'hora_salida_turno' => $validated['hora_salida_turno'] ?? $empleado->hora_salida_turno,
            'tolerancia_minutos' => $validated['tolerancia_minutos'] ?? $empleado->tolerancia_minutos,
            'estado' => $validated['estado'],
        ];

        if ($request->filled('descriptor_facial')) {
            $data['descriptor_facial'] = $request->input('descriptor_facial');
        }

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make($request->input('password'));
        }

        if ($request->boolean('eliminar_foto')) {
            if ($empleado->foto_referencia && Storage::disk('public')->exists($empleado->foto_referencia)) {
                Storage::disk('public')->delete($empleado->foto_referencia);
            }
            $data['foto_referencia'] = null;
            $data['descriptor_facial'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($empleado->foto_referencia && Storage::disk('public')->exists($empleado->foto_referencia)) {
                Storage::disk('public')->delete($empleado->foto_referencia);
            }
            $data['foto_referencia'] = $request->file('foto')->store('fotos_empleados', 'public');
        } elseif ($request->filled('foto_base64')) {
            $base64Image = $request->input('foto_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $imgData = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]);
                $imgData = base64_decode($imgData);
                if ($imgData !== false) {
                    if ($empleado->foto_referencia && Storage::disk('public')->exists($empleado->foto_referencia)) {
                        Storage::disk('public')->delete($empleado->foto_referencia);
                    }
                    $filename = 'webcam_'.uniqid().'.'.($type === 'jpeg' ? 'jpg' : $type);
                    $data['foto_referencia'] = 'fotos_empleados/'.$filename;
                    Storage::disk('public')->put($data['foto_referencia'], $imgData);
                }
            }
        }

        $empleado->update($data);

        return redirect()->route('empleados')->with('success', 'Datos del empleado actualizados exitosamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();

        return redirect()->route('empleados')->with('success', 'Empleado eliminado correctamente.');
    }

    public function toggleStatus(Empleado $empleado)
    {
        $nuevoEstado = $empleado->estado === 'activo' ? 'inactivo' : 'activo';
        $empleado->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado === 'activo' ? 'Empleado activado exitosamente.' : 'Empleado desactivado exitosamente.';

        return redirect()->route('empleados')->with('success', $mensaje);
    }
}
