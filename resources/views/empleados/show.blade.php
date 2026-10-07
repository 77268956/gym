@extends('layouts.app')

@section('title', 'Expediente del Empleado - ' . $empleado->nombre)

@section('skeleton')
    <div class="skel-box" style="height: 140px; width: 100%; margin-bottom: 1.5rem;"></div>
    <div class="skel-row">
        <div class="skel-box" style="height: 200px; flex: 1;"></div>
        <div class="skel-box" style="height: 200px; flex: 2;"></div>
    </div>
@endsection

@push('styles')
<style>
    .ic-profile-header { background: var(--primary); color: #fff; border-radius: 12px; padding: 1.5rem; position: relative; overflow: hidden; }
    .ic-profile-header::after { content: ''; position: absolute; right: 0; top: 0; width: 260px; height: 100%; background: url('data:image/svg+xml;utf8,<svg opacity="0.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="white"/></svg>') no-repeat right center; background-size: cover; pointer-events: none; }
    .ic-avatar-large { width: 88px; height: 88px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,.25); }
    .ic-avatar-placeholder { width: 88px; height: 88px; border-radius: 50%; background: rgba(255,255,255,.18); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; }
    .ic-stat-box { height: 100%; padding: 1rem; text-align: center; background: #fff; border: 1px solid #f1f5f9; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,.02); }
    .ic-stat-box .value { margin-bottom: .25rem; color: var(--primary); font-size: 1.5rem; font-weight: 700; line-height: 1; }
    .ic-stat-box .label { color: #64748b; font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; }
    .ic-card { border: 0; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,.04); }
    .ic-card-header { padding: 1rem 1.25rem; background: #fff; border-bottom: 1px solid #f0f2f5; border-radius: 12px 12px 0 0 !important; }
    .ic-table th { color: var(--primary); background: #f8f9fc; font-size: .72rem; text-transform: uppercase; white-space: nowrap; }
    .ic-table td { vertical-align: middle; font-size: .82rem; }
    .employee-status { padding: .3rem .65rem; border-radius: 50px; font-size: .72rem; font-weight: 700; }
    .employee-status.active { color: #059669; background: #d1fae5; }
    .employee-status.inactive { color: #475569; background: #f1f5f9; }
    @media (max-width: 767.98px) {
        .ic-profile-header { padding: 1.25rem; }
        .ic-avatar-large, .ic-avatar-placeholder { width: 68px; height: 68px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:.5rem;">
        <div>
            <h1 class="h5 font-weight-bold text-dark mb-1">Expediente del empleado</h1>
            <p class="small text-muted mb-0">Información y registro de asistencia.</p>
        </div>
        <div class="d-flex" style="gap:.5rem;">
            <a href="{{ route('empleados') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Empleados</a>
            <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-primary btn-sm"><i class="fas fa-pen mr-1"></i> Editar</a>
        </div>
    </div>

    <div class="ic-profile-header mb-3">
        <div class="row align-items-center position-relative" style="z-index:1;">
            <div class="col-auto">
                @if($empleado->foto_referencia)
                    <img src="{{ asset('storage/' . $empleado->foto_referencia) }}" alt="Foto de {{ $empleado->nombre }}" class="ic-avatar-large">
                @else
                    <div class="ic-avatar-placeholder">{{ collect(explode(' ', $empleado->nombre))->map(fn ($parte) => strtoupper($parte[0] ?? ''))->take(2)->implode('') }}</div>
                @endif
            </div>
            <div class="col">
                <h2 class="h4 font-weight-bold mb-1">{{ $empleado->nombre }}</h2>
                <div class="d-flex flex-wrap align-items-center mb-2" style="gap:.5rem 1rem; font-size:.85rem; color:rgba(255,255,255,.9);">
                    <span><i class="fas fa-id-card mr-1"></i>{{ $empleado->cedula }}</span>
                    <span><i class="fas fa-user mr-1"></i>{{ $empleado->usuario }}</span>
                    <span><i class="fas fa-user-tag mr-1"></i>{{ $empleado->rol === 'admin' ? 'Administrador' : 'Recepcionista' }}</span>
                </div>
                <span class="employee-status {{ $empleado->estado === 'activo' ? 'active' : 'inactive' }}">
                    <i class="fas {{ $empleado->estado === 'activo' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>{{ strtoupper($empleado->estado) }}
                </span>
            </div>
            <div class="col-md-auto mt-3 mt-md-0">
                <div class="bg-white text-dark rounded p-3 text-center">
                    <div class="small text-muted text-uppercase font-weight-bold">Horario asignado</div>
                    <div class="font-weight-bold mt-1">
                        {{ $empleado->hora_entrada_turno ? \Carbon\Carbon::parse($empleado->hora_entrada_turno)->format('h:i A') : 'Sin definir' }}
                        <span class="text-muted mx-1">a</span>
                        {{ $empleado->hora_salida_turno ? \Carbon\Carbon::parse($empleado->hora_salida_turno)->format('h:i A') : 'Sin definir' }}
                    </div>
                    <small class="text-muted">Tolerancia: {{ $empleado->tolerancia_minutos ?? 0 }} min</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="ic-stat-box"><div class="value">{{ $asistenciasMes }}</div><div class="label">Asistencias este mes</div></div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3">
            <div class="ic-stat-box"><div class="value text-warning">{{ $tardanzasMes }}</div><div class="label">Entradas tarde</div></div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-sm-0">
            <div class="ic-stat-box"><div class="value text-danger">{{ $salidasTempranasMes }}</div><div class="label">Salidas tempranas</div></div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="ic-stat-box"><div class="value text-info">{{ $ausenciasMes }}</div><div class="label">Ausencias este mes</div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="ic-card h-100">
                <div class="ic-card-header"><h3 class="h6 font-weight-bold text-primary mb-0"><i class="fas fa-address-card mr-2"></i>Datos del empleado</h3></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Usuario</span><strong>{{ $empleado->usuario }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Correo</span><strong>{{ $empleado->email }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Cédula</span><strong>{{ $empleado->cedula }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Rol</span><strong>{{ $empleado->rol === 'admin' ? 'Administrador' : 'Recepcionista' }}</strong></div>
                    <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Entrada</span><strong>{{ $empleado->hora_entrada_turno ? \Carbon\Carbon::parse($empleado->hora_entrada_turno)->format('h:i A') : 'No definida' }}</strong></div>
                    <div class="d-flex justify-content-between py-2"><span class="text-muted">Salida</span><strong>{{ $empleado->hora_salida_turno ? \Carbon\Carbon::parse($empleado->hora_salida_turno)->format('h:i A') : 'No definida' }}</strong></div>
                    <div class="small text-muted mt-2">Registrado el {{ $empleado->created_at?->format('d/m/Y') ?? '—' }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-3">
            <div class="ic-card h-100 p-0 overflow-hidden">
                <div class="ic-card-header d-flex flex-wrap justify-content-between align-items-center" style="gap:.5rem;">
                    <h3 class="h6 font-weight-bold text-primary mb-0"><i class="fas fa-calendar-check mr-2"></i>Historial de asistencia</h3>
                    <span class="small text-muted">{{ $asistenciasSemana }} asistencia(s) esta semana · últimos 30 registros</span>
                </div>
                <div class="table-responsive">
                    <table class="table ic-table table-hover mb-0">
                        <thead>
                            <tr><th>FECHA</th><th>ENTRADA</th><th>SALIDA</th><th>HORAS</th><th>OBSERVACIONES</th></tr>
                        </thead>
                        <tbody>
                            @forelse($empleado->asistencias as $asistencia)
                                <tr>
                                    <td class="text-nowrap">
                                        <div class="font-weight-bold">{{ $asistencia->fecha->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $asistencia->fecha->locale('es')->isoFormat('dddd') }}</small>
                                    </td>
                                    <td class="text-nowrap">
                                        @if($asistencia->hora_entrada)
                                            {{ \Carbon\Carbon::parse($asistencia->hora_entrada)->format('h:i A') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if($asistencia->hora_salida)
                                            {{ \Carbon\Carbon::parse($asistencia->hora_salida)->format('h:i A') }}
                                        @elseif($asistencia->salida_no_registrada)
                                            <span class="text-danger">No registrada</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $asistencia->horas_trabajadas ? number_format((float) $asistencia->horas_trabajadas, 1) . ' h' : '—' }}</td>
                                    <td>
                                        @if(!$asistencia->hora_entrada)
                                            <span class="text-danger font-weight-bold">Ausencia</span>
                                        @else
                                            @if($asistencia->tardanza)<span class="text-warning font-weight-bold mr-2">Tarde</span>@endif
                                            @if($asistencia->salida_temprana)<span class="text-danger font-weight-bold mr-2">Salida temprana</span>@endif
                                            @if($asistencia->salida_no_registrada)<span class="text-muted font-weight-bold">Salida automática</span>@endif
                                            @if(!$asistencia->tardanza && !$asistencia->salida_temprana && !$asistencia->salida_no_registrada)<span class="text-success">Sin incidencias</span>@endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-5"><i class="fas fa-calendar-times fa-2x d-block mb-2"></i>No hay registros de asistencia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 border-top text-right">
                    <a href="{{ route('asistencias_empleados.index', ['empleado_id' => $empleado->id]) }}" class="btn btn-sm btn-outline-primary">Ver bitácora completa <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
