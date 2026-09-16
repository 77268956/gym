<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }} - @yield('title', 'Escáner facial')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary: {{ $gymConfig->color_primario ?? '#2563EB' }};
            --primary-hover: {{ $gymConfig->color_primario_hover ?? '#1D4ED8' }};
            --sidebar-bg: {{ $gymConfig->color_sidebar ?? '#1E293B' }};
            --sidebar-hover: {{ $gymConfig->color_sidebar_hover ?? '#334155' }};
        }
        body { min-height: 100vh; margin: 0; background: #F1F5F9; color: #334155; font-family: 'Inter', sans-serif; }
        .scanner-topbar { background: var(--sidebar-bg); color: #fff; padding: .85rem 1.25rem; box-shadow: 0 2px 8px rgba(15, 23, 42, .15); }
        .scanner-brand { color: #fff; font-weight: 700; text-decoration: none; }
        .scanner-brand:hover { color: #fff; text-decoration: none; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--primary-hover); border-color: var(--primary-hover); }
        .text-primary { color: var(--primary) !important; }
        .badge-primary { background-color: var(--primary); }
        @media (max-width: 767.98px) { .scanner-topbar { padding: .75rem 1rem; } }
    </style>
    @stack('styles')
</head>
<body>
    <header class="scanner-topbar d-flex align-items-center justify-content-between">
        <a href="{{ route('login') }}" class="scanner-brand">
            @if(isset($gymConfig) && $gymConfig->logo_path)
                <img src="{{ asset('storage/' . $gymConfig->logo_path) }}" alt="Logo" style="height: 30px; max-width: 120px; object-fit: contain;" class="mr-2">
            @else
                <i class="fas fa-dumbbell mr-2"></i>
            @endif
            {{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }}
        </a>
        <span class="small text-white-50"><i class="fas fa-camera mr-1"></i> Recepción</span>
    </header>

    <main>
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
