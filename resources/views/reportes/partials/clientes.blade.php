<div class="row mb-3">
    <div class="col-md-3 col-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Clientes nuevos</div><p class="report-stat-value">{{ number_format($clientes['nuevos']) }}</p></div><i class="fas fa-user-plus report-stat-icon"></i></div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Clientes activos</div><p class="report-stat-value">{{ number_format($clientes['activos']) }}</p></div><i class="fas fa-user-check report-stat-icon"></i></div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Membresías por vencer</div><p class="report-stat-value">{{ number_format($clientes['membresiasPorVencer']) }}</p></div><i class="fas fa-hourglass-half report-stat-icon"></i></div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Visitas registradas</div><p class="report-stat-value">{{ number_format($clientes['asistencias']) }}</p></div><i class="fas fa-door-open report-stat-icon"></i></div>
    </div>
</div>

<div class="row flex-grow-1" style="min-height: 0;">
    <div class="col-lg-5 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-chart-line text-primary mr-2"></i>Visitas y nuevos clientes</div>
            <div class="report-chart-panel flex-grow-1"><canvas id="chartClientes" aria-label="Gráfica de visitas y nuevos clientes"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-users text-primary mr-2"></i>Clientes registrados en el periodo</div>
            <div class="table-responsive flex-grow-1">
                <table class="table ic-table">
                    <thead><tr><th>Cliente</th><th>Cédula</th><th>Fecha de registro</th></tr></thead>
                    <tbody>
                        @forelse($clientes['nuevosClientes'] as $cliente)
                            <tr><td>{{ $cliente->nombre }}</td><td>{{ $cliente->cedula }}</td><td>{{ $cliente->created_at?->format('d/m/Y') }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="report-empty">No hay nuevos clientes en este periodo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
