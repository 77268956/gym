<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Ingresos del periodo</div><p class="report-stat-value">{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($finanzas['ingresos'], 2) }}</p></div><i class="fas fa-money-bill-wave report-stat-icon"></i></div>
    </div>
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Clientes nuevos</div><p class="report-stat-value">{{ number_format($clientes['nuevos']) }}</p></div><i class="fas fa-user-plus report-stat-icon"></i></div>
    </div>
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Asistencias de clientes</div><p class="report-stat-value">{{ number_format($clientes['asistencias']) }}</p></div><i class="fas fa-door-open report-stat-icon"></i></div>
    </div>
    <div class="col-xl-3 col-md-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Alertas pendientes</div><p class="report-stat-value">{{ number_format($sistema['pendientes']) }}</p></div><i class="fas fa-bell report-stat-icon"></i></div>
    </div>
</div>