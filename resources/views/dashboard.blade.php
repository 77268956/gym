@extends('layouts.app')

@section('title', 'Dashboard Principal')

@push('styles')
<style>
    :root { --card-color: var(--sidebar-bg); }
    html, body { width: 100%; height: 100%; overflow: hidden; }
    #page-wrapper { height: calc(100vh - var(--navbar-h)); min-height: 0; }
    #page-wrapper main {
        box-sizing: border-box; height: 100%; min-height: 0;
        display: flex; flex-direction: column; overflow: hidden;
        padding: 0.5rem 1rem !important;
    }
    .dashboard-container {
        box-sizing: border-box; width: 100%; height: 100%; min-height: 0;
        display: grid;
        grid-template-rows: auto auto auto minmax(0, 1.1fr) minmax(0, 0.9fr) minmax(0, 1fr);
        gap: 0.45rem; overflow: hidden; padding: 0 !important;
    }
    .dashboard-toolbar {
        grid-column: 1 / -1; min-height: 42px; margin: 0 !important; padding: .35rem .65rem;
        border-radius: 10px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,.04);
        gap: .5rem;
    }
    .dashboard-filter-form { display: flex; align-items: flex-end; flex-wrap: wrap; gap: .45rem; }
    .dashboard-filter-control { min-width: 145px; }
    .dashboard-filter-control label { display: block; margin-bottom: .15rem; color: #64748b; font-size: .65rem; font-weight: 700; text-transform: uppercase; }
    .dashboard-filter-form .form-control, .dashboard-filter-form .custom-select { height: 32px; border-radius: 6px; font-size: .78rem; }
    .dashboard-filter-form .date-control { width: 145px; }
    .dashboard-range-summary { color: #334155; font-size: .85rem; white-space: nowrap; }
    .dashboard-range-summary i { color: var(--primary); }
    .dashboard-toolbar > div { font-size: 0.78rem; white-space: nowrap; }

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
    .chart-container { position: relative; flex: 1 1 auto; height: 100%; min-height: 0; width: 100%; }
    .dashboard-row { min-width: 0; margin: 0; }
    .dashboard-kpis {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.45rem; min-height: 0;
    }
    .dashboard-kpis > [class*="col-"] {
        width: auto;
        max-width: none;
        padding: 0;
        margin: 0 !important;
    }
    .dashboard-kpis .kpi-card { min-height: 56px; gap: 0.35rem; padding: 0.45rem 0.6rem; }
    .dashboard-kpis .kpi-card > div { min-width: 0; }
    .dashboard-kpis .kpi-value { font-size: clamp(0.78rem, 1.2vw, 1.15rem); white-space: nowrap; }
    .dashboard-kpis .kpi-label { line-height: 1.25; }
    .dashboard-kpis .kpi-icon { flex-shrink: 0; font-size: 1.2rem; }
    .dashboard-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.45rem; }
    .dashboard-summary > [class*="col-"] { width: auto; max-width: none; padding: 0; min-width: 0; }
    .dashboard-summary .ic-card { height: 100%; min-height: 48px; padding: 0.4rem 0.6rem !important; }
    .dashboard-summary .ic-card small { display: block; font-size: 0.62rem; line-height: 1.15; }
    .dashboard-summary .ic-card .h5 { font-size: 0.9rem; line-height: 1.15; }
    .dashboard-primary-charts { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); }
    .dashboard-secondary-charts { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .dashboard-charts { min-height: 0; gap: 0.45rem; }
    .dashboard-charts > [class*="col-"] { width: auto; max-width: none; height: 100%; min-height: 0; padding: 0; }
    .dashboard-charts .ic-card { display: flex; flex-direction: column; height: 100%; min-height: 0; }
    .dashboard-charts .ic-card-header { flex: 0 0 auto; padding: 0.4rem 0.55rem; font-size: 0.66rem; }
    .dashboard-charts .card-body { display: flex; flex: 1 1 auto; min-height: 0; padding: 0.25rem 0.4rem; }
    .dashboard-tables { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); min-height: 0; gap: 0.45rem; }
    .dashboard-tables > [class*="col-"] { width: auto; max-width: none; height: 100%; min-height: 0; padding: 0; }
    .dashboard-tables .ic-table-container { height: 100%; min-height: 0; overflow: hidden; }
    .dashboard-tables .table-responsive { flex: 1 1 auto; min-height: 0; overflow: auto; }
    .dashboard-tables .ic-card-header { flex: 0 0 auto; padding: 0.4rem 0.55rem; font-size: 0.66rem; }
    .dashboard-tables .ic-table th, .dashboard-tables .ic-table td { padding: 0.22rem 0.35rem; font-size: 0.65rem; line-height: 1.15; }
    #vencimientosTable { width: 100%; table-layout: fixed; }
    #vencimientosTable th:nth-child(1), #vencimientosTable td:nth-child(1) { width: 48%; }
    #vencimientosTable th:nth-child(2), #vencimientosTable td:nth-child(2) { width: 26%; }
    #vencimientosTable th:nth-child(3), #vencimientosTable td:nth-child(3) { width: 26%; }
    #vencimientosTable td:first-child div { white-space: normal; overflow-wrap: anywhere; }
    #vencimientosTable td:nth-child(3) { white-space: nowrap; }
    .dashboard-tables .card-footer { flex: 0 0 auto; padding: 0.25rem !important; }
    .dashboard-tables .card-footer .btn { padding: 0.2rem 0.45rem; font-size: 0.65rem; }
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

    .dashboard-summary, .dashboard-primary-charts, .dashboard-secondary-charts, .dashboard-tables { min-width: 0; }
    @media (max-height: 800px) {
        .dashboard-container { gap: 0.3rem; grid-template-rows: auto auto auto minmax(0, 1.1fr) minmax(0, 0.9fr) minmax(0, 1fr); }
        .dashboard-toolbar { min-height: 36px; padding: 0.2rem 0.5rem; }
        .dashboard-kpis .kpi-card { min-height: 48px; padding: 0.3rem 0.45rem; }
        .dashboard-summary .ic-card { min-height: 42px; padding: 0.3rem 0.45rem !important; }
        .dashboard-charts .ic-card-header, .dashboard-tables .ic-card-header { padding: 0.3rem 0.45rem; }
    }
    @media (max-width: 991.98px) {
        #page-wrapper main { padding: 0.4rem 0.55rem !important; }
        .dashboard-toolbar { align-items: stretch !important; flex-direction: column; }
        .dashboard-filter-form { justify-content: flex-start; align-items: flex-end; }
        .dashboard-filter-control { flex: 1 1 110px; min-width: 0; }
        .dashboard-kpis { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .dashboard-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-primary-charts { grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr); }
    }
    @media (max-width: 575.98px) {
        .dashboard-container { gap: 0.25rem; }
        .dashboard-kpis { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.25rem; }
        .dashboard-kpis .kpi-card { min-height: 42px; padding: 0.25rem; }
        .dashboard-kpis .kpi-icon { display: none; }
        .dashboard-kpis .kpi-value { font-size: 0.75rem; }
        .dashboard-kpis .kpi-label { font-size: 0.48rem; }
        .dashboard-toolbar { padding: 0.25rem; }
        .dashboard-toolbar > div { font-size: 0.62rem; }
        .dashboard-filter-form { gap: 0.2rem; flex-wrap: wrap; }
        .dashboard-filter-control { min-width: 0; }
        .dashboard-filter-control label { font-size: 0.48rem; }
        .dashboard-filter-form .form-control, .dashboard-filter-form .custom-select { height: 26px; padding: 0.15rem; font-size: 0.6rem; }
        .dashboard-filter-form .date-control { width: 92px; }
        .dashboard-filter-form .btn { padding: 0.2rem 0.35rem; font-size: 0.6rem; }
        .dashboard-summary { gap: 0.25rem; }
        .dashboard-summary .ic-card { min-height: 36px; padding: 0.2rem !important; }
        .dashboard-summary .ic-card small { font-size: 0.48rem; }
        .dashboard-summary .ic-card .h5 { font-size: 0.65rem; }
        .dashboard-primary-charts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-secondary-charts { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .dashboard-charts .ic-card-header, .dashboard-tables .ic-card-header { padding: 0.2rem; font-size: 0.52rem; }
        .dashboard-charts .card-body { padding: 0.1rem; }
        .dashboard-tables { gap: 0.25rem; }
        .dashboard-tables .ic-table th, .dashboard-tables .ic-table td { padding: 0.15rem; font-size: 0.52rem; }
        .dashboard-tables .card-footer .btn { font-size: 0.52rem; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-container">
    
    <div class="d-flex justify-content-between align-items-center dashboard-toolbar">
        <div>
            <span class="text-muted"><i class="far fa-calendar-alt mr-1 text-primary"></i>Fechas:</span>
            <strong>{{ $rangoFechas ?? '' }}</strong>
        </div>
        
        <form method="GET" action="{{ route('dashboard') }}" class="dashboard-filter-form">
            <div class="dashboard-filter-control">
                <label for="periodo">Periodo</label>
                <select name="periodo" id="periodo" class="custom-select custom-select-sm" onchange="actualizarFiltrosDashboard()">
                    <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>Mes completo</option>
                    <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Semana del mes</option>
                    <option value="rango" {{ $periodo === 'rango' ? 'selected' : '' }}>Rango personalizado</option>
                </select>
            </div>

            <div id="filtroMes" class="dashboard-filter-control {{ $periodo === 'rango' ? 'd-none' : '' }}">
                <label for="mes">Mes</label>
                <select name="mes" id="mes" class="custom-select custom-select-sm">
                    @foreach($mesesDisponibles as $valorMes => $etiquetaMes)
                        <option value="{{ $valorMes }}" {{ $mesSeleccionado === $valorMes ? 'selected' : '' }}>{{ $etiquetaMes }}</option>
                    @endforeach
                </select>
            </div>
            <div id="filtroSemana" class="dashboard-filter-control {{ $periodo === 'semana' ? '' : 'd-none' }}">
                <label for="semana">Semana</label>
                <select name="semana" id="semana" class="custom-select custom-select-sm">
                    @foreach($semanasDisponibles as $semana)
                        <option value="{{ $semana['inicio'] }}" {{ $semanaSeleccionada === $semana['inicio'] ? 'selected' : '' }}>{{ $semana['etiqueta'] }}</option>
                    @endforeach
                </select>
            </div>
            <div id="filtroFechas" class="dashboard-filter-control {{ $periodo === 'rango' ? '' : 'd-none' }}">
                <label for="desde">Desde</label>
                <input type="date" name="desde" id="desde" class="form-control form-control-sm date-control" value="{{ $desdeSeleccionado }}">
            </div>
            <div id="filtroHasta" class="dashboard-filter-control {{ $periodo === 'rango' ? '' : 'd-none' }}">
                <label for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta" class="form-control form-control-sm date-control" value="{{ $hastaSeleccionado }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-filter mr-1"></i>Aplicar</button>
        </form>
    </div>

    @if($errors->has('desde') || $errors->has('hasta'))
        <div class="alert alert-danger py-2 mb-2 small">{{ $errors->first('desde') ?: $errors->first('hasta') }}</div>
    @endif

    <!-- KPI Cards -->
    <div class="row dashboard-row dashboard-kpis">
        <div class="col-xl-2 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div>
                    <div class="kpi-value">${{ number_format($ingresosPeriodo, 2) }}</div>
                    <div class="kpi-label">Ingresos ({{ $labelPeriodo }})</div>
                </div>
                <i class="fas fa-dollar-sign kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div>
                    <div class="kpi-value">{{ $clientesTotales }}</div>
                    <div class="kpi-label">Clientes registrados</div>
                </div>
                <i class="fas fa-users kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card">
                <div>
                    <div class="kpi-value">{{ $asistenciasPeriodo }}</div>
                    <div class="kpi-label">Asistencias ({{ $labelPeriodo }})</div>
                </div>
                <i class="fas fa-walking kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card"><div><div class="kpi-value">{{ number_format($clientesInactivos) }}</div><div class="kpi-label">Clientes inactivos</div></div><i class="fas fa-user-slash kpi-icon"></i></div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card"><div><div class="kpi-value">{{ number_format($membresiasVencidas) }}</div><div class="kpi-label">Membresías vencidas</div></div><i class="fas fa-calendar-times kpi-icon"></i></div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card"><div><div class="kpi-value">{{ $gymConfig->simbolo_moneda }} {{ number_format($ingresosHoy, 2) }}</div><div class="kpi-label">Ingresos de hoy</div></div><i class="fas fa-cash-register kpi-icon"></i></div>
        </div>
        <div class="col-xl-2 col-md-4 mb-3 mb-xl-0">
            <div class="kpi-card"><div><div class="kpi-value">{{ number_format($asistenciasHoy) }}</div><div class="kpi-label">Asistencias de hoy</div></div><i class="fas fa-walking kpi-icon"></i></div>
        </div>
    </div>

    <div class="row dashboard-row dashboard-summary">
        <div class="col-md-3"><div class="ic-card p-3"><small class="text-muted">Ingresos del mes</small><div class="h5 mb-0 font-weight-bold">{{ $gymConfig->simbolo_moneda }} {{ number_format($ingresosMes, 2) }}</div></div></div>
        <div class="col-md-3"><div class="ic-card p-3"><small class="text-muted">Membresías activas / próximas a vencer</small><div class="h5 mb-0 font-weight-bold">{{ number_format($membresiasActivas) }} / {{ number_format($membresiasProximas) }}</div></div></div>
        <div class="col-md-3"><div class="ic-card p-3"><small class="text-muted">Puntos acumulados</small><div class="h5 mb-0 font-weight-bold">{{ number_format($puntosAcumulados) }} <i class="fas fa-star text-warning"></i></div></div></div>
        <div class="col-md-3"><div class="ic-card p-3"><small class="text-muted">Cliente con más puntos</small><div class="h5 mb-0 font-weight-bold">{{ $clienteMasPuntos?->nombre }} {{ $clienteMasPuntos?->apellido }} <small>({{ number_format($clienteMasPuntos?->puntos_ecogim ?? 0) }})</small></div></div></div>
    </div>

    <!-- Charts -->
    <div class="row dashboard-row dashboard-charts dashboard-primary-charts">
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

    <div class="row dashboard-row dashboard-charts dashboard-secondary-charts">
        <div class="col-lg-4"><div class="ic-card h-100"><div class="ic-card-header">Ingresos por mes</div><div class="card-body"><div class="chart-container"><canvas id="ingresosMensualesChart"></canvas></div></div></div></div>
        <div class="col-lg-4"><div class="ic-card h-100"><div class="ic-card-header">Membresías por estado</div><div class="card-body"><div class="chart-container"><canvas id="membresiasEstadoChart"></canvas></div></div></div></div>
        <div class="col-lg-4"><div class="ic-card h-100"><div class="ic-card-header">Asistencias y clientes nuevos</div><div class="card-body"><div class="chart-container"><canvas id="actividadMensualChart"></canvas></div></div></div></div>
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
                    <table id="vencimientosTable" class="table ic-table table-hover">
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
                                    <div class="font-weight-bold">{{ $vencimiento->cliente?->nombre_completo ?? 'Cliente no encontrado' }}</div>
                                    <div class="text-muted small">{{ $vencimiento->cliente?->telefono ?? 'Sin teléfono' }}</div>
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
function actualizarFiltrosDashboard() {
    const periodo = document.getElementById('periodo').value;
    document.getElementById('filtroMes').classList.toggle('d-none', periodo === 'rango');
    document.getElementById('filtroSemana').classList.toggle('d-none', periodo !== 'semana');
    document.getElementById('filtroFechas').classList.toggle('d-none', periodo !== 'rango');
    document.getElementById('filtroHasta').classList.toggle('d-none', periodo !== 'rango');
}

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

    const labelsMensuales = {!! json_encode($chartMesesLabels) !!};
    const ingresosMensuales = document.getElementById('ingresosMensualesChart');
    if (ingresosMensuales) {
        new Chart(ingresosMensuales, { type: 'line', data: { labels: labelsMensuales, datasets: [{ label: 'Ingresos', data: {!! json_encode($chartIngresosMensuales) !!}, borderColor: primaryColor, backgroundColor: primaryColor + '33', fill: true, tension: .3 }] }, options: { responsive: true, maintainAspectRatio: false } });
    }

    const membresiasEstado = document.getElementById('membresiasEstadoChart');
    if (membresiasEstado) {
        new Chart(membresiasEstado, { type: 'doughnut', data: { labels: {!! json_encode($chartMembresiasLabels) !!}, datasets: [{ data: {!! json_encode($chartMembresiasData) !!}, backgroundColor: ['#10B981', '#F59E0B', '#EF4444'] }] }, options: { responsive: true, maintainAspectRatio: false } });
    }

    const actividadMensual = document.getElementById('actividadMensualChart');
    if (actividadMensual) {
        new Chart(actividadMensual, { type: 'bar', data: { labels: labelsMensuales, datasets: [{ label: 'Asistencias', data: {!! json_encode($chartAsistenciasMensuales) !!}, backgroundColor: primaryColor }, { label: 'Clientes nuevos', data: {!! json_encode($chartNuevosClientes) !!}, backgroundColor: '#10B981' }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } } });
    }
});
</script>
@endpush
