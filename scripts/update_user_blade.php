<?php
$f = 'c:/laragon/www/GymX/resources/views/user.blade.php';

$blade = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Socios / Clientes')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    /* Diseño sin scroll en escritorio */
    body, html { overflow: hidden; height: 100%; }
    .content-wrapper { height: calc(100vh - 56px); overflow: hidden; display: flex; flex-direction: column; padding-bottom: 0; }
    .main-container { flex: 1; overflow: hidden; padding-bottom: 1rem; }
    
    :root {
        --ic-accent: #2563EB;
        --ic-green: #10B981;
        --ic-red: #EF4444;
        --ic-muted: #64748B;
    }
    
    .kpi-card {
        border-radius: 12px;
        border: none;
        padding: 1.25rem;
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s;
    }
    .kpi-card:hover { transform: translateY(-3px); }
    .kpi-card-1 { background: linear-gradient(135deg, #2563EB, #1D4ED8); }
    .kpi-card-2 { background: linear-gradient(135deg, #10B981, #059669); }
    .kpi-card-3 { background: linear-gradient(135deg, #F59E0B, #D97706); }
    .kpi-card-4 { background: linear-gradient(135deg, #8B5CF6, #6D28D9); }
    
    .kpi-icon {
        font-size: 2.5rem;
        opacity: 0.5;
    }
    .kpi-value {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }
    .kpi-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.9;
    }

    .ic-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
        padding: 1rem;
        display: flex;
        flex-direction: column;
    }
    .ic-card-title { font-weight: 700; font-size: 0.9rem; color: #1E293B; margin-bottom: 0.5rem; text-transform: uppercase; }
    
    /* Panel scrollable para la tabla */
    .table-panel { height: 100%; overflow-y: auto; }
    
    .ic-table thead th {
        font-size: 0.75rem;
        color: var(--ic-muted);
        background: #F8FAFC;
        border-bottom: 2px solid #E2E8F0;
        white-space: nowrap;
    }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #F1F5F9; }
    .ic-avatar { width: 35px; height: 35px; background: #E0E7FF; color: #3730A3; font-weight: bold; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    
    /* Badges */
    .ic-badge-active { background: #D1FAE5; color: #059669; padding: 4px 10px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .ic-badge-inactive { background: #F1F5F9; color: #475569; padding: 4px 10px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .ic-badge-warn { background: #FEF3C7; color: #D97706; padding: 4px 10px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .ic-badge-critical { background: #FEE2E2; color: #EF4444; padding: 4px 10px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    
    /* List item */
    .ic-list-item { display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid #F1F5F9; }
    .ic-list-item:last-child { border-bottom: none; }
    .ic-list-title { font-size: 0.85rem; font-weight: 600; color: #1E293B; }
    .ic-list-sub { font-size: 0.75rem; color: var(--ic-muted); }

    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
</style>
@endpush

@section('content')
<!-- content-wrapper force -->
<script>
    document.querySelector('.content-wrapper') && document.querySelector('.content-wrapper').classList.add('d-flex', 'flex-column');
</script>

<div class="container-fluid main-container d-flex flex-column pt-3">
    
    {{-- ===== ROW 1: KPI Cards ===== --}}
    <div class="row mb-3 flex-shrink-0">
        <div class="col-md-3">
            <div class="kpi-card kpi-card-1">
                <div>
                    <h3 class="kpi-value">{{ number_format($totalClientes ?? 1842) }}</h3>
                    <div class="kpi-label">Total Clientes</div>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-card-2">
                <div>
                    <h3 class="kpi-value">{{ number_format($clientesActivos ?? 1605) }}</h3>
                    <div class="kpi-label">Activos Hoy</div>
                </div>
                <i class="fas fa-user-check kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-card-3">
                <div>
                    <h3 class="kpi-value">{{ number_format($membresiasPorVencer ?? 37) }}</h3>
                    <div class="kpi-label">Por Vencer (7d)</div>
                </div>
                <i class="fas fa-exclamation-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card kpi-card-4">
                <div>
                    <h3 class="kpi-value">{{ number_format($nuevosClientes ?? 158) }}</h3>
                    <div class="kpi-label">Nuevos Este Mes</div>
                </div>
                <i class="fas fa-user-plus kpi-icon"></i>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: Main Layout ===== --}}
    <div class="row flex-grow-1" style="min-height: 0;">
        
        {{-- COLUMNA IZQUIERDA: TABLA PRINCIPAL --}}
        <div class="col-lg-8 h-100 pb-2">
            <div class="ic-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
                    <h5 class="ic-card-title mb-0"><i class="fas fa-list text-primary mr-2"></i> Directorio de Clientes</h5>
                    <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-primary font-weight-bold">
                        <i class="fas fa-plus mr-1"></i> Nuevo Cliente
                    </a>
                </div>
                
                <div class="table-panel">
                    <table id="mainClientesTable" class="table ic-table w-100">
                        <thead>
                            <tr>
                                <th>CLIENTE</th>
                                <th>CÉDULA</th>
                                <th>TELÉFONO</th>
                                <th>MEMBRESÍA</th>
                                <th>PUNTOS</th>
                                <th>ESTADO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($cliente->foto_referencia)
                                            <img src="{{ asset('storage/' . $cliente->foto_referencia) }}" class="rounded-circle mr-2" style="width:35px;height:35px;object-fit:cover;">
                                        @else
                                            <div class="ic-avatar mr-2">{{ strtoupper(substr($cliente->nombre, 0, 2)) }}</div>
                                        @endif
                                        <div class="font-weight-bold text-dark">{{ $cliente->nombre }}</div>
                                    </div>
                                </td>
                                <td>{{ $cliente->cedula }}</td>
                                <td>{{ $cliente->telefono ?? '—' }}</td>
                                <td>
                                    @php
                                        $membActiva = $cliente->membresias->first();
                                        if ($membActiva) {
                                            $mLabel = 'ACTIVA'; $mClass = 'ic-badge-active';
                                        } else {
                                            $tieneVencida = $cliente->membresias()->where('estado', 'vencida')->exists();
                                            $mLabel = $tieneVencida ? 'VENCIDA' : 'SIN MEMBRESÍA';
                                            $mClass = $tieneVencida ? 'ic-badge-critical' : 'ic-badge-inactive';
                                        }
                                    @endphp
                                    <span class="{{ $mClass }}">{{ $mLabel }}</span>
                                </td>
                                <td class="font-weight-bold text-primary">{{ $cliente->puntos_recompensa ?? ($cliente->puntos_ecogim ?? 0) }}</td>
                                <td>
                                    <span class="{{ $cliente->estado === 'activo' ? 'ic-badge-active' : 'ic-badge-inactive' }}">
                                        {{ strtoupper($cliente->estado) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="{{ route('clientes.show', $cliente) }}"><i class="fas fa-eye text-info mr-2"></i> Ver</a>
                                            <a class="dropdown-item" href="{{ route('clientes.edit', $cliente) }}"><i class="fas fa-pen text-primary mr-2"></i> Editar</a>
                                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline form-delete">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash text-danger mr-2"></i> Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No hay clientes registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: SECUNDARIAS --}}
        <div class="col-lg-4 h-100 d-flex flex-column pb-2">
            
            {{-- Panel Gráfica Pequeña --}}
            <div class="ic-card flex-shrink-0 mb-3" style="height: 35%;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="ic-card-title mb-0"><i class="fas fa-chart-line text-success mr-2"></i> Crecimiento</span>
                </div>
                <div class="flex-grow-1" style="position: relative;">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            {{-- Panel Membresías por Vencer --}}
            <div class="ic-card flex-grow-1" style="overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="ic-card-title mb-0"><i class="fas fa-clock text-warning mr-2"></i> Por Vencer</span>
                    <span class="ic-badge-warn">TOP</span>
                </div>
                <div>
                    @php
                        $listaVencer = $porVencer ?? [
                            (object)['nombre' => 'Logan Cole',   'plan' => 'Black Pass Anual',    'dias' => 2, 'nivel' => 'critical'],
                            (object)['nombre' => 'Diana Prince', 'plan' => 'Standard Mensual',    'dias' => 3, 'nivel' => 'critical'],
                            (object)['nombre' => 'Roy Harper',   'plan' => 'Performance Tier',    'dias' => 5, 'nivel' => 'warn'],
                            (object)['nombre' => 'Selina Kyle',  'plan' => 'Black Pass Mensual',  'dias' => 5, 'nivel' => 'warn'],
                        ];
                    @endphp
                    @forelse ($listaVencer as $item)
                    <div class="ic-list-item">
                        <div>
                            <div class="ic-list-title">{{ $item->nombre }}</div>
                            <div class="ic-list-sub">{{ $item->plan }}</div>
                        </div>
                        <div>
                            <span class="{{ $item->nivel === 'critical' ? 'ic-badge-critical' : 'ic-badge-warn' }}">
                                {{ $item->dias }} DÍAS
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">No hay membresías por vencer.</p>
                    @endforelse
                </div>
            </div>
            
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
$(document).ready(function() {
    // Configuración para que el wrapper no haga scroll, sino la tabla misma internamente
    $('#mainClientesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        scrollY: "calc(100vh - 280px)",
        scrollCollapse: true,
        info: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    // Gráfico pequeño
    var ctx = document.getElementById('growthChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['E','F','M','A','M','J','J','A','S','O','N','D'],
                datasets: [{
                    data: [120,128,131,135,141,148,152,156,161,168,175,184],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    pointRadius: 0,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { grid: { display: false }, ticks: { font: { size: 9 } } }
                }
            }
        });
    }
});
</script>
@endpush
BLADE;

file_put_contents($f, $blade);
echo "New UI applied.";