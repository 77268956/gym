<?php

$files = [];

// ==========================================
// CONTROLLERS
// ==========================================

$files['app/Http/Controllers/EmpleadoController.php'] = <<<'EOT'
<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::all();
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        return view('empleados.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'cedula' => 'required|string|max:20|unique:empleados,cedula',
            'usuario' => 'required|string|max:50|unique:empleados,usuario',
            'password' => 'required|string|min:6',
            'rol' => 'required|in:admin,empleado',
            'hora_entrada_turno' => 'nullable|date_format:H:i',
            'hora_salida_turno' => 'nullable|date_format:H:i',
            'tolerancia_minutos' => 'required|integer',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $data['password_hash'] = bcrypt($data['password']);
        unset($data['password']);

        Empleado::create($data);
        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente.');
    }

    public function edit(Empleado $empleado)
    {
        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'cedula' => 'required|string|max:20|unique:empleados,cedula,'.$empleado->id,
            'usuario' => 'required|string|max:50|unique:empleados,usuario,'.$empleado->id,
            'password' => 'nullable|string|min:6',
            'rol' => 'required|in:admin,empleado',
            'hora_entrada_turno' => 'nullable|date_format:H:i',
            'hora_salida_turno' => 'nullable|date_format:H:i',
            'tolerancia_minutos' => 'required|integer',
            'estado' => 'required|in:activo,inactivo',
        ]);

        if (!empty($data['password'])) {
            $data['password_hash'] = bcrypt($data['password']);
        }
        unset($data['password']);

        $empleado->update($data);
        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado correctamente.');
    }
}
EOT;

$files['app/Http/Controllers/TipoMembresiaController.php'] = <<<'EOT'
<?php

namespace App\Http\Controllers;

use App\Models\TipoMembresia;
use Illuminate\Http\Request;

class TipoMembresiaController extends Controller
{
    public function index()
    {
        $tipos = TipoMembresia::all();
        return view('tipos_membresias.index', compact('tipos'));
    }

    public function create()
    {
        return view('tipos_membresias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50',
            'duracion_dias' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
        ]);

        TipoMembresia::create($data);
        return redirect()->route('tipos-membresias.index')->with('success', 'Tipo de Membresía creado.');
    }

    public function edit(TipoMembresia $tipos_membresia) // Note parameter name matched to resource
    {
        return view('tipos_membresias.edit', compact('tipos_membresia'));
    }

    public function update(Request $request, TipoMembresia $tipos_membresia)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:50',
            'duracion_dias' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $tipos_membresia->update($data);
        return redirect()->route('tipos-membresias.index')->with('success', 'Tipo de Membresía actualizado.');
    }

    public function destroy(TipoMembresia $tipos_membresia)
    {
        $tipos_membresia->delete();
        return redirect()->route('tipos-membresias.index')->with('success', 'Tipo de Membresía eliminado.');
    }
}
EOT;

$files['app/Http/Controllers/ProductoEcogimController.php'] = <<<'EOT'
<?php

namespace App\Http\Controllers;

use App\Models\ProductoEcogim;
use Illuminate\Http\Request;

class ProductoEcogimController extends Controller
{
    public function index()
    {
        $productos = ProductoEcogim::all();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:50',
            'puntos_valor' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'estado' => 'required|in:activo,inactivo',
        ]);

        ProductoEcogim::create($data);
        return redirect()->route('productos.index')->with('success', 'Producto creado.');
    }

    public function edit(ProductoEcogim $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, ProductoEcogim $producto)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:50',
            'puntos_valor' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $producto->update($data);
        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(ProductoEcogim $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }
}
EOT;

$files['app/Http/Controllers/ConfiguracionPuntoController.php'] = <<<'EOT'
<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionPunto;
use Illuminate\Http\Request;

class ConfiguracionPuntoController extends Controller
{
    public function index()
    {
        $configuracion = ConfiguracionPunto::orderBy('vigente_desde', 'desc')->first();
        return view('configuracion_puntos.index', compact('configuracion'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'puntos_por_visita' => 'required|integer|min:1',
            'vigente_desde' => 'required|date',
        ]);

        ConfiguracionPunto::create($data);
        return redirect()->route('configuracion-puntos.index')->with('success', 'Configuración actualizada.');
    }
}
EOT;

// ==========================================
// VIEWS
// ==========================================

