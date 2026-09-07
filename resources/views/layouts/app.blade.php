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
            min-height: calc(100vh - 56px);
            padding: 84px 28px 32px;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: -18px;
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
            letter-spacing: 0;
        }
        @media (max-width: 767.98px) {
            main {
                padding: 76px 16px 24px;
            }
            .page-header {
                margin-top: -10px;
                margin-bottom: 18px;
            }
            .page-title {
                font-size: 1.5rem;
            }
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
                            <a class="nav-link {{ request()->routeIs('user') ? 'active' : '' }}" href="{{ route('user') }}">
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
                            <a class="nav-link {{ request()->routeIs('empleados') ? 'active' : '' }}" href="{{ route('empleados') }}">
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

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10">
                <header class="page-header">
                    <h1 class="page-title">@yield('title')</h1>
                </header>
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