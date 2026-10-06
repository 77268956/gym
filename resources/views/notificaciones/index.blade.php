@extends('layouts.app')

@section('title', 'Centro de Alertas')

@push('styles')
<style>
    .alerts-page { padding: 1rem 1.5rem 1.5rem; }
    .alerts-page .page-header { margin-bottom: .75rem; }
    .alerts-page .kpi-card {
        min-height: 82px; padding: .75rem 1rem; border: 0; border-radius: 10px;
        color: #fff; background: var(--sidebar-bg); box-shadow: 0 4px 10px rgba(0,0,0,.1);
        display: flex; align-items: center; justify-content: space-between;
    }
    .alerts-page .kpi-label { color: rgba(255,255,255,.8); font-size: .7rem; font-weight: 700; text-transform: uppercase; }
    .alerts-page .kpi-value { margin: 0; color: #fff; font-size: 1.4rem; font-weight: 800; line-height: 1.1; }
    .alerts-page .kpi-icon { color: rgba(255,255,255,.45); font-size: 1.6rem; }
    .alerts-page .ic-card { background: #fff; border: 0; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,.04); }
    .alerts-page .filters-panel { padding: .85rem 1rem; }
    .alerts-page .filters-panel label { margin-bottom: .2rem; font-size: .68rem; }
    .alerts-page .form-control { border-radius: 6px; font-size: .8rem; }
    .alerts-page .ic-table { margin-bottom: 0; }
    .alerts-page .ic-table thead th {
        padding: .75rem .65rem; color: var(--primary); background: #eaecf4;
        border-bottom: 2px solid var(--primary); font-size: .72rem; font-weight: 700;
        letter-spacing: .04em; white-space: nowrap;
    }
    .alerts-page .ic-table td { padding: .7rem .65rem; color: #5a5c69; font-size: .82rem; vertical-align: middle; }
    .alerts-page .alert-message { min-width: 220px; white-space: normal; color: #334155; }
    .alerts-page .alert-status { display: inline-flex; align-items: center; gap: .35rem; font-size: .75rem; font-weight: 700; white-space: nowrap; }
    .alerts-page .alert-status.pending { color: #b7791f; }
    .alerts-page .alert-status.done { color: #059669; }
    .alerts-page .alert-type { color: #475569; font-weight: 600; }
    .alerts-page .pagination { margin-bottom: 0; }
    .alerts-page .page-link { color: var(--primary); }
    .alerts-page .page-item.active .page-link { color: #fff; background: var(--primary); border-color: var(--primary); }
    @media (max-width: 767.98px) {
        .alerts-page { padding: .75rem; }
        .alerts-page .kpi-card { min-height: 70px; }
        .alerts-page .kpi-icon { font-size: 1.25rem; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid alerts-page">
        <span class="small text-muted mt-2 mt-md-0">Hoy · {{ now()->format('d/m/Y') }}</span>

    <div class="row mb-3">
        <div class="col-md-4 mb-2 mb-md-0">
            <div class="kpi-card">
                <div><div class="kpi-label">Todas las alertas</div><p class="kpi-value">{{ number_format($conteos['total']) }}</p></div>
                <i class="fas fa-bell kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4 mb-2 mb-md-0">
            <div class="kpi-card">
                <div><div class="kpi-label">Pendientes</div><p class="kpi-value">{{ number_format($conteos['pendientes']) }}</p></div>
                <i class="fas fa-clock kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <div><div class="kpi-label">Atendidas</div><p class="kpi-value">{{ number_format($conteos['atendidas']) }}</p></div>
                <i class="fas fa-check-circle kpi-icon"></i>
            </div>
        </div>
    </div>

    <div class="ic-card filters-panel mb-3">
        <form method="GET" action="{{ route('notificaciones.index') }}">
            <div class="form-row align-items-end">
                <div class="form-group col-xl-2 col-md-6 mb-2">
                    <label for="tipo" class="font-weight-bold text-muted">TIPO DE ALERTA</label>
                    <select name="tipo" id="tipo" class="form-control form-control-sm">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposAlerta as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ request('tipo') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-xl-2 col-md-6 mb-2">
                    <label for="estado" class="font-weight-bold text-muted">ESTADO</label>
                    <select name="estado" id="estado" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="atendida" {{ request('estado') === 'atendida' ? 'selected' : '' }}>Atendidas</option>
                    </select>
                </div>
                <div class="form-group col-xl-2 col-md-6 mb-2">
                    <label for="desde" class="font-weight-bold text-muted">DESDE</label>
                    <input type="date" name="desde" id="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
                </div>
                <div class="form-group col-xl-2 col-md-6 mb-2">
                    <label for="hasta" class="font-weight-bold text-muted">HASTA</label>
                    <input type="date" name="hasta" id="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
                </div>
                <div class="form-group col-xl-4 mb-2">
                    <label for="busqueda" class="font-weight-bold text-muted">BUSCAR</label>
                    <div class="input-group input-group-sm">
                        <input type="search" name="busqueda" id="busqueda" class="form-control" placeholder="Texto del aviso..." value="{{ request('busqueda') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-filter mr-1"></i>Filtrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <small class="text-muted">{{ $alertas->total() }} alerta(s) encontrada(s)</small>
                @if(request()->hasAny(['tipo', 'estado', 'desde', 'hasta', 'busqueda']))
                    <a href="{{ route('notificaciones.index') }}" class="btn btn-link btn-sm px-0">Limpiar filtros</a>
                @endif
            </div>
        </form>
    </div>

    <div class="ic-card p-0 overflow-hidden">
        <div class="ic-card-header d-flex justify-content-between align-items-center px-3 py-3">
            <h2 class="h6 font-weight-bold text-primary mb-0"><i class="fas fa-list-ul mr-2"></i>Historial de alertas</h2>
            <small class="text-muted">{{ $alertas->total() }} registro(s)</small>
        </div>
        <div class="table-responsive">
            <table class="table ic-table table-hover mb-0">
                <thead>
                    <tr>
                        <th>FECHA</th>
                        <th>TIPO</th>
                        <th>DETALLE</th>
                        <th>ESTADO</th>
                        <th class="text-right">ACCIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alertas as $alerta)
                        <tr>
                            <td class="text-nowrap">
                                <div>{{ $alerta->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $alerta->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="alert-type">{{ $tiposAlerta[$alerta->tipo_alerta] ?? $alerta->tipo_alerta }}</td>
                            <td class="alert-message">{{ $alerta->mensaje }}</td>
                            <td>
                                @if($alerta->estado === 'pendiente')
                                    <span class="alert-status pending"><i class="fas fa-clock"></i>Pendiente</span>
                                @else
                                    <span class="alert-status done"><i class="fas fa-check-circle"></i>Atendida</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($alerta->estado === 'pendiente')
                                    <form method="POST" action="{{ route('notificaciones.atender', $alerta) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Atender</button>
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="far fa-bell-slash fa-2x d-block mb-2"></i>
                                No hay alertas que coincidan con esos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alertas->hasPages())
            <div class="p-3">{{ $alertas->links('pagination::bootstrap-4') }}</div>
        @endif
    </div>
</div>
@endsection
