<?php
$f = 'c:/laragon/www/GymX/resources/views/layouts/app.blade.php';
$c = file_get_contents($f);

// Replace the Recepción Facial link (href="#") with real route
$old = '<a class="nav-link" href="#" title="Recep';
$new = '<a class="nav-link {{ request()->routeIs(\'asistencias.*\') ? \'active\' : \'\' }}" href="{{ route(\'asistencias.escanear\') }}" title="Escanear Facial';

$c = str_replace($old, $new, $c);

// Also add Pagos link after membresias
$membresiaLink = '<li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(\'membresias.*\') ? \'active\' : \'\' }}" href="{{ route(\'membresias.index\') }}" title="Membres';

$pagosLink = '<li class="nav-item">
                    <a class="nav-link {{ request()->routeIs(\'pagos.*\') ? \'active\' : \'\' }}" href="{{ route(\'pagos.index\') }}" title="Pagos y Cobros">
                        <i class="fas fa-wallet"></i>
                        <span class="sidebar-label">Pagos y Cobros</span>
                    </a>
                </li>
                ' . $membresiaLink;

if (strpos($c, 'pagos.index') === false) {
    $c = str_replace($membresiaLink, $pagosLink, $c);
}

file_put_contents($f, $c);
echo "Sidebar fixed.\n";