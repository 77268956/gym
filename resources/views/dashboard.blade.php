@extends('layouts.app')

@section('title', 'Dashboard Principal')

@push('styles')
<style>
    :root { --card-color: var(--sidebar-bg); }
    html, body { height: 100%; overflow: hidden; }
    #page-wrapper main {
        height: calc(100vh - var(--navbar-h));
        display: flex; flex-direction: column; overflow: hidden;
        padding: 0.5rem 1.25rem !important;
    }
    .page-header { margin-bottom: 0.5rem !important; }
    .dashboard-container {
        flex: 1; min-height: 0; display: flex; flex-direction: column;
        padding: 0 !important;
    }
    .dashboard-toolbar { flex-shrink: 0; margin-bottom: 0.25rem !important; }

    .kpi-card {
        border-radius: 10px; border: none; padding: 0.6rem 1rem; color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex;
        justify-content: space-between; align-items: center; height: 100%;
        background: var(--card-color);
    }
    .kpi-value { font-size: 1.4rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px; }
    .kpi-icon { font-size: 1.8rem; opacity: 0.4; }

    .ic-card, .ic-table-container {
        background: #fff; border: none; border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04); overflow: hidden;
    }
    .ic-card-header {
        background: #fff; border-bottom: 1px solid #f0f2f5;
        padding: 0.85rem; font-weight: 700; font-size: 0.8rem;
        color: var(--card-color); text-transform: uppercase;
    }
    .ic-table { margin-bottom: 0; }
    .ic-table th {
        background: #F8FAFC; color: #64748B; text-transform: uppercase;
        font-size: 0.7rem; border-top: none; border-bottom: 2px solid #E2E8F0;
        padding: 0.5rem;
    }
    .ic-table td { vertical-align: middle; font-size: 0.8rem; padding: 0.5rem; border-top: 1px solid #F1F5F9; }
    .card-footer { padding: 0.5rem; }
    .chart-container { position: relative; height: 175px; width: 100%; }
    .dashboard-row { margin-bottom: 0.5rem; }
    .dashboard-kpis { flex-shrink: 0; }
    .dashboard-charts { height: 235px; flex-shrink: 0; }
    .dashboard-charts > [class*="col-"] { height: 100%; }
    .dashboard-charts .card-body { padding: 0.6rem 0.75rem; }
    .dashboard-tables { flex: 1; min-height: 0; }
    .dashboard-tables > [class*="col-"] { height: 100%; }
    .dashboard-tables .table-responsive { overflow-y: auto; min-height: 0; }
    .dashboard-tables .card-footer { flex-shrink: 0; }
    .payment-badge {
        color: #fff; padding: 0.3rem 0.6rem; border-radius: 50px;
        font-size: 0.72rem; font-weight: 600;
    }
    .payment-badge-membership { background: var(--primary); }
    .payment-badge-daily-pass { background: var(--primary-hover); }
    .payment-badge-other { background: var(--sidebar-hover); }
    .payment-method-icon { color: var(--primary); }
    .payment-section-icon { color: var(--primary); }
    .expiration-section-icon { color: var(--primary); }

    @media (max-width: 991.98px) {
        html, body { overflow: auto; }
        #page-wrapper main { height: auto; min-height: calc(100vh - var(--navbar-h)); overflow: visible; }
        .dashboard-container { display: block; }
        .dashboard-charts { height: auto; }
        .dashboard-charts > [class*="col-"], .dashboard-tables > [class*="col-"] { height: auto; }
        .chart-container { height: 220px; }
        .dashboard-tables .table-responsive { overflow: auto; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-container">
    
    <div class="d-flex justify-content-between align-items-center dashboard-toolbar">
        <div>
            <span class="text-muted">{{ \Carbon\Carbon::today()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}</span>
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
    <div class="row dashboard-row dashboard-kpis">
        <div class="col-xl-4 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div>
                    <div class="kpi-value">${{ number_format($ingresosPeriodo, 2) }}</div>
                    <div class="kpi-label">Ingresos ({{ $labelPeriodo }})</div>
                </div>
                <i class="fas fa-dollar-sign kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div>
                    <div class="kpi-value">{{ $clientesActivos }}</div>
                    <div class="kpi-label">Clientes Activos Hoy</div>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div>
                    <div class="kpi-value">{{ $asistenciasPeriodo }}</div>
                    <div class="kpi-label">Asistencias ({{ $labelPeriodo }})</div>
                </div>
                <i class="fas fa-walking kpi-icon"></i>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row dashboard-row dashboard-charts">
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
    <div class="row dashboard-tables">
        <!-- Vencimientos Próximos -->
        <div class="col-lg-6 mb-3">
            <div class="ic-table-container h-100 d-flex flex-column">
                <div class="ic-card-header">
                    <i class="fas fa-exclamation-triangle expiration-section-icon mr-2"></i>Vencimientos Próximos (7 días)
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
        <div class="col-lg-6 mb-3">
            <div class="ic-table-container h-100 d-flex flex-column">
                <div class="ic-card-header">
                    <i class="fas fa-receipt payment-section-icon mr-2"></i>Pagos Recientes
                </div>
                <div class="table-responsive flex-grow-1">
                    <table class="table ic-table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pagosRecientes as $pago)
                            <tr>
                                <td>
                                    <span class="d-block font-weight-bold text-dark">{{ $pago->fecha_pago->format('d/m/Y') }}</span>
                                    <small class="text-muted">{{ $pago->fecha_pago->format('h:i A') }}</small>
                                </td>
                                <td class="font-weight-bold">
                                    {{ $pago->cliente->nombre ?? 'Cliente no disponible' }}
                                </td>
                                <td>
                                    @if($pago->tipo_pago === 'membresia')
                                        <span class="payment-badge payment-badge-membership"><i class="fas fa-id-card mr-1"></i>Membresía</span>
                                    @elseif($pago->tipo_pago === 'pase_diario')
                                        <span class="payment-badge payment-badge-daily-pass"><i class="fas fa-ticket-alt mr-1"></i>Pase Diario</span>
                                    @else
                                        <span class="payment-badge payment-badge-other"><i class="fas fa-receipt mr-1"></i>{{ $pago->tipo_pago }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold text-success">${{ number_format($pago->monto, 2) }}</span>
                                    @php
                                        $metodoPagoIcon = 'fas fa-money-bill';
                                        if ($pago->metodo_pago === 'tarjeta') {
                                            $metodoPagoIcon = 'fas fa-credit-card';
                                        } elseif ($pago->metodo_pago === 'transferencia') {
                                            $metodoPagoIcon = 'fas fa-exchange-alt';
                                        }
                                    @endphp
                                    <div class="small text-muted">
                                        <i class="{{ $metodoPagoIcon }} payment-method-icon mr-1"></i>{{ ucfirst($pago->metodo_pago) }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No hay pagos registrados recientemente.</td>
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
    const primaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--primary').trim() || '#2563EB';

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
                    backgroundColor: primaryColor,
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