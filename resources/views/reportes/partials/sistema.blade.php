<div class="row mb-3">
    <div class="col-md-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Alertas generadas en el periodo</div><p class="report-stat-value">{{ number_format($sistema['total']) }}</p></div><i class="fas fa-bell report-stat-icon"></i></div>
    </div>
    <div class="col-md-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Alertas pendientes del periodo</div><p class="report-stat-value">{{ number_format($sistema['pendientes']) }}</p></div><i class="fas fa-bell-slash report-stat-icon"></i></div>
    </div>
</div>

<div class="row flex-grow-1" style="min-height: 0;">
    <div class="col-lg-5 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-chart-pie text-primary mr-2"></i>Alertas por tipo</div>
            <div class="report-chart-panel flex-grow-1"><canvas id="chartSistema" aria-label="Gráfica de alertas del sistema"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-list text-primary mr-2"></i>Detalle y alertas recientes</div>
            <div class="table-responsive flex-grow-1">
                <table class="table ic-table">
                    <thead><tr><th>Tipo de alerta</th><th>Cantidad</th></tr></thead>
                    <tbody>
                        @forelse($sistema['porTipo'] as $tipo)
                            <tr><td>{{ ucfirst(str_replace('_', ' ', $tipo->tipo_alerta)) }}</td><td>{{ number_format($tipo->cantidad) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="report-empty">No hay alertas en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="ic-card-header border-top" style="position: sticky; top: 0; z-index: 10;">Alertas recientes</div>
                <table class="table ic-table mb-0">
                    <thead><tr><th>Fecha</th><th>Alerta</th><th>Estado</th><th>Detalle</th></tr></thead>
                    <tbody>
                        @forelse($sistema['recientes'] as $alerta)
                            <tr><td>{{ $alerta->created_at?->format('d/m/Y H:i') }}</td><td>{{ ucfirst(str_replace('_', ' ', $alerta->tipo_alerta)) }}</td><td class="text-capitalize">{{ $alerta->estado }}</td><td>{{ $alerta->mensaje }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="report-empty">No hay alertas en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
