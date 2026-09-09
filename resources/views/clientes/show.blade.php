@extends('layouts.app')

@section('title', 'Expediente del Cliente - ' . $cliente->nombre)

@push('styles')
<style>
    .ic-profile-header { background: linear-gradient(135deg, var(--primary), #1e40af); color: white; border-radius: 12px; padding: 2rem; position: relative; overflow: hidden; }
    .ic-profile-header::after { content: ''; position: absolute; right: 0; top: 0; width: 300px; height: 100%; background: url('data:image/svg+xml;utf8,<svg opacity="0.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="white"/></svg>') no-repeat right center; background-size: cover; pointer-events: none; }
    .ic-avatar-large { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.2); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .ic-stat-box { background: white; border-radius: 10px; padding: 1rem; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; }
    .ic-stat-box .value { font-size: 1.5rem; font-weight: 700; color: var(--primary); line-height: 1; margin-bottom: 0.25rem; }
    .ic-stat-box .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
    .ic-timeline { position: relative; padding-left: 1.5rem; border-left: 2px solid #e2e8f0; margin-left: 1rem; }
    .ic-timeline-item { position: relative; margin-bottom: 1.5rem; }
    .ic-timeline-item::before { content: ''; position: absolute; left: -1.85rem; top: 0.25rem; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); border: 2px solid white; box-shadow: 0 0 0 2px var(--primary); }
    .ic-timeline-date { font-size: 0.85rem; color: #64748b; font-weight: 500; }
    .ic-timeline-content { background: #f8fafc; padding: 0.75rem 1rem; border-radius: 8px; margin-top: 0.25rem; border: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-11">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3 d-flex justify-content-between align-items-center">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('user') }}"><i class="fas fa-users mr-1"></i> Socios / Clientes</a></li>
                    <li class="breadcrumb-item active">Expediente: {{ $cliente->nombre }}</li>
                </ol>
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary bg-white font-weight-bold">
                    <i class="fas fa-pen mr-1"></i> Editar Perfil
                </a>
            </nav>

            {{-- Header Profile --}}
            <div class="ic-profile-header mb-4 shadow-sm">
                <div class="row align-items-center position-relative" style="z-index: 1;">
                    <div class="col-md-auto text-center mb-3 mb-md-0">
                        @if($cliente->foto_referencia)
                            <img src="{{ asset('storage/' . $cliente->foto_referencia) }}" alt="{{ $cliente->nombre }}" class="ic-avatar-large">
                        @else
                            <div class="ic-avatar-large mx-auto d-flex align-items-center justify-content-center bg-white text-primary" style="font-size: 2.5rem; font-weight: 700;">
                                {{ collect(explode(' ', $cliente->nombre))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md">
                        <h3 class="font-weight-bold mb-1">{{ $cliente->nombre }}</h3>
                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 15px; font-size: 0.9rem; color: rgba(255,255,255,0.9);">
                            <span><i class="fas fa-id-card mr-1"></i> {{ $cliente->cedula }}</span>
                            <span><i class="fas fa-phone mr-1"></i> {{ $cliente->telefono ?? 'Sin teléfono' }}</span>
                            <span><i class="fas fa-calendar-alt mr-1"></i> Registrado: {{ $cliente->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            @if($cliente->estado === 'activo')
                                <span class="badge badge-light text-success px-2 py-1" style="font-size:0.75rem;"><i class="fas fa-check-circle mr-1"></i> ACTIVO</span>
                            @else
                                <span class="badge badge-light text-danger px-2 py-1" style="font-size:0.75rem;"><i class="fas fa-times-circle mr-1"></i> INACTIVO</span>
                            @endif
                            
                            @if($cliente->descriptor_facial)
                                <span class="badge badge-info ml-2 px-2 py-1" style="background: rgba(255,255,255,0.2); color:white; border:none; font-size:0.75rem;">
                                    <i class="fas fa-fingerprint mr-1"></i> Biometría Registrada
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-auto text-center text-md-right mt-3 mt-md-0">
                        <div class="bg-white rounded-lg p-3 text-center d-inline-block shadow-sm" style="min-width: 150px;">
                            <div class="text-warning mb-1" style="font-size: 1.5rem;"><i class="fas fa-star"></i></div>
                            <h2 class="font-weight-bold text-dark mb-0" style="line-height:1;">{{ number_format($cliente->puntos_ecogim ?? 0) }}</h2>
                            <small class="text-muted font-weight-bold text-uppercase" style="letter-spacing:0.05em; font-size:0.7rem;">Puntos Ecogim</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Columna Izquierda: Info & Plan --}}
                <div class="col-lg-4 mb-4">
                    {{-- Plan Actual --}}
                    <div class="ic-card mb-4">
                        <div class="ic-card-header pb-0 border-0 bg-transparent pt-4">
                            <h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-id-badge mr-2"></i> Plan de Membresía Actual</h6>
                        </div>
                        <div class="card-body">
                            @if($membresiaActiva)
                                <div class="p-3 border rounded mb-3" style="background:#F0FDF4; border-color:#10B981 !important;">
                                    <h5 class="font-weight-bold" style="color:#065F46;">{{ $membresiaActiva->tipoMembresia->nombre ?? 'Plan Desconocido' }}</h5>
                                    <div class="d-flex justify-content-between mt-2 text-sm" style="font-size: 0.85rem;">
                                        <div class="text-muted">Inicio: <br><strong class="text-dark">{{ \Carbon\Carbon::parse($membresiaActiva->fecha_inicio)->format('d/m/Y') }}</strong></div>
                                        <div class="text-right text-muted">Vence: <br><strong class="text-dark">{{ \Carbon\Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y') }}</strong></div>
                                    </div>
                                    @php
                                        $diasRestantes = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($membresiaActiva->fecha_vencimiento), false);
                                        $porcentaje = max(0, min(100, (($membresiaActiva->tipoMembresia->duracion_dias ?? 30) - $diasRestantes) / ($membresiaActiva->tipoMembresia->duracion_dias ?? 30) * 100));
                                    @endphp
                                    <div class="progress mt-3" style="height: 6px;">
                                        <div class="progress-bar {{ $diasRestantes <= 5 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                    <div class="text-center mt-2" style="font-size: 0.8rem;">
                                        @if($diasRestantes < 0)
                                            <span class="text-danger font-weight-bold">Vencida hace {{ abs(intval($diasRestantes)) }} días</span>
                                        @elseif($diasRestantes == 0)
                                            <span class="text-danger font-weight-bold">¡Vence HOY!</span>
                                        @else
                                            <span class="text-success font-weight-bold">Quedan {{ intval($diasRestantes) }} días</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="text-center p-4 border rounded bg-light">
                                    <i class="fas fa-exclamation-circle text-muted fa-2x mb-2"></i>
                                    <p class="text-muted mb-0 font-weight-bold">Sin membresía activa</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Historial Médico y Notas --}}
                    <div class="ic-card mb-4">
                        <div class="ic-card-header pb-0 border-0 bg-transparent pt-4">
                            <h6 class="font-weight-bold text-primary mb-0"><i class="fas fa-notes-medical mr-2"></i> Notas / Historial Médico</h6>
                        </div>
                        <div class="card-body">
                            @if($cliente->historial_medico)
                                <div class="p-3 bg-light rounded" style="font-size: 0.9rem; color: #475569; border-left: 3px solid var(--primary);">
                                    {{ $cliente->historial_medico }}
                                </div>
                            @else
                                <p class="text-muted small mb-0 font-italic">No hay notas registradas para este cliente.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Columna Derecha: Estadísticas y Actividad --}}
                <div class="col-lg-8">
                    
                    {{-- Mini Stats --}}
                    <div class="row mb-4">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <div class="ic-stat-box">
                                <div class="value">{{ $asistenciasMes }}</div>
                                <div class="label">Asistencias este Mes</div>
                            </div>
                        </div>
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <div class="ic-stat-box">
                                <div class="value">{{ $asistenciasSemana }}</div>
                                <div class="label">Esta Semana</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="ic-stat-box">
                                <div class="value">{{ $cliente->canjes->count() }}</div>
                                <div class="label">Canjes Realizados</div>
                            </div>
                        </div>
                    </div>

                    {{-- Pestañas de Actividad --}}
                    <div class="ic-card">
                        <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
                            <ul class="nav nav-tabs border-bottom-0" id="activityTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active font-weight-bold" style="border-top-width: 3px; border-top-color: var(--primary);" id="asistencias-tab" data-toggle="tab" href="#asistencias" role="tab"><i class="fas fa-calendar-check mr-1"></i> Asistencias</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold" id="grafico-tab" data-toggle="tab" href="#grafico" role="tab"><i class="fas fa-chart-bar mr-1"></i> Frecuencia</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold" id="canjes-tab" data-toggle="tab" href="#canjes" role="tab"><i class="fas fa-gift mr-1"></i> Productos Canjeados</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content" id="activityTabsContent">
                                
                                {{-- Tab: Lista Asistencias --}}
                                <div class="tab-pane fade show active" id="asistencias" role="tabpanel">
                                    @if($cliente->asistencias->count() > 0)
                                        <div class="ic-timeline mt-2">
                                            @foreach($cliente->asistencias as $asistencia)
                                            <div class="ic-timeline-item">
                                                <div class="ic-timeline-date">{{ \Carbon\Carbon::parse($asistencia->fecha)->isoFormat('dddd, D [de] MMMM YYYY') }} • {{ \Carbon\Carbon::parse($asistencia->hora)->format('h:i A') }}</div>
                                                <div class="ic-timeline-content d-flex justify-content-between align-items-center">
                                                    <span>Ingreso registrado en recepción.</span>
                                                    @if($asistencia->puntos_otorgados > 0)
                                                        <span class="badge badge-success text-white">+{{ $asistencia->puntos_otorgados }} pts</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center p-5">
                                            <i class="fas fa-door-open fa-3x text-muted mb-3 opacity-50"></i>
                                            <h6 class="text-muted">No hay registros de asistencia recientes.</h6>
                                        </div>
                                    @endif
                                </div>

                                {{-- Tab: Gráfico Frecuencia --}}
                                <div class="tab-pane fade" id="grafico" role="tabpanel">
                                    <h6 class="font-weight-bold mb-4 text-center">Días de mayor asistencia (Histórico)</h6>
                                    <div style="height: 300px; width: 100%;">
                                        <canvas id="frecuenciaChart"></canvas>
                                    </div>
                                </div>

                                {{-- Tab: Canjes --}}
                                <div class="tab-pane fade" id="canjes" role="tabpanel">
                                    @if($cliente->canjes->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table ic-table">
                                                <thead>
                                                    <tr>
                                                        <th>FECHA</th>
                                                        <th>PRODUCTO</th>
                                                        <th>PUNTOS USADOS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($cliente->canjes as $canje)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($canje->fecha)->format('d/m/Y') }}</td>
                                                        <td class="font-weight-bold text-dark">{{ $canje->producto->nombre ?? 'Producto Desconocido' }}</td>
                                                        <td class="text-danger font-weight-bold">-{{ $canje->puntos_utilizados }} pts</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center p-5">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3 opacity-50"></i>
                                            <h6 class="text-muted">El cliente no ha canjeado ningún producto aún.</h6>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar gráfico cuando se abre la pestaña (para evitar bugs de renderizado oculto)
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if(e.target.id === 'grafico-tab' && !window.frecuenciaChartInstance) {
                var ctx = document.getElementById('frecuenciaChart').getContext('2d');
                window.frecuenciaChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                        datasets: [{
                            label: 'Visitas',
                            data: {!! json_encode($diasChart) !!},
                            backgroundColor: 'rgba(37, 99, 235, 0.7)',
                            borderColor: '#2563EB',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } },
                            x: { grid: { display: false } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    });
</script>
@endpush