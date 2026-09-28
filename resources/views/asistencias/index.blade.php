@extends('layouts.app')

@section('title', 'Bitácora de Accesos')

@section('skeleton')
    <div class="skel-box" style="height: 32px; width: 220px; margin-bottom: 1.5rem;"></div>
    <div class="skel-box" style="height: 42px; width: 100%; margin-bottom: 0.5rem; border-radius: 8px 8px 0 0;"></div>
    <div class="skel-box" style="flex: 1; width: 100%; border-radius: 0 0 8px 8px;"></div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    /* Diseño sin scroll en escritorio */
    body, html { overflow: hidden; height: 100%; }
    
    #page-wrapper main { 
        padding: 1rem 1.5rem !important; 
        display: flex; 
        flex-direction: column; 
        height: calc(100vh - 60px);
        overflow: hidden;
    }
    
    .page-header { flex-shrink: 0; margin-bottom: 0.75rem !important; }
    .main-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }
    
    :root {
        --ic-accent: var(--primary);
        --card-color: var(--sidebar-bg);
    }
    
    .kpi-card {
        border-radius: 10px;
        border: none;
        padding: 0.6rem 1rem;
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
    
    .table-panel { flex: 1; min-height: 0; overflow-y: auto; padding-right: 5px; }
    
    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dataTables_scroll { flex-grow: 1; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scrollBody { flex-grow: 1; min-height: 0; overflow-y: auto !important; max-height: none !important; height: auto !important; }
    
    .ic-table thead th { font-size: 0.7rem; color: #64748B; background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 0.4rem 0.5rem; }
    .ic-table td { font-size: 0.8rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #F1F5F9; padding: 0.4rem 0.5rem; }
    .ic-avatar { width: 30px; height: 30px; background: var(--card-color); color: white; font-weight: bold; font-size:0.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    
    .ic-badge-active { background: #10B981; color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    .ic-badge-inactive { background: #EF4444; color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    
    .row.tight { margin-bottom: 0.75rem; }
</style>
@endpush

@section('content')
<div class="container-fluid main-container">
    
    {{-- ===== ROW 1: KPI Cards ===== --}}
    <div class="row tight flex-shrink-0">
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $accesosHoy ?? 0 }}</h3>
                    <div class="kpi-label">Accesos Hoy</div>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $exitososHoy ?? 0 }}</h3>
                    <div class="kpi-label">Exitosos</div>
                </div>
                <i class="fas fa-check-circle kpi-icon text-success"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $fallidosHoy ?? 0 }}</h3>
                    <div class="kpi-label">Rechazados</div>
                </div>
                <i class="fas fa-times-circle kpi-icon text-danger"></i>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: Main Layout ===== --}}
    <div class="row flex-grow-1" style="min-height: 0;">
        <div class="col-12 h-100 pb-1">
            <div class="ic-card h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-1 flex-shrink-0">
                    <h5 class="ic-card-title mb-0"><i class="fas fa-clipboard-list text-primary mr-2"></i> Historial de Accesos</h5>
                </div>
                
                <div class="table-panel">
                    <table id="mainAsistenciasTable" class="table ic-table w-100">
                        <thead>
                            <tr>
                                <th>FECHA Y HORA</th>
                                <th>CLIENTE</th>
                                <th>CÉDULA</th>
                                <th>ESTADO</th>
                                <th>MOTIVO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asistencias as $asistencia)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($asistencia->hora)->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($asistencia->cliente && $asistencia->cliente->foto_referencia)
                                            <img src="{{ asset('storage/' . $asistencia->cliente->foto_referencia) }}" class="rounded-circle mr-2" style="width:30px;height:30px;object-fit:cover;">
                                        @else
                                            <div class="ic-avatar mr-2">{{ strtoupper(substr($asistencia->cliente->nombre ?? '?', 0, 2)) }}</div>
                                        @endif
                                        <div class="font-weight-bold text-dark">{{ $asistencia->cliente->nombre ?? 'Cliente Eliminado' }}</div>
                                    </div>
                                </td>
                                <td>{{ $asistencia->cliente->cedula ?? '-' }}</td>
                                <td>
                                    @if($asistencia->exitoso)
                                        <span class="ic-badge-active">EXITOSO</span>
                                    @else
                                        <span class="ic-badge-inactive">FALLIDO</span>
                                    @endif
                                </td>
                                <td class="text-muted">
                                    {{ $asistencia->motivo_rechazo ?? '-' }}
                                </td>
                                <td class="text-center py-1">
                                    @if($asistencia->cliente)
                                        <a href="{{ route('clientes.show', $asistencia->cliente_id) }}" class="btn btn-sm btn-light py-0 px-2" title="Ver Expediente">
                                            <i class="fas fa-folder-open text-primary"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#mainAsistenciasTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        scrollY: '100%',
        scrollCollapse: true,
        info: true,
        order: [[0, "desc"]], // Ordenar por fecha por defecto
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row dataTables_scroll'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });
});
</script>
@endpush