// Empleados Views
$files['resources/views/empleados/index.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Gestión de Empleados')
@section('content')
<div class="mb-3">
    <a href="{{ route('empleados.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo Empleado</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-striped datatable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cédula</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($empleados as $empleado)
                <tr>
                    <td>{{ $empleado->nombre }}</td>
                    <td>{{ $empleado->cedula }}</td>
                    <td>{{ $empleado->usuario }}</td>
                    <td>{{ ucfirst($empleado->rol) }}</td>
                    <td>{{ \Carbon\Carbon::parse($empleado->hora_entrada_turno)->format('H:i') }} - {{ \Carbon\Carbon::parse($empleado->hora_salida_turno)->format('H:i') }}</td>
                    <td><span class="badge badge-{{ $empleado->estado == 'activo' ? 'success' : 'danger' }}">{{ ucfirst($empleado->estado) }}</span></td>
                    <td>
                        <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('empleados.destroy', $empleado) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este empleado?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
EOT;

$files['resources/views/empleados/create.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Nuevo Empleado')
@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('empleados.store') }}" method="POST">
            @csrf
            @include('empleados._form')
            <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Guardar</button>
            <a href="{{ route('empleados.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
        </form>
    </div>
</div>
@endsection
EOT;

$files['resources/views/empleados/edit.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Editar Empleado')
@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('empleados.update', $empleado) }}" method="POST">
            @csrf @method('PUT')
            @include('empleados._form')
            <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Actualizar</button>
            <a href="{{ route('empleados.index') }}" class="btn btn-secondary mt-3">Cancelar</a>
        </form>
    </div>
</div>
@endsection
EOT;

