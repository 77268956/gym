@extends('layouts.app')

@section('title', 'Reportes')

@push('styles')
@include('reportes.partials.styles')
<style>
    .report-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:1.15rem; }
</style>
@endpush

@section('content')
<div class="container-fluid reports-page">
    <div class="reports-page-scrollable pt-2 pl-2">
    @include('reportes.partials.resumen')

    <div class="report-grid mb-3">
        <a href="{{ route('reportes.finanzas') }}" class="report-card-link">
            <div class="report-card-head">
                <span class="report-card-icon"><i class="fas fa-coins"></i></span>
                <span class="report-card-title">Reporte financiero</span>
            </div>
            <div class="report-card-stats">
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Ingresos del periodo</span>
                    <span class="report-stat-item-value">{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($finanzas['ingresos'], 2) }}</span>
                </div>
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Transacciones</span>
                    <span class="report-stat-item-value">{{ number_format($finanzas['transacciones']) }}</span>
                </div>
            </div>
            <div class="report-card-mini-chart">
                <span class="report-card-mini-title">Ingresos por fecha</span>
                <div class="report-card-mini-wrap">
                    <canvas id="miniChartFinanzas" aria-label="Gráfica mínima de ingresos financieros"></canvas>
                </div>
                @if($finanzas['transacciones'] == 0)
                    <div class="report-card-mini-empty">Sin movimientos</div>
                @endif
            </div>
            <span class="report-card-cta">Ver estadísticas completas <i class="fas fa-arrow-right"></i></span>
        </a>
        <a href="{{ route('reportes.clientes') }}" class="report-card-link">
            <div class="report-card-head">
                <span class="report-card-icon"><i class="fas fa-users"></i></span>
                <span class="report-card-title">Reporte de clientes</span>
            </div>
            <div class="report-card-stats">
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Clientes nuevos</span>
                    <span class="report-stat-item-value">{{ number_format($clientes['nuevos']) }}</span>
                </div>
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Clientes activos</span>
                    <span class="report-stat-item-value">{{ number_format($clientes['activos']) }}</span>
                </div>
            </div>
            <div class="report-card-mini-chart">
                <span class="report-card-mini-title">Visitas y nuevos clientes</span>
                <div class="report-card-mini-wrap">
                    <canvas id="miniChartClientes" aria-label="Gráfica mínima de visitas y clientes"></canvas>
                </div>
                @if($clientes['asistencias'] == 0 && $clientes['nuevos'] == 0)
                    <div class="report-card-mini-empty">Sin actividad</div>
                @endif
            </div>
            <span class="report-card-cta">Ver estadísticas completas <i class="fas fa-arrow-right"></i></span>
        </a>
        <a href="{{ route('reportes.empleados') }}" class="report-card-link">
            <div class="report-card-head">
                <span class="report-card-icon"><i class="fas fa-user-clock"></i></span>
                <span class="report-card-title">Reporte de empleados</span>
            </div>
            <div class="report-card-stats">
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Asistencias</span>
                    <span class="report-stat-item-value">{{ number_format($empleados['asistencias']) }}</span>
                </div>
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Llegadas tarde</span>
                    <span class="report-stat-item-value">{{ number_format($empleados['tardanzas']) }}</span>
                </div>
            </div>
            <div class="report-card-mini-chart">
                <span class="report-card-mini-title">Incidencias de asistencia</span>
                <div class="report-card-mini-wrap">
                    <canvas id="miniChartEmpleados" aria-label="Gráfica mínima de incidencias de empleados"></canvas>
                </div>
                @if($empleados['asistencias'] == 0)
                    <div class="report-card-mini-empty">Sin actividad</div>
                @endif
            </div>
            <span class="report-card-cta">Ver estadísticas completas <i class="fas fa-arrow-right"></i></span>
        </a>
        <a href="{{ route('reportes.sistema') }}" class="report-card-link">
            <div class="report-card-head">
                <span class="report-card-icon"><i class="fas fa-shield-alt"></i></span>
                <span class="report-card-title">Reporte del sistema</span>
            </div>
            <div class="report-card-stats">
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Alertas pendientes</span>
                    <span class="report-stat-item-value">{{ number_format($sistema['pendientes']) }}</span>
                </div>
                <div class="report-stat-item">
                    <span class="report-stat-item-label">Alertas generadas</span>
                    <span class="report-stat-item-value">{{ number_format($sistema['total']) }}</span>
                </div>
            </div>
            <div class="report-card-mini-chart">
                <span class="report-card-mini-title">Alertas por tipo</span>
                <div class="report-card-mini-wrap">
                    <canvas id="miniChartSistema" aria-label="Gráfica mínima de alertas del sistema"></canvas>
                </div>
                @if($sistema['total'] == 0)
                    <div class="report-card-mini-empty">Sin alertas</div>
                @endif
            </div>
            <span class="report-card-cta">Ver estadísticas completas <i class="fas fa-arrow-right"></i></span>
        </a>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    (() => {
        const reportCharts = @json($graficas);
        const palette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'];
        const themePrimary = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#2563EB';
        const themeText = getComputedStyle(document.documentElement).getPropertyValue('--text-main').trim() || '#334155';
        const themePrimaryHover = getComputedStyle(document.documentElement).getPropertyValue('--primary-hover').trim() || themePrimary;

        Chart.defaults.color = themeText;

        const drawMini = (canvasId, type, labels, values, label, options = {}) => {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type,
                data: {
                    labels,
                    datasets: [{
                        label,
                        data: values,
                        borderColor: type === 'line' ? themePrimary : '#fff',
                        backgroundColor: type === 'line' ? `color-mix(in srgb, ${themePrimary} 12%, transparent)` : palette.slice(0, Math.max(1, values.length)),
                        borderWidth: type === 'line' ? 2 : 1,
                        borderRadius: type === 'bar' ? 4 : 0,
                        fill: type === 'line',
                        tension: .38,
                        pointRadius: 1.5,
                        pointHitRadius: 12,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 0 },
                    plugins: { legend: { display: false }, tooltip: { displayColors: false } },
                    scales: type === 'doughnut' ? {} : {
                        y: { display: false, beginAtZero: true, ticks: { precision: 0 } },
                        x: { display: false, grid: { display: false } }
                    },
                    ...options
                }
            });
        };

        drawMini('miniChartFinanzas', 'line', reportCharts.etiquetasDias, reportCharts.ingresosPorDia, 'Ingresos');
        const cCli = drawMini('miniChartClientes', 'line', reportCharts.etiquetasDias, reportCharts.visitasPorDia, 'Visitas');
        if (cCli) {
            const ctx = document.getElementById('miniChartClientes').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: reportCharts.etiquetasDias,
                    datasets: [
                        { label: 'Visitas', data: reportCharts.visitasPorDia, borderColor: themePrimary, backgroundColor: `color-mix(in srgb, ${themePrimary} 10%, transparent)`, fill: true, tension: .38, pointRadius: 1 },
                        { label: 'Nuevos', data: reportCharts.clientesNuevosPorDia, borderColor: themePrimaryHover, backgroundColor: `color-mix(in srgb, ${themePrimaryHover} 10%, transparent)`, fill: true, tension: .38, pointRadius: 1 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, animation: { duration: 0 }, plugins: { legend: { display: false } }, scales: { y: { display: false }, x: { display: false } } }
            });
        }
        drawMini('miniChartEmpleados', 'bar', ['Tarde', 'Temp.', 'Sin salida'], reportCharts.incidenciasEmpleados.valores, 'Incidencias', { scales: { y: { display: false }, x: { display: false } } });
        drawMini('miniChartSistema', 'doughnut', reportCharts.alertas.labels, reportCharts.alertas.valores, 'Alertas', { scales: {} });
    })();
</script>
@endpush