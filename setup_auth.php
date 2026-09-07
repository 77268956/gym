<?php

// 1. Update Empleado Model
$empleadoPath = __DIR__.'/app/Models/Empleado.php';
$empleadoContent = <<<'EOT'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'cedula',
        'usuario',
        'password_hash',
        'rol',
        'foto_referencia',
        'hora_entrada_turno',
        'hora_salida_turno',
        'tolerancia_minutos',
        'estado',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    
    // Allow using 'usuario' instead of email if needed by default Laravel methods, though we'll handle it manually
    public function getAuthIdentifierName()
    {
        return 'usuario';
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function asistenciasClientesValidadas()
    {
        return $this->hasMany(AsistenciaCliente::class, 'empleado_valida_id');
    }

    public function asistencias()
    {
        return $this->hasMany(AsistenciaEmpleado::class);
    }

    public function canjesProcesados()
    {
        return $this->hasMany(CanjeEcogim::class, 'empleado_id');
    }
}
EOT;
file_put_contents($empleadoPath, $empleadoContent);

// 2. Update config/auth.php providers
$authConfigPath = __DIR__.'/config/auth.php';
$authConfig = file_get_contents($authConfigPath);
$authConfig = str_replace(
    'App\Models\User::class',
    'App\Models\Empleado::class',
    $authConfig
);
file_put_contents($authConfigPath, $authConfig);

// 3. AuthController
$authControllerPath = __DIR__.'/app/Http/Controllers/AuthController.php';
$authControllerContent = <<<'EOT'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Attempt login, overriding the password key to match 'password_hash' in the database
        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'usuario' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('usuario');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
EOT;
file_put_contents($authControllerPath, $authControllerContent);

// 4. Routes
$routesPath = __DIR__.'/routes/web.php';
$routesContent = <<<'EOT'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
EOT;
file_put_contents($routesPath, $routesContent);

// 5. Create views directories
@mkdir(__DIR__.'/resources/views/auth', 0755, true);
@mkdir(__DIR__.'/resources/views/layouts', 0755, true);

// 6. Layouts
$layoutPath = __DIR__.'/resources/views/layouts/app.blade.php';
$layoutContent = <<<'EOT'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoGim - @yield('title', 'Admin')</title>
    <!-- Bootstrap 4 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0; 
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            background-color: #343a40;
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: #c2c7d0;
            padding: 10px 20px;
        }
        .sidebar .nav-link .fas {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
            font-size: 1rem;
            background-color: rgba(0, 0, 0, .25);
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
        }
        main {
            padding-top: 60px;
        }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">EcoGim Panel</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="navbar-nav px-3 ml-auto">
            <li class="nav-item text-nowrap">
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-white">Cerrar Sesión</button>
                </form>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Módulos</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users"></i> Socios / Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-money-bill-wave"></i> Membresías y Pagos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-camera"></i> Recepción Facial
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-gift"></i> Tienda EcoGim
                            </a>
                        </li>
                        
                        @if(Auth::user() && Auth::user()->rol === 'admin')
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Administración</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user-tie"></i> Empleados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cogs"></i> Configuración
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title', 'Dashboard')</h1>
                </div>

                @yield('content')

            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Librerias obligatorias del PDF -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    
    @stack('scripts')
</body>
</html>
EOT;
file_put_contents($layoutPath, $layoutContent);

// 7. Login view
$loginPath = __DIR__.'/resources/views/auth/login.blade.php';
$loginContent = <<<'EOT'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcoGim</title>
    <!-- Bootstrap 4 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .login-logo {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #28a745;
        }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="card-body p-4">
        <div class="login-logo">
            EcoGim <i class="fas fa-leaf"></i>
        </div>
        <h5 class="text-center mb-4">Iniciar Sesión</h5>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" value="{{ old('usuario') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-success btn-block mt-4">Ingresar al Sistema</button>
        </form>
    </div>
</div>

</body>
</html>
EOT;
file_put_contents($loginPath, $loginContent);

// 8. Dashboard view
$dashboardPath = __DIR__.'/resources/views/dashboard.blade.php';
$dashboardContent = <<<'EOT'
@extends('layouts.app')

@section('title', 'Dashboard Principal')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">¡Bienvenido, {{ Auth::user()->nombre }}!</h5>
                <p class="card-text">Has iniciado sesión con el rol de <strong>{{ ucfirst(Auth::user()->rol) }}</strong>.</p>
                <p class="text-muted">Selecciona un módulo del menú lateral para comenzar a operar.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Placeholders para los futuros gráficos de Chart.js -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                Flujo de Asistencias (Próximamente)
            </div>
            <div class="card-body d-flex align-items-center justify-content-center bg-light">
                <span class="text-muted">Área de gráfico (Chart.js)</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                Ingresos Generados (Próximamente)
            </div>
            <div class="card-body d-flex align-items-center justify-content-center bg-light">
                <span class="text-muted">Área de gráfico (Chart.js)</span>
            </div>
        </div>
    </div>
</div>
@endsection
EOT;
file_put_contents($dashboardPath, $dashboardContent);

echo "Archivos generados exitosamente.\n";