$files['resources/views/empleados/_form.blade.php'] = <<<'EOT'
<div class="row">
    <div class="col-md-6 form-group">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $empleado->nombre ?? '') }}" required>
    </div>
    <div class="col-md-6 form-group">
        <label>Cédula</label>
        <input type="text" name="cedula" class="form-control" value="{{ old('cedula', $empleado->cedula ?? '') }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>Usuario</label>
        <input type="text" name="usuario" class="form-control" value="{{ old('usuario', $empleado->usuario ?? '') }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>Contraseña {{ isset($empleado) ? '(Dejar en blanco para no cambiar)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ isset($empleado) ? '' : 'required' }}>
    </div>
    <div class="col-md-4 form-group">
        <label>Rol</label>
        <select name="rol" class="form-control" required>
            <option value="empleado" {{ old('rol', $empleado->rol ?? '') == 'empleado' ? 'selected' : '' }}>Empleado</option>
            <option value="admin" {{ old('rol', $empleado->rol ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
    <div class="col-md-4 form-group">
        <label>Hora Entrada (Turno)</label>
        <input type="time" name="hora_entrada_turno" class="form-control" value="{{ old('hora_entrada_turno', isset($empleado) ? \Carbon\Carbon::parse($empleado->hora_entrada_turno)->format('H:i') : '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Hora Salida (Turno)</label>
        <input type="time" name="hora_salida_turno" class="form-control" value="{{ old('hora_salida_turno', isset($empleado) ? \Carbon\Carbon::parse($empleado->hora_salida_turno)->format('H:i') : '') }}">
    </div>
    <div class="col-md-4 form-group">
        <label>Tolerancia (minutos)</label>
        <input type="number" name="tolerancia_minutos" class="form-control" value="{{ old('tolerancia_minutos', $empleado->tolerancia_minutos ?? 10) }}" required>
    </div>
    <div class="col-md-4 form-group">
        <label>Estado</label>
        <select name="estado" class="form-control" required>
            <option value="activo" {{ old('estado', $empleado->estado ?? '') == 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ old('estado', $empleado->estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
        </select>
    </div>
</div>
EOT;

// Tipos Membresias Views
$files['resources/views/tipos_membresias/index.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Tipos de Membresías')
@section('content')
<div class="mb-3">
    <a href="{{ route('tipos-membresias.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo Tipo</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-striped datatable">
            <thead><tr><th>Nombre</th><th>Duración (Días)</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                @foreach($tipos as $tipo)
                <tr>
                    <td>{{ $tipo->nombre }}</td>
                    <td>{{ $tipo->duracion_dias }}</td>
                    <td>${{ number_format($tipo->precio, 2) }}</td>
                    <td>{{ ucfirst($tipo->estado) }}</td>
                    <td>
                        <a href="{{ route('tipos-membresias.edit', $tipo) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('tipos-membresias.destroy', $tipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
EOT;

$files['resources/views/tipos_membresias/create.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Nuevo Tipo de Membresía')
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('tipos-membresias.store') }}" method="POST">
        @csrf @include('tipos_membresias._form')
        <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
    </form>
</div></div>
@endsection
EOT;

$files['resources/views/tipos_membresias/edit.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Editar Tipo de Membresía')
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('tipos-membresias.update', $tipos_membresia) }}" method="POST">
        @csrf @method('PUT') @include('tipos_membresias._form')
        <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
    </form>
</div></div>
@endsection
EOT;

$files['resources/views/tipos_membresias/_form.blade.php'] = <<<'EOT'
<div class="row mb-3">
    <div class="col-md-3"><label>Nombre</label><input type="text" name="nombre" class="form-control" value="{{ $tipos_membresia->nombre ?? '' }}" required></div>
    <div class="col-md-3"><label>Duración (Días)</label><input type="number" name="duracion_dias" class="form-control" value="{{ $tipos_membresia->duracion_dias ?? '' }}" required></div>
    <div class="col-md-3"><label>Precio</label><input type="number" step="0.01" name="precio" class="form-control" value="{{ $tipos_membresia->precio ?? '' }}" required></div>
    <div class="col-md-3"><label>Estado</label><select name="estado" class="form-control" required><option value="activo" {{ ($tipos_membresia->estado ?? '') == 'activo' ? 'selected' : '' }}>Activo</option><option value="inactivo" {{ ($tipos_membresia->estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo</option></select></div>
</div>
EOT;

// Productos Views
$files['resources/views/productos/index.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Productos EcoGim (Tienda)')
@section('content')
<div class="mb-3">
    <a href="{{ route('productos.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Nuevo Producto</a>
</div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-striped datatable">
            <thead><tr><th>Nombre</th><th>Categoría</th><th>Puntos</th><th>Stock</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
                @foreach($productos as $prod)
                <tr>
                    <td>{{ $prod->nombre }}</td><td>{{ $prod->categoria }}</td><td>{{ $prod->puntos_valor }} pts</td><td>{{ $prod->stock }}</td><td>{{ ucfirst($prod->estado) }}</td>
                    <td>
                        <a href="{{ route('productos.edit', $prod) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
EOT;

$files['resources/views/productos/create.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Nuevo Producto')
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('productos.store') }}" method="POST">
        @csrf @include('productos._form')
        <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
    </form>
</div></div>
@endsection
EOT;

$files['resources/views/productos/edit.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Editar Producto')
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('productos.update', $producto) }}" method="POST">
        @csrf @method('PUT') @include('productos._form')
        <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
    </form>
</div></div>
@endsection
EOT;

$files['resources/views/productos/_form.blade.php'] = <<<'EOT'
<div class="row mb-3">
    <div class="col-md-6"><label>Nombre</label><input type="text" name="nombre" class="form-control" value="{{ $producto->nombre ?? '' }}" required></div>
    <div class="col-md-6"><label>Categoría</label><input type="text" name="categoria" class="form-control" value="{{ $producto->categoria ?? '' }}"></div>
    <div class="col-md-4 mt-2"><label>Valor en Puntos</label><input type="number" name="puntos_valor" class="form-control" value="{{ $producto->puntos_valor ?? '' }}" required></div>
    <div class="col-md-4 mt-2"><label>Stock</label><input type="number" name="stock" class="form-control" value="{{ $producto->stock ?? 0 }}" required></div>
    <div class="col-md-4 mt-2"><label>Estado</label><select name="estado" class="form-control" required><option value="activo" {{ ($producto->estado ?? '') == 'activo' ? 'selected' : '' }}>Activo</option><option value="inactivo">Inactivo</option></select></div>
    <div class="col-md-12 mt-2"><label>Descripción</label><textarea name="descripcion" class="form-control">{{ $producto->descripcion ?? '' }}</textarea></div>
</div>
EOT;

// Configuración Puntos
$files['resources/views/configuracion_puntos/index.blade.php'] = <<<'EOT'
@extends('layouts.app')
@section('title', 'Configuración de Puntos')
@section('content')
<div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('configuracion-puntos.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Puntos otorgados por visita diaria</label>
                <input type="number" name="puntos_por_visita" class="form-control" value="{{ $configuracion->puntos_por_visita ?? 10 }}" required>
            </div>
            <div class="col-md-6 form-group">
                <label>Fecha de vigencia (Desde)</label>
                <input type="date" name="vigente_desde" class="form-control" value="{{ $configuracion->vigente_desde ? $configuracion->vigente_desde->format('Y-m-d') : date('Y-m-d') }}" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Nueva Configuración</button>
    </form>
    @if($configuracion)
        <p class="mt-3 text-muted">La configuración actual otorga {{ $configuracion->puntos_por_visita }} puntos (vigente desde {{ $configuracion->vigente_desde->format('d/m/Y') }}).</p>
    @endif
</div></div>
@endsection
EOT;

// Create necessary directories
$directories = [
    'app/Http/Controllers',
    'resources/views/empleados',
    'resources/views/tipos_membresias',
    'resources/views/productos',
    'resources/views/configuracion_puntos',
];

foreach ($directories as $dir) {
    if (! is_dir(__DIR__.'/'.$dir)) {
        mkdir(__DIR__.'/'.$dir, 0755, true);
    }
}

// Write files
foreach ($files as $path => $content) {
    file_put_contents(__DIR__.'/'.$path, $content);
}

// Update routes
$routesPath = __DIR__.'/routes/web.php';
$routesContent = file_get_contents($routesPath);

$newRoutes = <<<'EOT'
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\TipoMembresiaController;
use App\Http\Controllers\ProductoEcogimController;
use App\Http\Controllers\ConfiguracionPuntoController;

Route::middleware('auth')->group(function () {
    Route::resource('empleados', EmpleadoController::class);
    Route::resource('tipos-membresias', TipoMembresiaController::class);
    Route::resource('productos', ProductoEcogimController::class);
    Route::get('configuracion-puntos', [ConfiguracionPuntoController::class, 'index'])->name('configuracion-puntos.index');
    Route::post('configuracion-puntos', [ConfiguracionPuntoController::class, 'store'])->name('configuracion-puntos.store');
});
EOT;

// Check if routes are already added to prevent duplication
if (strpos($routesContent, 'EmpleadoController') === false) {
    // Insert new routes at the end of the file
    $routesContent = str_replace("})->name('dashboard');", "})->name('dashboard');\n\n".$newRoutes, $routesContent);
    file_put_contents($routesPath, $routesContent);
}

// Update app.blade.php sidebar links
$layoutPath = __DIR__.'/resources/views/layouts/app.blade.php';
$layoutContent = file_get_contents($layoutPath);

// Adding specific routes to the sidebar placeholders
$layoutContent = str_replace(
    '<a class="nav-link" href="#">
                                <i class="fas fa-gift"></i> Tienda EcoGim
                            </a>',
    '<a class="nav-link" href="{{ route(\'productos.index\') }}">
                                <i class="fas fa-gift"></i> Tienda EcoGim
                            </a>',
    $layoutContent
);

$layoutContent = str_replace(
    '<a class="nav-link" href="#">
                                <i class="fas fa-user-tie"></i> Empleados
                            </a>',
    '<a class="nav-link" href="{{ route(\'empleados.index\') }}">
                                <i class="fas fa-user-tie"></i> Empleados
                            </a>',
    $layoutContent
);

$layoutContent = str_replace(
    '<a class="nav-link" href="#">
                                <i class="fas fa-cogs"></i> Configuración
                            </a>',
    '<a class="nav-link" href="{{ route(\'tipos-membresias.index\') }}">
                                <i class="fas fa-tags"></i> Tipos Membresía
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route(\'configuracion-puntos.index\') }}">
                                <i class="fas fa-star"></i> Puntos EcoGim
                            </a>',
    $layoutContent
);

// Add initialization of datatables in the layout
if (strpos($layoutContent, 'DataTable()') === false) {
    $layoutContent = str_replace(
        '@stack(\'scripts\')',
        '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <script>
        $(document).ready(function() {
            if($(".datatable").length > 0) {
                $(".datatable").DataTable({
                    language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" }
                });
            }
        });
    </script>
    @stack(\'scripts\')',
        $layoutContent
    );
}

file_put_contents($layoutPath, $layoutContent);

echo "CRUDs generados exitosamente.\n";
