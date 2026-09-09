<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }} - @yield('title', 'Admin')</title>
    <!-- Modern Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Frameworks & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>

        /* =========================================================
           VARIABLES - Modern Gym Admin Palette
        ========================================================= */
        :root {
            --primary: #2563EB;       /* Blue 600 - Azul Puro (No morado) */
            --primary-hover: #1D4ED8; /* Blue 700 */
            --sidebar-bg: #1E293B;    /* Slate 800 - Fondo oscuro y elegante */
            --sidebar-hover: #334155; /* Slate 700 */
            --topbar-bg: #FFFFFF;     /* Topbar limpio */
            --background-light: #F1F5F9; /* Slate 100 - Fondo app */
            --text-main: #334155;
            --text-muted: #64748B;
            --navbar-h:  64px;        /* Un poco más alto */
            --sidebar-w: 240px;       /* Un poco más ancho */
            --sidebar-cw: 72px;
            --trans: .3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* =========================================================
           BASE
        ========================================================= */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: .875rem;
            background-color: var(--background-light);
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            font-weight: 500;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .text-primary { color: var(--primary) !important; }
        .border-primary { border-color: var(--primary) !important; }

        /* =========================================================
           TOPBAR
        ========================================================= */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--navbar-h);
            z-index: 200;
            background: var(--topbar-bg);
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid #E2E8F0;
        }
        .topbar-brand {
            width: var(--sidebar-w);
            flex-shrink: 0;
            padding: 0 1.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--sidebar-bg);
            white-space: nowrap;
            overflow: hidden;
            transition: width var(--trans);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .topbar-brand:hover { color: var(--primary); text-decoration: none; }
        .topbar-actions {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 1rem;
        }
        .topbar-toggle {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            color: var(--text-main);
            font-size: 1rem;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            line-height: 1;
            transition: all 0.2s;
        }
        .topbar-toggle:hover { background: #F1F5F9; color: var(--primary); }
        .topbar-toggle i { transition: transform var(--trans); display: block; }
        
        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            padding-right: 1.5rem;
        }

        /* Mobile toggle (hamburger) */
        .topbar-mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-main);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 6px 12px;
        }

        /* =========================================================
           SIDEBAR
        ========================================================= */
        .sidebar {
            position: fixed;
            top: var(--navbar-h);
            left: 0;
            bottom: 0;
            width: var(--sidebar-w);
            z-index: 150;
            background: var(--sidebar-bg);
            overflow: hidden;
            transition: width var(--trans);
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 8px rgba(0,0,0,.05);
        }
        .sidebar-inner {
            flex: 1;
            width: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            white-space: nowrap;
            padding: 12px 24px;
            color: #94A3B8;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all var(--trans);
            margin-bottom: 2px;
        }
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            flex-shrink: 0;
            margin-right: 12px;
            font-size: 1.1rem;
        }
        .sidebar .nav-link:hover { color: #F8FAFC; background: var(--sidebar-hover); text-decoration: none; }
        .sidebar .nav-link.active  { color: #fff; background: var(--sidebar-hover); border-left-color: var(--primary); font-weight: 600; }
        .sidebar-heading {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #64748B;
            padding: 16px 24px 8px;
            white-space: nowrap;
            font-weight: 600;
        }

        /* Sidebar Footer (User Profile & Logout) */
        .sidebar-footer {
            width: 100%;
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(0, 0, 0, 0.15);
            transition: padding var(--trans);
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .sidebar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .sidebar-user-info {
            margin-left: 12px;
            overflow: hidden;
        }
        .sidebar-user-name {
            color: #F8FAFC;
            font-weight: 600;
            font-size: 0.85rem;
            display: block;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .sidebar-user-role {
            color: #94A3B8;
            font-size: 0.75rem;
            display: block;
        }
        .sidebar-footer .btn-logout {
            width: 100%;
            background: rgba(239, 68, 68, 0.1);
            color: #FCA5A5;
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 8px;
            border-radius: 6px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .sidebar-footer .btn-logout:hover {
            background: #EF4444;
            color: white;
        }
        .sidebar-footer .btn-logout i { margin-right: 8px; }

        /* =========================================================
           MAIN CONTENT
        ========================================================= */
        #page-wrapper {
            margin-top: var(--navbar-h);
            margin-left: var(--sidebar-w);
            transition: margin-left var(--trans);
            min-height: calc(100vh - var(--navbar-h));
        }
        main {
            padding: 2rem;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 0;
            border: none;
        }
        .page-title {
            margin: 0;
            color: var(--sidebar-bg);
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* =========================================================
           COLLAPSED STATE (desktop)
        ========================================================= */
        body.sidebar-collapsed .sidebar          { width: var(--sidebar-cw); }
        body.sidebar-collapsed #page-wrapper     { margin-left: var(--sidebar-cw); }
        body.sidebar-collapsed .topbar-brand     { width: var(--sidebar-cw); padding: 0; justify-content: center; }
        body.sidebar-collapsed .topbar-brand span { display: none; } /* Hide text */
        
        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding: 12px 0;
        }
        body.sidebar-collapsed .sidebar .nav-link i  { margin-right: 0; font-size: 1.25rem; }
        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .sidebar-heading      { display: none; }
        body.sidebar-collapsed .topbar-toggle i      { transform: rotate(180deg); }

        /* Sidebar Footer Collapsed */
        body.sidebar-collapsed .sidebar-footer { padding: 1rem 0; text-align: center; }
        body.sidebar-collapsed .sidebar-user-info { display: none; }
        body.sidebar-collapsed .sidebar-user-avatar { margin: 0 auto 0.75rem auto; }
        body.sidebar-collapsed .btn-logout span { display: none; }
        body.sidebar-collapsed .btn-logout i { margin-right: 0; font-size: 1.1rem; }
        body.sidebar-collapsed .btn-logout { padding: 10px; width: 40px; margin: 0 auto; }

        /* =========================================================
           OVERLAY (móvil)
        ========================================================= */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 140;
        }
        body.mobile-sidebar-open .sidebar-overlay { display: block; }

        /* =========================================================
           MOBILE  (<768px)
        ========================================================= */
        @media (max-width: 767.98px) {
            .topbar-brand        { display: none; }
            .topbar-actions      { display: none; }
            .topbar-mobile-toggle {
                display: flex;
                align-items: center;
                padding: 0 16px;
                flex-shrink: 0;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
                z-index: 150;
            }
            body.mobile-sidebar-open .sidebar { transform: translateX(0); }
            #page-wrapper { margin-left: 0 !important; }
            main { padding: 1.5rem 1rem; }

            body.sidebar-collapsed .sidebar     { width: 280px !important; transform: translateX(-100%); }
            body.sidebar-collapsed #page-wrapper { margin-left: 0 !important; }
            body.sidebar-collapsed .sidebar .nav-link { justify-content: flex-start; padding: 12px 24px; }
            body.sidebar-collapsed .sidebar .nav-link i { margin-right: 12px; }
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .sidebar-heading { display: block; }
            body.mobile-sidebar-open.sidebar-collapsed .sidebar { transform: translateX(0); }
            
            body.sidebar-collapsed .sidebar-footer { padding: 1rem; text-align: left; }
            body.sidebar-collapsed .sidebar-user-info { display: block; }
            body.sidebar-collapsed .sidebar-user-avatar { margin: 0; }
            body.sidebar-collapsed .btn-logout span { display: inline; }
            body.sidebar-collapsed .btn-logout i { margin-right: 8px; }
            body.sidebar-collapsed .btn-logout { width: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- ===== TOPBAR ===== -->
    <header class="topbar">
        <button id="mobileSidebarToggle" class="topbar-mobile-toggle" title="Abrir menu">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand / Logo -->
        <a class="topbar-brand" href="{{ route('dashboard') }}">
            @if(isset($gymConfig) && $gymConfig->logo_path)
                <img src="{{ asset('storage/' . $gymConfig->logo_path) }}" alt="Logo" style="height: 32px; max-height: 36px; max-width: 140px; object-fit: contain;" class="mr-3">
            @else
                <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded mr-2" style="width: 32px; height: 32px;">
                    <i class="fas fa-dumbbell font-weight-bold" style="font-size: 0.9rem;"></i>
                </div>
            @endif
            <span>{{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }}</span>
        </a>

        <!-- Desktop Toggle -->
        <div class="topbar-actions">
            <button id="sidebarToggle" class="topbar-toggle" title="Colapsar menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="topbar-right">
            <!-- Empty for now, can add notifications or clock here later -->
        </div>
    </header>

    <!-- Overlay móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-inner">
            <ul class="nav flex-column mt-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">
                        <i class="fas fa-chart-line"></i>
                        <span class="sidebar-label">Dashboard</span>
                    </a>
                </li>

                <div class="sidebar-heading">Módulos</div>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user') ? 'active' : '' }}" href="{{ route('user') }}" title="Socios / Clientes">
                        <i class="fas fa-users"></i>
                        <span class="sidebar-label">Socios / Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('membresias.*') ? 'active' : '' }}" href="{{ route('membresias.index') }}" title="Membresías y Pagos">
                        <i class="fas fa-id-card"></i>
                        <span class="sidebar-label">Membresías y Pagos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Recepción Facial">
                        <i class="fas fa-camera"></i>
                        <span class="sidebar-label">Recepción Facial</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Tienda EcoGim">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="sidebar-label">Tienda EcoGim</span>
                    </a>
                </li>

                @if(Auth::user() && Auth::user()->rol === 'admin')
                    <div class="sidebar-heading">Administración</div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('empleados*') ? 'active' : '' }}" href="{{ route('empleados') }}" title="Empleados">
                            <i class="fas fa-user-shield"></i>
                            <span class="sidebar-label">Empleados</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}" href="{{ route('configuracion.index') }}" title="Configuración">
                            <i class="fas fa-cog"></i>
                            <span class="sidebar-label">Configuración</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        
        <!-- Sidebar Footer (Usuario y Cerrar Sesión) -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar shadow-sm">
                    {{ strtoupper(substr(Auth::user()->usuario ?? 'A', 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{ Auth::user()->nombre ?? 'Administrador' }}</span>
                    <span class="sidebar-user-role">
                        @if(Auth::user() && Auth::user()->rol === 'admin') 
                            Admin General 
                        @else 
                            Recepcionista 
                        @endif
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ===== PAGE WRAPPER ===== -->
    <div id="page-wrapper">
        <main>
            <header class="page-header">
                <h1 class="page-title">@yield('title')</h1>
            </header>
            @yield('content')
        </main>
    </div>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <script>
        (function () {
            var body    = document.body;
            var toggle  = document.getElementById('sidebarToggle');
            var mToggle = document.getElementById('mobileSidebarToggle');
            var overlay = document.getElementById('sidebarOverlay');
            var isMobile = function () { return window.innerWidth < 768; };

            if (!isMobile() && localStorage.getItem('sidebar-collapsed') === 'true') {
                body.classList.add('sidebar-collapsed');
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    var collapsed = body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', collapsed);
                });
            }

            function openMobileSidebar() { body.classList.add('mobile-sidebar-open'); }
            function closeMobileSidebar() { body.classList.remove('mobile-sidebar-open'); }

            if (mToggle) { mToggle.addEventListener('click', openMobileSidebar); }
            if (overlay) { overlay.addEventListener('click', closeMobileSidebar); }

            document.querySelectorAll('#sidebar .nav-link').forEach(function (link) {
                link.addEventListener('click', closeMobileSidebar);
            });

            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    closeMobileSidebar();
                    if (localStorage.getItem('sidebar-collapsed') === 'true') {
                        body.classList.add('sidebar-collapsed');
                    }
                }
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>