@extends('layouts.app')

@section('title', 'Clientes')

@section('skeleton')
    {{-- 4 KPI cards --}}
    <div class="skel-row">
        <div class="skel-box" style="height: 70px; flex: 1;"></div>
        <div class="skel-box" style="height: 70px; flex: 1;"></div>
        <div class="skel-box" style="height: 70px; flex: 1;"></div>
        <div class="skel-box" style="height: 70px; flex: 1;"></div>
    </div>
    {{-- 2-column: tabla izquierda + panel derecho --}}
    <div style="display: flex; gap: 1rem; flex: 1; min-height: 0;">
        <div style="flex: 3; display: flex; flex-direction: column; gap: 0.5rem;">
            <div class="skel-box" style="height: 36px; border-radius: 8px 8px 0 0;"></div>
            <div class="skel-box" style="flex: 1; border-radius: 0 0 8px 8px;"></div>
        </div>
        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.75rem;">
            <div class="skel-box" style="height: 35%;"></div>
            <div class="skel-box" style="flex: 1;"></div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    /* Diseño sin scroll en escritorio */
    body, html { overflow: hidden; height: 100%; }
    
    /* Override de main para que sea flexbox y ocupe el alto disponible exacto */
    #page-wrapper main { 
        padding: 1rem 1.5rem !important; 
        display: flex; 
        flex-direction: column; 
        height: calc(100vh - 60px); /* 60px asumiendo la navbar */
        overflow: hidden;
    }
    
    .page-header { flex-shrink: 0; margin-bottom: 0.75rem !important; }
    .main-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }
    
    :root {
        --ic-accent: var(--primary);
        --card-color: var(--sidebar-bg);
        --ic-green: #10B981;
        --ic-red: #EF4444;
        --ic-muted: #64748B;
    }
    
    /* KPI Cards más delgadas y del mismo color */
    .kpi-card {
        border-radius: 10px;
        border: none;
        padding: 0.6rem 1rem; /* Mucho más delgadas */
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--card-color);
        height: 100%;
    }
    .kpi-icon { font-size: 1.8rem; opacity: 0.4; }
    .kpi-value { font-size: 1.4rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px;}

    .ic-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        padding: 0.85rem;
        display: flex;
        flex-direction: column;
        margin-bottom: 0 !important;
    }
    .ic-card-title { font-weight: 700; font-size: 0.8rem; color: var(--sidebar-bg); margin-bottom: 0.5rem; text-transform: uppercase; }
    
    /* Panel scrollable */
    .table-panel { flex: 1; min-height: 0; overflow-y: auto; padding-right: 5px; }
    
    /* Fix datatables height */
    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dataTables_scroll { flex-grow: 1; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scrollBody { flex-grow: 1; min-height: 0; overflow-y: auto !important; max-height: none !important; height: auto !important; }
    
    .ic-table thead th { font-size: 0.7rem; color: var(--ic-muted); background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 0.4rem 0.5rem; }
    .ic-table td { font-size: 0.8rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #F1F5F9; padding: 0.4rem 0.5rem; }
    .ic-avatar { width: 30px; height: 30px; background: var(--card-color); color: white; font-weight: bold; font-size:0.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .ic-card-icon { color: var(--card-color); }
    
    /* Badges */
    .ic-badge-active, .ic-badge-inactive, .ic-badge-warn, .ic-badge-critical { background: var(--card-color); color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    
    /* List item */
    .ic-list-item { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9; }
    .ic-list-item:last-child { border-bottom: none; }
    .ic-list-title { font-size: 0.8rem; font-weight: 600; color: var(--sidebar-bg); }
    .ic-list-sub { font-size: 0.7rem; color: var(--ic-muted); }

    /* Reducir márgenes de row */
    .row.tight { margin-bottom: 0.75rem; }
</style>
@endpush

@section('content')

<div class="container-fluid main-container">
    
    {{-- ===== ROW 1: KPI Cards ===== --}}
    <div class="row tight flex-shrink-0">
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ number_format($totalClientes ?? 1842) }}</h3>
                    <div class="kpi-label">Total Clientes</div>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ number_format($clientesActivos ?? 1605) }}</h3>
                    <div class="kpi-label">Activos Hoy</div>
                </div>
                <i class="fas fa-user-check kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ number_format($membresiasPorVencer ?? 37) }}</h3>
                    <div class="kpi-label">Por Vencer (7d)</div>
                </div>
                <i class="fas fa-exclamation-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
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
        <div class="col-lg-9 h-100 pb-1">
            <div class="ic-card h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-1 flex-shrink-0">
                    <h5 class="ic-card-title mb-0"><i class="fas fa-list text-primary mr-2"></i> Directorio de Clientes</h5>
                    <a href="{{ route('clientes.create') }}" class="btn btn-sm btn-primary py-1 px-2 font-weight-bold" style="font-size:0.75rem;">
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
                                            <img src="{{ asset('storage/' . $cliente->foto_referencia) }}" class="rounded-circle mr-2" style="width:30px;height:30px;object-fit:cover;">
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
                                    @if($membActiva)
                                        <small class="d-block text-muted mt-1">{{ $membActiva->tipoMembresia->nombre ?? 'Tipo no disponible' }}</small>
                                    @endif
                                </td>
                                <td class="font-weight-bold text-primary">{{ $cliente->puntos_recompensa ?? ($cliente->puntos_ecogim ?? 0) }}</td>
                                <td>
                                    <span class="{{ $cliente->estado === 'activo' ? 'ic-badge-active' : 'ic-badge-inactive' }}">
                                        {{ strtoupper($cliente->estado) }}
                                    </span>
                                </td>
                                <td class="text-center py-1">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light py-0 px-2" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <div class="dropdown-menu dropdown-menu-right" style="font-size:0.8rem;">
                                            <a class="dropdown-item py-1" href="{{ route('clientes.show', $cliente) }}"><i class="fas fa-eye text-info mr-2"></i> Ver</a>
                                            <a class="dropdown-item py-1" href="{{ route('clientes.edit', $cliente) }}"><i class="fas fa-pen text-primary mr-2"></i> Editar</a>
                                            @if(Auth::user() && Auth::user()->rol === 'admin')
                                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline form-delete">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-1 text-danger"><i class="fas fa-trash text-danger mr-2"></i> Eliminar</button>
                                                </form>
                                            @endif
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
        <div class="col-lg-3 h-100 d-flex flex-column pb-1">
            
            {{-- Panel Gráfica Pequeña --}}
            <div class="ic-card flex-shrink-0 mb-2" style="height: 35%;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="ic-card-title mb-0"><i class="fas fa-chart-line text-success mr-1"></i> Crecimiento</span>
                </div>
                <div class="flex-grow-1" style="position: relative; min-height:0;">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            {{-- Panel Membresías por Vencer --}}
            <div class="ic-card flex-grow-1" style="min-height:0;">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0">
                    <span class="ic-card-title mb-0"><i class="fas fa-clock ic-card-icon mr-1"></i> Por Vencer</span>
                    <span class="ic-badge-warn">TOP</span>
                </div>
                <div style="overflow-y:auto; padding-right:4px;">
                    @php
                        // Si no hay $porVencer o está vacío y estamos en pruebas
                        $listaVencer = isset($porVencer) ? $porVencer : [];
                    @endphp
                    @forelse ($listaVencer as $item)
                    <div class="ic-list-item">
                        <div>
                            <div class="ic-list-title">{{ $item->nombre }}</div>
                            <div class="ic-list-sub">{{ $item->plan }}</div>
                        </div>
                        <div>
                            <span class="{{ $item->nivel === 'critical' ? 'ic-badge-critical' : 'ic-badge-warn' }}">
                                {{ $item->dias }} D
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
    $('#mainClientesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        scrollY: '100%',
        scrollCollapse: true,
        info: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row dataTables_scroll'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    var ctx = document.getElementById('growthChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($growthLabels ?? ['1','2','3','4','5','6']) !!},
                datasets: [{
                    data: {!! json_encode($growthData ?? [0,0,0,0,0,0]) !!},
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