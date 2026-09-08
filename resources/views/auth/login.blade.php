<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }}</title>
    <!-- Bootstrap 4 CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .login-logo img {
            max-height: 70px;
            max-width: 200px;
            object-fit: contain;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="card-body p-4">
        <div class="login-logo">
            @if(isset($gymConfig) && $gymConfig->logo_path)
                <img src="{{ asset('storage/' . $gymConfig->logo_path) }}" alt="{{ $gymConfig->nombre_gimnasio }}">
            @else
                <i class="fas fa-dumbbell fa-2x mb-2 text-success"></i>
            @endif
            <span>{{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }}</span>
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