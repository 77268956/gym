<?php
$f = 'c:/laragon/www/GymX/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

$new_menus = <<<'EOD'
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('membresias.*') ? 'active' : '' }}" href="{{ route('membresias.index') }}" title="Planes de Membresía">
                        <i class="fas fa-id-card"></i>
                        <span class="sidebar-label">Planes de Membresía</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}" href="{{ route('pagos.index') }}" title="Pagos y Cobros">
                        <i class="fas fa-wallet"></i>
                        <span class="sidebar-label">Pagos y Cobros</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('asistencias.escanear') ? 'active' : '' }}" href="{{ route('asistencias.escanear') }}" title="Escanear Facial">
                        <i class="fas fa-camera"></i>
                        <span class="sidebar-label">Escanear Facial</span>
                    </a>
                </li>
EOD;

$c = preg_replace('/<li class="nav-item">\s*<a class="nav-link[^>]*href="{{ route\(\'membresias\.index\'\).*?<\/li>\s*<li class="nav-item">\s*<a class="nav-link" href="#" title="Recep[^"]*".*?<\/li>/is', $new_menus, $c);

file_put_contents($f, $c);
echo "Sidebar updated.\n";