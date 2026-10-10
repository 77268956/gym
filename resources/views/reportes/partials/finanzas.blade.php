<div class="row mb-3">
    <div class="col-md-4 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Transacciones</div><p class="report-stat-value">{{ number_format($finanzas['transacciones']) }}</p></div><i class="fas fa-receipt report-stat-icon"></i></div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Promedio por pago</div><p class="report-stat-value">{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($finanzas['promedio'], 2) }}</p></div><i class="fas fa-calculator report-stat-icon"></i></div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Ingresos registrados</div><p class="report-stat-value">{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($finanzas['ingresos'], 2) }}</p></div><i class="fas fa-money-bill-wave report-stat-icon"></i></div>
    </div>
</div>

<div class="row flex-grow-1" style="min-height: 0;">
    <div class="col-lg-5 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-chart-line text-primary mr-2"></i>Ingresos y métodos de pago</div>
            <div class="report-chart-panel" style="flex: 1;"><div class="report-chart-title">Ingresos por fecha</div><canvas id="chartIngresos" aria-label="Gráfica de ingresos por fecha"></canvas></div>
            <div class="report-chart-panel border-top" style="flex: 1;"><div class="report-chart-title">Distribución por método</div><canvas id="chartMetodosPago" aria-label="Gráfica de métodos de pago"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-list text-primary mr-2"></i>Detalle de ingresos y pagos recientes</div>
            <div class="table-responsive flex-grow-1">
                <table class="table ic-table">
                    <thead><tr><th>Método de pago</th><th>Transacciones</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($finanzas['porMetodo'] as $metodo)
                            <tr><td class="text-capitalize">{{ $metodo->metodo_pago }}</td><td>{{ number_format($metodo->cantidad) }}</td><td>{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($metodo->total, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="report-empty">No hay pagos en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="ic-card-header border-top" style="position: sticky; top: 0; z-index: 10;">Pagos recientes</div>
                <table class="table ic-table mb-0">
                    <thead><tr><th>Fecha</th><th>Cliente</th><th>Atendió</th><th>Concepto</th><th>Monto</th></tr></thead>
                    <tbody>
                        @forelse($finanzas['pagosRecientes'] as $pago)
                            <tr><td>{{ $pago->fecha_pago?->format('d/m/Y H:i') }}</td><td>{{ $pago->cliente?->nombre ?? 'Cliente eliminado' }}</td><td>{{ $pago->empleado?->nombre ?? '—' }}</td><td class="text-capitalize">{{ str_replace('_', ' ', $pago->tipo_pago) }}</td><td>{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($pago->monto, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="5" class="report-empty">No hay pagos en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
