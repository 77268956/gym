<div class="row mb-3">
    <div class="col-md col-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Personal activo</div><p class="report-stat-value">{{ number_format($empleados['total']) }}</p></div><i class="fas fa-user-clock report-stat-icon"></i></div>
    </div>
    <div class="col-md col-6 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Asistencias</div><p class="report-stat-value">{{ number_format($empleados['asistencias']) }}</p></div><i class="fas fa-clipboard-check report-stat-icon"></i></div>
    </div>
    <div class="col-md col-4 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Llegadas tarde</div><p class="report-stat-value">{{ number_format($empleados['tardanzas']) }}</p></div><i class="fas fa-stopwatch report-stat-icon"></i></div>
    </div>
    <div class="col-md col-4 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Salidas tempranas</div><p class="report-stat-value">{{ number_format($empleados['salidasTempranas']) }}</p></div><i class="fas fa-sign-out-alt report-stat-icon"></i></div>
    </div>
    <div class="col-md col-4 mb-2">
        <div class="report-stat"><div><div class="report-stat-label">Salidas sin marcar</div><p class="report-stat-value">{{ number_format($empleados['salidasSinMarcar']) }}</p></div><i class="fas fa-exclamation-circle report-stat-icon"></i></div>
    </div>
</div>

<div class="row flex-grow-1" style="min-height: 0;">
    <div class="col-lg-5 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-chart-bar text-primary mr-2"></i>Incidencias de asistencia</div>
            <div class="report-chart-panel flex-grow-1"><canvas id="chartEmpleados" aria-label="Gráfica de incidencias de empleados"></canvas></div>
        </div>
    </div>
    <div class="col-lg-7 d-flex flex-column mb-3">
        <div class="ic-card flex-card h-100 m-0">
            <div class="ic-card-header"><i class="fas fa-list text-primary mr-2"></i>Detalle de incidencias</div>
            <div class="table-responsive flex-grow-1">
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
    </div>
</div>
