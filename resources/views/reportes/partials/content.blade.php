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

<details id="reporte-finanzas" class="ic-card report-accordion mb-3" @if(empty($modoImpresion)) open @endif>
    <summary class="report-accordion-toggle">
        <span><i class="fas fa-coins mr-2 text-primary"></i>Reporte financiero</span>
        <span class="report-accordion-icon"><i class="fas fa-plus report-icon-plus"></i><i class="fas fa-minus report-icon-minus"></i></span>
    </summary>
    <div class="report-accordion-content">
    <div class="row no-gutters">
        <div class="col-md-4 p-3 border-right"><small class="text-muted d-block">Transacciones</small><strong>{{ number_format($finanzas['transacciones']) }}</strong></div>
        <div class="col-md-4 p-3 border-right"><small class="text-muted d-block">Promedio por pago</small><strong>{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($finanzas['promedio'], 2) }}</strong></div>
        <div class="col-md-4 p-3"><small class="text-muted d-block">Ingresos registrados</small><strong>{{ $gymConfig->simbolo_moneda ?? '$' }} {{ number_format($finanzas['ingresos'], 2) }}</strong></div>
    </div>
    <div class="row no-gutters border-top">
        <div class="col-lg-8 border-right">
            <div class="report-chart-panel"><div class="report-chart-title">Ingresos por fecha</div><canvas id="chartIngresos" aria-label="Gráfica de ingresos por fecha"></canvas></div>
        </div>
        <div class="col-lg-4">
            <div class="report-chart-panel"><div class="report-chart-title">Distribución por método</div><canvas id="chartMetodosPago" aria-label="Gráfica de métodos de pago"></canvas></div>
        </div>
    </div>
    <div class="table-responsive">
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
    </div>
    <div class="ic-card-header border-top">Pagos recientes</div>
    <div class="table-responsive">
        <table class="table ic-table">
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
</details>

<details id="reporte-clientes" class="ic-card report-accordion mb-3" @if(!empty($modoImpresion)) open @endif>
    <summary class="report-accordion-toggle">
        <span><i class="fas fa-users mr-2 text-primary"></i>Reporte de clientes</span>
        <span class="report-accordion-icon"><i class="fas fa-plus report-icon-plus"></i><i class="fas fa-minus report-icon-minus"></i></span>
    </summary>
    <div class="report-accordion-content">
    <div class="row no-gutters">
        <div class="col-md-3 col-6 p-3 border-right border-bottom"><small class="text-muted d-block">Clientes nuevos</small><strong>{{ number_format($clientes['nuevos']) }}</strong></div>
        <div class="col-md-3 col-6 p-3 border-right border-bottom"><small class="text-muted d-block">Clientes activos</small><strong>{{ number_format($clientes['activos']) }}</strong></div>
        <div class="col-md-3 col-6 p-3 border-right"><small class="text-muted d-block">Membresías con vencimiento en el rango</small><strong>{{ number_format($clientes['membresiasPorVencer']) }}</strong></div>
        <div class="col-md-3 col-6 p-3"><small class="text-muted d-block">Visitas registradas</small><strong>{{ number_format($clientes['asistencias']) }}</strong></div>
    </div>
    <div class="border-top">
        <div class="report-chart-panel"><div class="report-chart-title">Visitas y nuevos clientes por fecha</div><canvas id="chartClientes" aria-label="Gráfica de visitas y nuevos clientes"></canvas></div>
    </div>
    <div class="ic-card-header border-top">Clientes registrados en el periodo</div>
    <div class="table-responsive">
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
</details>

<details id="reporte-empleados" class="ic-card report-accordion mb-3" @if(!empty($modoImpresion)) open @endif>
    <summary class="report-accordion-toggle">
        <span><i class="fas fa-user-clock mr-2 text-primary"></i>Reporte de empleados</span>
        <span class="report-accordion-icon"><i class="fas fa-plus report-icon-plus"></i><i class="fas fa-minus report-icon-minus"></i></span>
    </summary>
    <div class="report-accordion-content">
    <div class="row no-gutters">
        <div class="col-md col-6 p-3 border-right border-bottom"><small class="text-muted d-block">Personal activo</small><strong>{{ number_format($empleados['total']) }}</strong></div>
        <div class="col-md col-6 p-3 border-right border-bottom"><small class="text-muted d-block">Asistencias</small><strong>{{ number_format($empleados['asistencias']) }}</strong></div>
        <div class="col-md col-4 p-3 border-right"><small class="text-muted d-block">Llegadas tarde</small><strong>{{ number_format($empleados['tardanzas']) }}</strong></div>
        <div class="col-md col-4 p-3 border-right"><small class="text-muted d-block">Salidas tempranas</small><strong>{{ number_format($empleados['salidasTempranas']) }}</strong></div>
        <div class="col-md col-4 p-3"><small class="text-muted d-block">Salidas sin marcar</small><strong>{{ number_format($empleados['salidasSinMarcar']) }}</strong></div>
    </div>
    <div class="border-top">
        <div class="report-chart-panel"><div class="report-chart-title">Incidencias de asistencia del periodo</div><canvas id="chartEmpleados" aria-label="Gráfica de incidencias de empleados"></canvas></div>
    </div>
    <div class="ic-card-header border-top">Incidencias de asistencia</div>
    <div class="table-responsive">
        <table class="table ic-table">
            <thead><tr><th>Fecha</th><th>Empleado</th><th>Entrada</th><th>Salida</th><th>Incidencias</th></tr></thead>
            <tbody>
                @forelse($empleados['incidencias'] as $asistencia)
                    <tr>
                        <td>{{ $asistencia->fecha?->format('d/m/Y') }}</td><td>{{ $asistencia->empleado?->nombre ?? 'Empleado eliminado' }}</td>
                        <td>{{ $asistencia->hora_entrada ?? '—' }}</td><td>{{ $asistencia->hora_salida ?? '—' }}</td>
                        <td>
                            @if($asistencia->tardanza)<span class="badge badge-warning">Tarde</span>@endif
                            @if($asistencia->salida_temprana)<span class="badge badge-info">Salida temprana</span>@endif
                            @if($asistencia->salida_no_registrada)<span class="badge badge-danger">Sin marcar salida</span>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="report-empty">No hay incidencias de asistencia en este periodo.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</details>

<details id="reporte-sistema" class="ic-card report-accordion mb-3" @if(!empty($modoImpresion)) open @endif>
    <summary class="report-accordion-toggle">
        <span><i class="fas fa-shield-alt mr-2 text-primary"></i>Reporte del sistema y notificaciones</span>
        <span class="report-accordion-icon"><i class="fas fa-plus report-icon-plus"></i><i class="fas fa-minus report-icon-minus"></i></span>
    </summary>
    <div class="report-accordion-content">
    <div class="row no-gutters">
        <div class="col-md-6 p-3 border-right"><small class="text-muted d-block">Alertas generadas en el periodo</small><strong>{{ number_format($sistema['total']) }}</strong></div>
        <div class="col-md-6 p-3"><small class="text-muted d-block">Alertas pendientes del periodo</small><strong>{{ number_format($sistema['pendientes']) }}</strong></div>
    </div>
    <div class="border-top">
        <div class="report-chart-panel"><div class="report-chart-title">Alertas por tipo</div><canvas id="chartSistema" aria-label="Gráfica de alertas del sistema"></canvas></div>
    </div>
    <div class="table-responsive">
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
    </div>
    <div class="ic-card-header border-top">Alertas recientes</div>
    <div class="table-responsive">
        <table class="table ic-table">
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
</details>
