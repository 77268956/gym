<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }} - @yield('title', 'Admin')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>

        /* =========================================================
           VARIABLES
        ========================================================= */
        :root {
            --navbar-h:  56px;
            --sidebar-w: 220px;
            --sidebar-cw: 64px;
            --trans: .25s ease;
        }

        /* =========================================================
           BASE
        ========================================================= */
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
            /* Nada de margin/padding extra; el navbar ocupa el top */
        }

        /* =========================================================
           TOPBAR
        ========================================================= */
        .topbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--navbar-h);
            z-index: 200;
            background: #343a40;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,.3);
        }
        .topbar-brand {
            width: var(--sidebar-w);
            flex-shrink: 0;
            padding: 0 1rem;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            transition: width var(--trans);
            text-decoration: none;
        }
        .topbar-brand:hover { color: #fff; text-decoration: none; }
        .topbar-actions {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 1rem;
        }
        .topbar-toggle {
            background: none;
            border: none;
            color: #c2c7d0;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 4px;
            line-height: 1;
            transition: color var(--trans);
        }
        .topbar-toggle:hover, .topbar-toggle:focus { color: #fff; outline: none; }
        .topbar-toggle i { transition: transform var(--trans); display: block; }
        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            padding-right: 1rem;
        }
        .topbar-right form button {
            background: none;
            border: none;
            color: #c2c7d0;
            cursor: pointer;
            font-size: .875rem;
            padding: 6px 12px;
        }
        .topbar-right form button:hover { color: #fff; }

        /* Mobile toggle (hamburger) */
        .topbar-mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #c2c7d0;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 6px 10px;
        }
        .topbar-mobile-toggle:focus { outline: none; }

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
            background: #2d3238;
            overflow: hidden;
            transition: width var(--trans);
            box-shadow: 2px 0 6px rgba(0,0,0,.15);
        }
        .sidebar-inner {
            width: 100%;   /* sigue el ancho actual del sidebar (expandido o colapsado) */
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            padding-top: .5rem;
        }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            white-space: nowrap;
            padding: 10px 20px;
            color: #c2c7d0;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: background var(--trans), color var(--trans), border-color var(--trans);
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            margin-right: 10px;
            font-size: 1rem;
        }
        .sidebar .nav-link:hover { color: #fff; background: #3d4349; text-decoration: none; }
        .sidebar .nav-link.active  { color: #fff; background: #3d4349; border-left-color: #2f7d72; }
        .sidebar-heading {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6c757d;
            padding: 12px 20px 4px;
            white-space: nowrap;
        }

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
            padding: 28px 28px 32px;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            padding: 0 0 12px 14px;
            border-bottom: 1px solid #dee2e6;
            border-left: 4px solid #2f7d72;
        }
        .page-title {
            margin: 0;
            color: #212529;
            font-size: 1.6rem;
            font-weight: 600;
        }

        /* =========================================================
           COLLAPSED STATE (desktop)
        ========================================================= */
        body.sidebar-collapsed .sidebar          { width: var(--sidebar-cw); }
        body.sidebar-collapsed #page-wrapper     { margin-left: var(--sidebar-cw); }
        body.sidebar-collapsed .topbar-brand     { width: var(--sidebar-cw); }

        body.sidebar-collapsed .sidebar .nav-link {
            justify-content: center;
            padding: 12px 0;
        }
        body.sidebar-collapsed .sidebar .nav-link i  { margin-right: 0; }
        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .sidebar-heading      { display: none; }
        body.sidebar-collapsed .topbar-toggle i      { transform: rotate(180deg); }

        /* =========================================================
           OVERLAY (móvil: fondo oscuro al abrir sidebar)
        ========================================================= */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 140;
        }
        body.mobile-sidebar-open .sidebar-overlay { display: block; }

        /* =========================================================
           MOBILE  (<768px)
        ========================================================= */
        @media (max-width: 767.98px) {
            /* Topbar: hamburger a la izquierda, título centrado, logout a la derecha */
            .topbar-brand        { display: none; }
            .topbar-actions      { display: none; }   /* oculta collapse desktop */
            .topbar-mobile-toggle {
                display: flex;
                align-items: center;
                padding: 0 12px;
                flex-shrink: 0;
            }

            /* Sidebar deslizable desde la izquierda */
            .sidebar {
                transform: translateX(-100%);
                transition: transform var(--trans);
                width: 260px !important;
                z-index: 150;
            }
            body.mobile-sidebar-open .sidebar { transform: translateX(0); }

            /* El contenido ocupa todo el ancho */
            #page-wrapper {
                margin-left: 0 !important;
            }

            main { padding: 20px 16px 24px; }
            .page-title { font-size: 1.4rem; }

            /* El sidebar-collapsed no aplica en móvil */
            body.sidebar-collapsed .sidebar     { width: 260px !important; transform: translateX(-100%); }
            body.sidebar-collapsed #page-wrapper { margin-left: 0 !important; }
            body.sidebar-collapsed .sidebar .nav-link {
                justify-content: flex-start;
                padding: 10px 20px;
            }
            body.sidebar-collapsed .sidebar .nav-link i { margin-right: 10px; }
            body.sidebar-collapsed .sidebar-label,
            body.sidebar-collapsed .sidebar-heading { display: block; }
            body.mobile-sidebar-open.sidebar-collapsed .sidebar { transform: translateX(0); }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- ===== TOPBAR ===== -->
    <header class="topbar">
        <!-- Hamburger móvil (izquierda) -->
        <button id="mobileSidebarToggle" class="topbar-mobile-toggle" title="Abrir menu">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Brand / Logo -->
        <a class="topbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            @if(isset($gymConfig) && $gymConfig->logo_path)
                <img src="{{ asset('storage/' . $gymConfig->logo_path) }}" alt="Logo" style="height: 32px; max-height: 36px; max-width: 140px; object-fit: contain;" class="mr-2">
            @endif
            <span>{{ $gymConfig->nombre_gimnasio ?? 'EcoGim' }}</span>
        </a>

        <!-- Desktop: botón colapsar sidebar -->
        <div class="topbar-actions">
            <button id="sidebarToggle" class="topbar-toggle" title="Colapsar menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Logout (derecha) -->
        <div class="topbar-right">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Cerrar Sesion</button>
            </form>
        </div>
    </header>

    <!-- Overlay oscuro (solo móvil, al abrir sidebar) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-inner">
            <ul class="nav flex-column mt-1">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}"
                       title="Dashboard">
                        <i class="fas fa-home"></i>
                        <span class="sidebar-label">Dashboard</span>
                    </a>
                </li>

                <div class="sidebar-heading">Modulos</div>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user') ? 'active' : '' }}"
                       href="{{ route('user') }}"
                       title="Socios / Clientes">
                        <i class="fas fa-users"></i>
                        <span class="sidebar-label">Socios / Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Membresias y Pagos">
                        <i class="fas fa-money-bill-wave"></i>
                        <span class="sidebar-label">Membresias y Pagos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Recepcion Facial">
                        <i class="fas fa-camera"></i>
                        <span class="sidebar-label">Recepcion Facial</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Tienda EcoGim">
                        <i class="fas fa-gift"></i>
                        <span class="sidebar-label">Tienda EcoGim</span>
                    </a>
                </li>

                @if(Auth::user() && Auth::user()->rol === 'admin')
                    <div class="sidebar-heading">Administracion</div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('empleados') ? 'active' : '' }}"
                           href="{{ route('empleados') }}"
                           title="Empleados">
                            <i class="fas fa-user-tie"></i>
                            <span class="sidebar-label">Empleados</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('configuracion.*') ? 'active' : '' }}"
                           href="{{ route('configuracion.index') }}"
                           title="Configuración">
                            <i class="fas fa-cogs"></i>
                            <span class="sidebar-label">Configuración</span>
                        </a>
                    </li>
                @endif

            </ul>
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

            /* ---- Restaurar estado colapsado (solo desktop) ---- */
            if (!isMobile() && localStorage.getItem('sidebar-collapsed') === 'true') {
                body.classList.add('sidebar-collapsed');
            }

            /* ---- Desktop: colapsar/expandir sidebar ---- */
            if (toggle) {
                toggle.addEventListener('click', function () {
                    var collapsed = body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', collapsed);
                });
            }

            /* ---- Mobile: abrir sidebar ---- */
            function openMobileSidebar() {
                body.classList.add('mobile-sidebar-open');
            }
            function closeMobileSidebar() {
                body.classList.remove('mobile-sidebar-open');
            }

            if (mToggle) {
                mToggle.addEventListener('click', openMobileSidebar);
            }

            /* Cerrar al tocar el overlay */
            if (overlay) {
                overlay.addEventListener('click', closeMobileSidebar);
            }

            /* Cerrar al tocar un enlace del sidebar (navega a otra página) */
            document.querySelectorAll('#sidebar .nav-link').forEach(function (link) {
                link.addEventListener('click', closeMobileSidebar);
            });

            /* Al cambiar de tamaño: limpiar estado móvil si pasa a desktop */
            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    closeMobileSidebar();
                    /* Restaurar colapsado si corresponde */
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