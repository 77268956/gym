@extends('layouts.app')

@section('title', 'Dashboard Principal')

@push('styles')
<style>
    .ic-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); background: #fff; margin-bottom: 1.5rem; }
    .ic-card-header { background: #fff; border-bottom: 1px solid #f0f2f5; border-radius: 12px 12px 0 0 !important; padding: 1.25rem 1.5rem; font-weight: 700; color: #4e73df; }
    .ic-table-container { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); overflow: hidden; margin-bottom: 1.5rem; }
    .ic-table { margin-bottom: 0; }
    .ic-table th { background: #f8f9fc; color: #4e73df; text-transform: uppercase; font-size: 0.75rem; border-top: none; }
    .ic-table td { vertical-align: middle; font-size: 0.85rem; }
    
    .kpi-card {
        border-radius: 12px; padding: 1.5rem; color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; height: 100%;
    }
    .kpi-card .kpi-value { font-size: 1.8rem; font-weight: 800; margin: 0; line-height: 1.2; }
    .kpi-card .kpi-label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; opacity: 0.9; }
    .kpi-card .kpi-icon { font-size: 2.5rem; opacity: 0.3; }
    
    .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
    .bg-gradient-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .bg-gradient-info { background: linear-gradient(45deg, #36b9cc, #258391); }
    .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    
    .ic-status-active { background-color: #D1FAE5; color: #059669; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
    .ic-status-inactive { background-color: #F1F5F9; color: #475569; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
    
    .chart-container { position: relative; height: 300px; width: 100%; }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 text-gray-800 font-weight-bold">Dashboard Admin</h4>
            <span class="text-muted">{{ \Carbon\Carbon::today()->isoFormat('dddd D \d\e MMMM, YYYY') }}</span>
        </div>
        
        <form method="GET" action="{{ route('dashboard') }}" class="form-inline">
            <label class="mr-2 font-weight-bold text-muted small">Periodo:</label>
            <select name="periodo" class="custom-select custom-select-sm" onchange="this.form.submit()">
                <option value="dia" {{ $periodo === 'dia' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Esta Semana</option>
                <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>Este Mes</option>
                <option value="ano" {{ $periodo === 'ano' ? 'selected' : '' }}>Este Año</option>
            </select>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-4 mb-4 mb-xl-0">
            <div class="kpi-card bg-gradient-success">
                <div>
                    <div class="kpi-value">${{ number_format($ingresosPeriodo, 2) }}</div>
                    <div class="kpi-label">Ingresos ({{ $labelPeriodo }})</div>
                </div>
                <i class="fas fa-dollar-sign kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-4 mb-xl-0">
            <div class="kpi-card bg-gradient-info">
                <div>
                    <div class="kpi-value">{{ $clientesActivos }}</div>
                    <div class="kpi-label">Clientes Activos Hoy</div>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-4 mb-xl-0">
            <div class="kpi-card bg-gradient-warning">
                <div>
                    <div class="kpi-value">{{ $asistenciasPeriodo }}</div>
                    <div class="kpi-label">Asistencias ({{ $labelPeriodo }})</div>
                </div>
                <i class="fas fa-walking kpi-icon"></i>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="ic-card h-100">
                <div class="ic-card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chart-bar mr-2"></i>Afluencia por Hora ({{ $labelPeriodo }})</span>
                    <span class="badge badge-primary" style="font-size: 0.8rem;">Total: {{ $asistenciasPeriodo }}</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="afluenciaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ic-card h-100">
                <div class="ic-card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chart-pie mr-2"></i>Métodos de Pago ({{ $labelPeriodo }})</span>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="metodosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row">
        <!-- Vencimientos Próximos -->
        <div class="col-lg-6 mb-4">
            <div class="ic-table-container h-100 d-flex flex-column">
                <div class="ic-card-header">
                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>Vencimientos Próximos (7 días)
                </div>
                <div class="table-responsive flex-grow-1">
                    <table class="table ic-table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Plan</th>
                                <th>Vence el</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vencimientosProximos as $vencimiento)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $vencimiento->cliente->nombre }}</div>
                                    <div class="text-muted small">{{ $vencimiento->cliente->telefono ?? 'Sin teléfono' }}</div>
                                </td>
                                <td>{{ $vencimiento->tipoMembresia->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="text-danger font-weight-bold">{{ $vencimiento->fecha_vencimiento->format('d/m/Y') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No hay vencimientos próximos en los siguientes 7 días.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0 text-center pb-3">
                    <a href="{{ route('membresias-clientes.index', ['estado' => 'por_vencer']) }}" class="btn btn-sm btn-outline-primary">Ver todas las membresías</a>
                </div>
            </div>
        </div>

        <!-- Pagos Recientes -->
        <div class="col-lg-6 mb-4">
            <div class="ic-table-container h-100 d-flex flex-column">
                <div class="ic-card-header">
                    <i class="fas fa-receipt text-success mr-2"></i>Pagos Recientes
                </div>
                <div class="table-responsive flex-grow-1">
                    <table class="table ic-table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagosRecientes as $pago)
                            <tr>
                                <td class="font-weight-bold">{{ $pago->cliente->nombre }}</td>
                                <td>
                                    @if($pago->tipo_pago === 'membresia')
                                        <span class="badge badge-primary">Membresía</span>
                                    @elseif($pago->tipo_pago === 'pase_diario')
                                        <span class="badge badge-info">Pase Diario</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $pago->tipo_pago }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold text-success">${{ number_format($pago->monto, 2) }}</span>
                                    <div class="small text-muted">{{ strtoupper($pago->metodo_pago) }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No hay pagos registrados recientemente.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-0 text-center pb-3">
                    <a href="{{ route('pagos.index') }}" class="btn btn-sm btn-outline-primary">Ir al módulo de Pagos</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Afluencia por hora (Bar Chart)
    const ctxAfluencia = document.getElementById('afluenciaChart');
    if (ctxAfluencia) {
        new Chart(ctxAfluencia, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartHorasLabels) !!},
                datasets: [{
                    label: 'Asistencias',
                    data: {!! json_encode($chartHorasData) !!},
                    backgroundColor: '#4e73df',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Métodos de Pago (Pie Chart)
    const ctxMetodos = document.getElementById('metodosChart');
    if (ctxMetodos) {
        new Chart(ctxMetodos, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartMetodosLabels) !!}.map(l => l.toUpperCase()),
                datasets: [{
                    data: {!! json_encode($chartMetodosData) !!},
                    backgroundColor: ['#1cc88a', '#4e73df', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
@endpush