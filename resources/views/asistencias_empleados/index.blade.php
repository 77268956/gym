@extends('layouts.app')

@section('title', 'Asistencias de Empleados')

@section('skeleton')
    <div class="skel-box" style="height: 32px; width: 220px; margin-bottom: 1.5rem;"></div>
    <div class="skel-box" style="height: 42px; width: 100%; margin-bottom: 0.5rem; border-radius: 8px 8px 0 0;"></div>
    <div class="skel-box" style="flex: 1; width: 100%; border-radius: 0 0 8px 8px;"></div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
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

    :root { --ic-accent: var(--primary); --card-color: var(--sidebar-bg); }

    .kpi-card {
        border-radius: 10px; border: none; padding: 0.6rem 1rem; color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center;
        background: var(--card-color); height: 100%;
    }
    .kpi-icon  { font-size: 1.8rem; opacity: 0.4; }
    .kpi-value { font-size: 1.4rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px; }

    .ic-card {
        background: #fff; border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        padding: 0.85rem; display: flex; flex-direction: column; margin-bottom: 0 !important;
    }
    .ic-card-title { font-weight: 700; font-size: 0.8rem; color: var(--sidebar-bg); margin-bottom: 0.5rem; text-transform: uppercase; }
    .table-panel { flex: 1 1 0; min-height: 0; min-width: 0; overflow: hidden; padding-right: 5px; display: flex; flex-direction: column; }

    .dataTables_wrapper { display: flex; flex: 1 1 0; min-height: 0; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dt-table-region { flex: 1 1 0; min-height: 0; overflow: hidden; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dt-table-region > .col-sm-12 { display: flex; min-height: 0; }
    .dataTables_scroll { flex: 1 1 0; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scrollBody { flex: 1 1 auto; min-height: 180px; height: calc(100vh - 350px) !important; max-height: calc(100vh - 350px) !important; overflow-y: scroll !important; padding-bottom: 1rem; box-sizing: border-box; }
    #asistenciasEmpleadosTable_wrapper { flex: 1 1 auto; min-height: 0; }
    #asistenciasEmpleadosTable_wrapper .dataTables_scroll { min-height: 0; }
    #asistenciasEmpleadosTable_wrapper .dataTables_scrollHead table,
    #asistenciasEmpleadosTable_wrapper .dataTables_scrollBody table { width: 100% !important; }
    #asistenciasEmpleadosTable_wrapper .dataTables_scrollBody { min-height: 120px; }
    #asistenciasEmpleadosTable_wrapper .pagination .page-item.active .page-link { background-color: var(--primary); border-color: var(--primary); color: #fff; }
    #asistenciasEmpleadosTable_wrapper .pagination .page-link { color: var(--primary); }
    #asistenciasEmpleadosTable_wrapper .pagination .page-item:not(.disabled):not(.active) .page-link:hover { background-color: var(--primary); border-color: var(--primary); color: #fff; }

    .ic-table thead th { font-size: 0.75rem; font-weight: 700; color: var(--primary); background: #eaecf4; border-bottom: 2px solid var(--primary); padding: 0.75rem 0.5rem; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; }
    .ic-table thead th i { display: inline-block; vertical-align: middle; }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #e3e6f0; padding: 0.6rem 0.5rem; color: #5a5c69; }
    .ic-avatar { width: 30px; height: 30px; background: var(--card-color); color: white; font-weight: bold; font-size: 0.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

    .badge-entrada  { background: #D1FAE5; color: #059669; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }
    .badge-salida   { background: #DBEAFE; color: #1D4ED8; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }
    .badge-tardanza { background: #FEF3C7; color: #D97706; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }
    .badge-salida-t { background: #FEE2E2; color: #DC2626; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }

    .row.tight { margin-bottom: 0.75rem; }
    .filters-panel { background: #fff; border-radius: 10px; padding: .75rem 1rem; box-shadow: 0 2px 5px rgba(0,0,0,.04); margin-bottom: .75rem; flex-shrink: 0; }
    .filters-panel .form-control, .filters-panel .custom-select { font-size: .8rem; height: 32px; border-radius: 6px; }
    .attendance-filter-fields { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)) auto; gap: .75rem; align-items: end; }
    .attendance-filter-field { min-width: 0; }
    .attendance-filter-field label { display: block; color: #475569; }
    .attendance-filter-actions { display: flex; align-items: center; justify-content: flex-end; gap: .35rem; white-space: nowrap; }
    .attendance-filter-actions .btn { width: 34px; min-height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    .attendance-toolbar { gap: .75rem; }
    .attendance-toolbar-main { flex: 1 1 420px; min-width: 0; }
    .attendance-search { flex: 1 1 220px; min-width: 180px; }
    .attendance-toolbar-count { flex-shrink: 0; }
    .attendance-filter-summary { display: flex; align-items: center; flex-wrap: wrap; gap: .4rem; margin-top: .55rem; font-size: .75rem; }
    .attendance-filter-summary .badge { padding: .35rem .55rem; border-radius: 50px; color: #475569; }

    @media (max-width: 991.98px) {
        body, html { overflow: auto; height: auto; }
        #page-wrapper main { height: auto; min-height: calc(100vh - 60px); overflow: visible; padding: .75rem !important; }
        .main-container { overflow: visible; min-height: auto; }
        .main-container > .row.flex-grow-1 { height: auto; flex: none !important; }
        .main-container > .row.flex-grow-1 > [class*="col-"] { height: auto !important; min-height: 0; padding-bottom: .75rem !important; }
        .main-container > .row.flex-grow-1 > .col-12 > .ic-card { min-height: 640px; }
        .table-panel { overflow-x: auto; overflow-y: visible; height: auto; min-height: 450px; flex: none; }
        #asistenciasEmpleadosTable_wrapper { width: 100%; min-width: 0; height: auto; min-height: 430px; }
        #asistenciasEmpleadosTable_wrapper .dataTables_scroll { min-width: 900px; height: auto; overflow: visible; }
        #asistenciasEmpleadosTable_wrapper .dataTables_scrollBody { height: 420px !important; max-height: 420px !important; min-height: 0; }
        .ic-card > .d-flex.justify-content-between.align-items-center { flex-wrap: wrap; gap: .65rem; }
        .ic-card > .d-flex.justify-content-between.align-items-center > div { display: flex; flex-wrap: wrap; gap: .5rem; }
        .ic-card > .d-flex.justify-content-between.align-items-center > div:last-child { width: 100%; }
        .ic-card > .d-flex.justify-content-between.align-items-center > div:last-child .btn { flex: 1 1 auto; margin: 0 !important; }
        .main-container > .row.flex-grow-1 { height: auto; flex: none !important; }
        .filters-panel { margin-bottom: .75rem; }
        .attendance-filter-fields { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .attendance-filter-actions { grid-column: 1 / -1; justify-content: flex-start; flex-wrap: wrap; }
        .attendance-toolbar { gap: .75rem; flex-wrap: wrap; }
        .attendance-toolbar-main, .attendance-toolbar-count { width: 100%; }
        .attendance-toolbar-main { flex-wrap: wrap; }
        .attendance-search { width: 100%; margin-top: .5rem; }
    }

    @media (max-width: 575.98px) {
        .kpi-card { min-height: 64px; }
        .kpi-value { font-size: 1.2rem; }
        .kpi-label { font-size: .62rem; }
        .ic-card { padding: .65rem; }
        .ic-card-title { font-size: .72rem; }
        .attendance-filter-fields { grid-template-columns: 1fr; }
        .attendance-filter-actions { grid-column: auto; justify-content: flex-start; }
        .attendance-toolbar-count { text-align: left; }
        #asistenciasEmpleadosTable_wrapper .dataTables_scrollBody { height: 360px !important; max-height: 360px !important; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid main-container">

    {{-- KPI Cards --}}
    <div class="row tight flex-shrink-0">
        <div class="col-md-3 mb-2 mb-md-0">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $asistenciasHoy }}</h3>
                    <div class="kpi-label">Asistencias Hoy</div>
                </div>
                <i class="fas fa-user-check kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $tardanzasHoy }}</h3>
                    <div class="kpi-label">Tardanzas Hoy</div>
                </div>
                <i class="fas fa-clock kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $salidasTempranasHoy }}</h3>
                    <div class="kpi-label">Salidas Tempranas</div>
                </div>
                <i class="fas fa-door-open kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $ausenciasHoy }}</h3>
                    <div class="kpi-label">Ausencias Hoy</div>
                </div>
                <i class="fas fa-user-times kpi-icon"></i>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filters-panel">
        <form method="GET" action="{{ route('asistencias_empleados.index') }}">
            <div class="attendance-filter-fields">
                <div class="attendance-filter-field">
                    <label class="small font-weight-bold mb-1">Empleado</label>
                    <select name="empleado_id" class="custom-select">
                        <option value="">Todos</option>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}" {{ request('empleado_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="attendance-filter-field">
                    <label class="small font-weight-bold mb-1">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="attendance-filter-field">
                    <label class="small font-weight-bold mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="attendance-filter-field">
                    <label class="small font-weight-bold mb-1">Estado</label>
                    <select name="estado" class="custom-select">
                        <option value="">Todos</option>
                        <option value="completo" {{ request('estado') === 'completo' ? 'selected' : '' }}>Completo</option>
                        <option value="en_turno" {{ request('estado') === 'en_turno' ? 'selected' : '' }}>En turno</option>
                        <option value="ausente" {{ request('estado') === 'ausente' ? 'selected' : '' }}>Ausente</option>
                        <option value="tardanza" {{ request('estado') === 'tardanza' ? 'selected' : '' }}>Tardanza</option>
                        <option value="salida_temp" {{ request('estado') === 'salida_temp' ? 'selected' : '' }}>Salida Temprana</option>
                        <option value="cerrado_auto" {{ request('estado') === 'cerrado_auto' ? 'selected' : '' }}>Cerrado Auto</option>
                    </select>
                </div>
                <div class="attendance-filter-actions">
                    <button type="submit" class="btn btn-sm btn-primary" title="Aplicar filtros"><i class="fas fa-filter"></i></button>
                    <a href="{{ route('asistencias_empleados.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros"><i class="fas fa-redo"></i></a>
                    <a href="{{ route('asistencias_empleados.escanear') }}" class="btn btn-sm btn-outline-primary" title="Escáner Interno"><i class="fas fa-camera"></i></a>
                    <a href="{{ route('asistencias_empleados.publico') }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Endpoint Público"><i class="fas fa-external-link-alt"></i></a>
                </div>
            </div>
            @if(request('fecha_desde') || request('fecha_hasta') || request('estado'))
                <div class="attendance-filter-summary">
                    @if(request('fecha_desde') || request('fecha_hasta'))
                        <span class="badge badge-light border">{{ request('fecha_desde', '…') }} → {{ request('fecha_hasta', '…') }}</span>
                    @endif
                    @if(request('estado'))
                        <span class="badge badge-light border">{{ str_replace('_', ' ', request('estado')) }}</span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- Attendance history --}}
    <div class="row flex-grow-1" style="min-height: 0;">
        <div class="col-12 h-100 pb-1">
            <div class="ic-card h-100 d-flex flex-column">
                <div class="attendance-toolbar d-flex justify-content-between align-items-center mb-2 flex-shrink-0 flex-wrap">
                    <div class="attendance-toolbar-main d-flex align-items-center flex-wrap">
                        <h5 class="ic-card-title mb-0 mr-3"><i class="fas fa-clipboard-list text-primary mr-2"></i> Historial de Asistencias — Empleados</h5>
                        <div class="input-group input-group-sm attendance-search">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="searchAsistenciasEmpleados" class="form-control border-left-0" placeholder="Buscar asistencia..." style="background-color: #F8FAFC;">
                        </div>
                    </div>
                    <span class="attendance-toolbar-count badge badge-light text-muted">{{ $asistencias->count() }} registros</span>
                </div>
                <div class="table-panel">
                    <table id="asistenciasEmpleadosTable" class="table ic-table w-100">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-alt mr-1 text-primary"></i> Fecha</th>
                                <th><i class="fas fa-user-tie mr-1 text-primary"></i> Empleado</th>
                                <th><i class="fas fa-sign-in-alt mr-1 text-primary"></i> Entrada</th>
                                <th><i class="fas fa-sign-out-alt mr-1 text-primary"></i> Salida</th>
                                <th><i class="fas fa-hourglass-half mr-1 text-primary"></i> Horas</th>
                                <th><i class="fas fa-toggle-on mr-1 text-primary"></i> Estado</th>
                                <th><i class="fas fa-fingerprint mr-1 text-primary"></i> Método</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asistencias as $asistencia)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($asistencia->fecha)->locale('es')->isoFormat('dddd') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($asistencia->empleado && $asistencia->empleado->foto_referencia)
                                            <img src="{{ asset('storage/' . $asistencia->empleado->foto_referencia) }}"
                                                 class="rounded-circle mr-2" style="width:30px;height:30px;object-fit:cover;">
                                        @else
                                            <div class="ic-avatar mr-2">{{ strtoupper(substr($asistencia->empleado?->nombre ?? '?', 0, 2)) }}</div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $asistencia->empleado?->nombre ?? 'Empleado eliminado' }}</div>
                                            <div class="text-muted small" style="font-size:0.68rem;">{{ ucfirst($asistencia->empleado?->rol ?? '') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($asistencia->hora_entrada)
                                        <span class="badge-entrada">
                                            <i class="fas fa-sign-in-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($asistencia->hora_entrada)->format('h:i A') }}
                                        </span>
                                        @if($asistencia->tardanza)
                                            <span class="badge-tardanza ml-1">Tardanza</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asistencia->hora_salida)
                                        <span class="badge-salida">
                                            <i class="fas fa-sign-out-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($asistencia->hora_salida)->format('h:i A') }}
                                        </span>
                                        @if($asistencia->salida_temprana)
                                            <span class="badge-salida-t ml-1">Temprana</span>
                                        @endif
                                    @elseif($asistencia->salida_no_registrada)
                                        <span class="text-danger small"><i class="fas fa-exclamation-circle mr-1"></i>No registrada</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asistencia->horas_trabajadas)
                                        <span class="font-weight-bold text-dark">{{ number_format($asistencia->horas_trabajadas, 1) }}h</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asistencia->metodo_registro === 'sistema' && !$asistencia->hora_entrada)
                                        {{-- Ausencia registrada automáticamente --}}
                                        <span style="background:#FEE2E2;color:#DC2626;padding:3px 8px;border-radius:50px;font-size:0.7rem;font-weight:600;"><i class="fas fa-user-times mr-1"></i>Ausente</span>
                                    @elseif($asistencia->hora_entrada && $asistencia->hora_salida && $asistencia->salida_no_registrada)
                                        {{-- Salida cerrada automáticamente --}}
                                        <span style="background:#E0E7FF;color:#4338CA;padding:3px 8px;border-radius:50px;font-size:0.7rem;font-weight:600;"><i class="fas fa-robot mr-1"></i>Cerrado auto</span>
                                    @elseif($asistencia->hora_entrada && $asistencia->hora_salida)
                                        <span style="background:#D1FAE5;color:#059669;padding:3px 8px;border-radius:50px;font-size:0.7rem;font-weight:600;"><i class="fas fa-check mr-1"></i>Completo</span>
                                    @elseif($asistencia->hora_entrada)
                                        <span style="background:#FEF3C7;color:#D97706;padding:3px 8px;border-radius:50px;font-size:0.7rem;font-weight:600;"><i class="fas fa-clock mr-1"></i>En turno</span>
                                    @else
                                        <span style="background:#F1F5F9;color:#64748B;padding:3px 8px;border-radius:50px;font-size:0.7rem;font-weight:600;">—</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    @if($asistencia->metodo_registro === 'sistema')
                                        <i class="fas fa-robot mr-1 text-secondary"></i> Sistema
                                    @else
                                        <i class="fas fa-camera mr-1 text-primary"></i> {{ ucfirst($asistencia->metodo_registro ?? '—') }}
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
    var tableAsistencias = $('#asistenciasEmpleadosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        scrollY: 'calc(100vh - 350px)',
        scrollCollapse: true,
        info: true,
        order: [[0, 'desc']],
        dom: "<'row dt-table-region'<'col-sm-12'tr>>" +
             "<'row mt-2 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>"
    });

    $('#searchAsistenciasEmpleados').on('keyup', function() {
        tableAsistencias.search(this.value).draw();
    });
});
</script>
@endpush
