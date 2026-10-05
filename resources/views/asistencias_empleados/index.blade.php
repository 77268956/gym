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
    .table-panel { flex: 1; min-height: 0; overflow-y: auto; padding-right: 5px; }

    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dataTables_scroll { flex-grow: 1; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scrollBody { flex-grow: 1; min-height: 0; overflow-y: auto !important; max-height: none !important; height: auto !important; }

    .ic-table thead th { font-size: 0.7rem; color: #64748B; background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 0.4rem 0.5rem; }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #e3e6f0; padding: 0.6rem 0.5rem; color: #5a5c69; }
    .ic-avatar { width: 30px; height: 30px; background: var(--card-color); color: white; font-weight: bold; font-size: 0.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

    .badge-entrada  { background: #D1FAE5; color: #059669; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }
    .badge-salida   { background: #DBEAFE; color: #1D4ED8; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }
    .badge-tardanza { background: #FEF3C7; color: #D97706; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }
    .badge-salida-t { background: #FEE2E2; color: #DC2626; padding: 3px 9px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }

    .row.tight { margin-bottom: 0.75rem; }
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

    {{-- Table --}}
    <div class="row flex-grow-1" style="min-height: 0;">
        <div class="col-12 h-100 pb-1">
            <div class="ic-card h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-1 flex-shrink-0">
                    <h5 class="ic-card-title mb-0">
                        <i class="fas fa-clipboard-list text-primary mr-2"></i> Historial de Asistencias — Empleados
                    </h5>
                    <div>
                        <a href="{{ route('asistencias_empleados.escanear') }}" class="btn btn-sm btn-primary mr-2">
                            <i class="fas fa-camera mr-1"></i> Escáner Interno
                        </a>
                        <a href="{{ route('asistencias_empleados.publico') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-external-link-alt mr-1"></i> Endpoint Público
                        </a>
                    </div>
                </div>
                {{-- Filter Bar --}}
                <form method="GET" action="{{ route('asistencias_empleados.index') }}" class="mb-2 flex-shrink-0">
                    <div class="row align-items-end" style="gap: 0.25rem 0;">
                        <div class="col-md-3 col-6 mb-1">
                            <label class="small font-weight-bold text-muted mb-0" style="font-size:0.7rem;">EMPLEADO</label>
                            <select name="empleado_id" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                @foreach($empleados as $emp)
                                    <option value="{{ $emp->id }}" {{ request('empleado_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-6 mb-1">
                            <label class="small font-weight-bold text-muted mb-0" style="font-size:0.7rem;">DESDE</label>
                            <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="col-md-2 col-6 mb-1">
                            <label class="small font-weight-bold text-muted mb-0" style="font-size:0.7rem;">HASTA</label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="col-md-2 col-6 mb-1">
                            <label class="small font-weight-bold text-muted mb-0" style="font-size:0.7rem;">ESTADO</label>
                            <select name="estado" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                <option value="completo"     {{ request('estado') === 'completo'     ? 'selected' : '' }}>Completo</option>
                                <option value="en_turno"     {{ request('estado') === 'en_turno'     ? 'selected' : '' }}>En turno</option>
                                <option value="ausente"      {{ request('estado') === 'ausente'      ? 'selected' : '' }}>Ausente</option>
                                <option value="tardanza"     {{ request('estado') === 'tardanza'     ? 'selected' : '' }}>Tardanza</option>
                                <option value="salida_temp"  {{ request('estado') === 'salida_temp'  ? 'selected' : '' }}>Salida Temprana</option>
                                <option value="cerrado_auto" {{ request('estado') === 'cerrado_auto' ? 'selected' : '' }}>Cerrado Auto</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-12 mb-1 d-flex align-items-end" style="gap:0.4rem;">
                            <button type="submit" class="btn btn-primary btn-sm font-weight-bold flex-grow-1">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                            @if(request()->hasAny(['empleado_id','fecha_desde','fecha_hasta','estado']))
                                <a href="{{ route('asistencias_empleados.index') }}" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Resultado activo --}}
                    <div class="d-flex align-items-center mt-1" style="gap:0.4rem; font-size:0.75rem;">
                        <span class="text-muted">
                            <strong>{{ $asistencias->count() }}</strong> registro(s) encontrado(s)
                        </span>
                        @if(request()->hasAny(['empleado_id','fecha_desde','fecha_hasta','estado']))
                            @if(request('fecha_desde') || request('fecha_hasta'))
                                <span class="badge badge-light border">
                                    📅 {{ request('fecha_desde', '…') }} → {{ request('fecha_hasta', '…') }}
                                </span>
                            @endif
                            @if(request('estado'))
                                <span class="badge badge-light border">{{ request('estado') }}</span>
                            @endif
                        @endif
                    </div>
                </form>

                <div class="table-panel">
                    <table id="asistenciasEmpleadosTable" class="table ic-table w-100">
                        <thead>
                            <tr>
                                <th>FECHA</th>
                                <th>EMPLEADO</th>
                                <th>ENTRADA</th>
                                <th>SALIDA</th>
                                <th>HORAS</th>
                                <th>ESTADO</th>
                                <th>MÉTODO</th>
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
    $('#asistenciasEmpleadosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        scrollY: '100%',
        scrollCollapse: true,
        info: true,
        order: [[0, 'desc']],
        dom: "<'row mb-2'<'col-sm-12 text-right'f>>" +
             "<'row dataTables_scroll'<'col-sm-12'tr>>" +
             "<'row mt-2 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>"
    });
});
</script>
@endpush
