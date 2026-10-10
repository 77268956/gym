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
    
    .table-panel { flex: 1 1 0; min-height: 0; min-width: 0; overflow: hidden; padding-right: 5px; display: flex; flex-direction: column; }

    .dataTables_wrapper { display: flex; flex: 1 1 0; min-height: 0; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dt-table-region { flex: 1 1 0; min-height: 0; overflow: hidden; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dt-table-region > .col-sm-12 { display: flex; min-height: 0; }
    .dataTables_scroll { flex: 1 1 0; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scrollBody { flex: 1 1 auto; min-height: 180px; height: calc(100vh - 350px) !important; max-height: calc(100vh - 350px) !important; overflow-y: scroll !important; padding-bottom: 1rem; box-sizing: border-box; }
    #mainAsistenciasTable_wrapper { flex: 1 1 auto; min-height: 0; }
    #mainAsistenciasTable_wrapper .dataTables_scroll { min-height: 0; }
    #mainAsistenciasTable_wrapper .dataTables_scrollBody { min-height: 120px; }
    #mainAsistenciasTable_wrapper .pagination .page-item.active .page-link { background-color: var(--primary); border-color: var(--primary); color: #fff; }
    #mainAsistenciasTable_wrapper .pagination .page-link { color: var(--primary); }
    #mainAsistenciasTable_wrapper .pagination .page-item:not(.disabled):not(.active) .page-link:hover { background-color: var(--primary); border-color: var(--primary); color: #fff; }
    
    .ic-table thead th { font-size: 0.75rem; font-weight: 700; color: var(--primary); background: #eaecf4; border-bottom: 2px solid var(--primary); padding: 0.75rem 0.5rem; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; }
    .ic-table thead th i { display: inline-block; vertical-align: middle; }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #e3e6f0; padding: 0.6rem 0.5rem; color: #5a5c69; }
    .ic-avatar { width: 30px; height: 30px; background: var(--card-color); color: white; font-weight: bold; font-size:0.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    
    .ic-badge-active, .ic-badge-inactive { background: var(--card-color); color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    
    .row.tight { margin-bottom: 0.75rem; }

    @media (max-width: 991.98px) {
        body, html { overflow: auto; height: auto; }
        #page-wrapper main { height: auto; min-height: calc(100vh - 60px); overflow: visible; padding: .75rem !important; }
        .main-container { overflow: visible; min-height: auto; }
        .main-container > .row.flex-grow-1 { height: auto; flex: none !important; }
        .main-container > .row.flex-grow-1 > [class*="col-"] { height: auto !important; min-height: 0; padding-bottom: .75rem !important; }
        .main-container > .row.flex-grow-1 > .col-12 > .ic-card { min-height: 540px; }
        .table-panel { overflow-x: auto; overflow-y: visible; height: auto; min-height: 450px; flex: none; }
        #mainAsistenciasTable_wrapper { width: 100%; min-width: 0; height: auto; min-height: 430px; }
        #mainAsistenciasTable_wrapper .dataTables_scroll { min-width: 760px; height: auto; overflow: visible; }
        #mainAsistenciasTable_wrapper .dataTables_scrollBody { height: 420px !important; max-height: 420px !important; min-height: 0; }
        .ic-card > .d-flex.justify-content-between.align-items-center { flex-wrap: wrap; gap: .65rem; }
        .ic-card > .d-flex.justify-content-between.align-items-center > div { width: 100%; flex-wrap: wrap; gap: .5rem; }
        #customSearch { width: 100%; }
    }

    @media (max-width: 575.98px) {
        .ic-card { padding: .65rem; }
        .ic-card-title { font-size: .72rem; }
        #mainAsistenciasTable_wrapper .dataTables_scrollBody { height: 360px !important; max-height: 360px !important; }
    }
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
                <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0">
                    <div class="d-flex align-items-center">
                        <h5 class="ic-card-title mb-0 mr-3"><i class="fas fa-clipboard-list text-primary mr-2"></i> Historial de Accesos</h5>
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="customSearch" class="form-control border-left-0" placeholder="Buscar acceso..." style="background-color: #F8FAFC;">
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <select id="filterEstado" class="form-control form-control-sm mr-2" style="width: 115px;">
                            <option value="">Todos los estados</option>
                            <option value="EXITOSO">Exitosos</option>
                            <option value="FALLIDO">Fallidos</option>
                        </select>
                        <input type="date" id="filterFechaDesde" class="form-control form-control-sm mr-2" title="Fecha desde" style="width: 135px;">
                        <input type="date" id="filterFechaHasta" class="form-control form-control-sm" title="Fecha hasta" style="width: 135px;">
                        <a href="{{ route('asistencias.escanear') }}" class="btn btn-sm btn-primary font-weight-bold px-3 ml-2" title="Escanear Acceso">
                            <i class="fas fa-qrcode mr-1"></i> Escanear Acceso
                        </a>
                    </div>
                </div>
                
                <div class="table-panel">
                    <table id="mainAsistenciasTable" class="table ic-table w-100">
                        <thead>
                            <tr>
                                <th class="text-uppercase"><i class="fas fa-calendar-alt mr-1 text-primary"></i> Fecha y hora</th>
                                <th class="text-uppercase"><i class="fas fa-user mr-1 text-primary"></i> Cliente</th>
                                <th class="text-uppercase"><i class="fas fa-id-card mr-1 text-primary"></i> Cédula</th>
                                <th class="text-uppercase"><i class="fas fa-toggle-on mr-1 text-primary"></i> Estado</th>
                                <th class="text-uppercase"><i class="fas fa-info-circle mr-1 text-primary"></i> Motivo</th>
                                <th class="text-center text-uppercase"><i class="fas fa-cogs mr-1 text-primary"></i> Acciones</th>
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
                                            <div class="ic-avatar mr-2">{{ strtoupper(substr($asistencia->cliente->nombre_completo ?? '?', 0, 2)) }}</div>
                                        @endif
                                        <div class="font-weight-bold text-dark">{{ $asistencia->cliente->nombre_completo ?? 'Cliente Eliminado' }}</div>
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
    var table = $('#mainAsistenciasTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        scrollY: 'calc(100vh - 350px)',
        scrollCollapse: true,
        info: true,
        order: [[0, "desc"]], // Ordenar por fecha por defecto
        dom: "<'row dt-table-region'<'col-sm-12'tr>>" +
             "<'row mt-2 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>"
    });

    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#filterEstado').on('change', function() {
        table.column(3).search(this.value).draw();
    });

    $.fn.dataTable.ext.search.push(function(settings, data) {
        if (settings.nTable.id !== 'mainAsistenciasTable') {
            return true;
        }

        var dateParts = data[0].split('/');
        var rowDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
        var fromValue = $('#filterFechaDesde').val();
        var toValue = $('#filterFechaHasta').val();
        var fromDate = fromValue ? new Date(fromValue + 'T00:00:00') : null;
        var toDate = toValue ? new Date(toValue + 'T23:59:59') : null;

        return (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);
    });

    $('#filterFechaDesde, #filterFechaHasta').on('change', function() {
        table.draw();
    });
});
</script>
@endpush
