@extends('layouts.app')

@section('title', 'Empleados')

@push('styles')
<style>
    :root {
        --ic-accent: #0082CA;
        --ic-accent-soft: #E5F4FC;
        --ic-green: #16a34a;
        --ic-red: #dc2626;
        --ic-border: #e9e9e7;
        --ic-muted: #8a8a86;
    }
    .ic-card {
        background: #fff;
        border: 1px solid var(--ic-border);
        border-radius: .75rem;
        padding: .55rem .65rem;
        overflow: hidden;
    }
    /* ── Stat cards ── */
    .ic-stat-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .45rem;
        min-height: 62px;
    }
    .ic-stat-card .ic-stat-label {
        line-height: 1.2;
        max-width: 46%;
    }
    .ic-stat-summary {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .25rem;
        min-width: 0;
        text-align: center;
    }
    .ic-stat-details {
        display: flex;
        align-items: baseline;
        gap: .35rem;
        line-height: 1.1;
        white-space: nowrap;
        justify-content: center;
    }
    .ic-stat-chart-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 84px;
        height: 26px;
        padding: 2px 5px;
        background: #fafaf8;
        border: 1px solid var(--ic-border);
        border-radius: .35rem;
    }
    .ic-stat-sparkline {
        display: block;
        width: 72px !important;
        height: 20px !important;
        max-width: 72px;
        max-height: 20px;
        flex: 0 0 72px;
    }
    #growthChart {
        display: block;
        width: 100% !important;
        max-width: 100%;
        height: 90px !important;
    }
    .ic-stat-label {
        font-size: .72rem;
        color: var(--ic-muted);
        letter-spacing: .02em;
    }
    .ic-stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1c1c1a;
    }
    .ic-delta-up   { color: var(--ic-green); font-size: .75rem; font-weight: 600; }
    .ic-delta-down { color: var(--ic-red); font-size: .75rem; font-weight: 600; }
    .ic-card-title {
        font-size: .8rem;
        font-weight: 700;
        color: #1c1c1a;
    }
    /* ── Badges ── */
    .ic-badge-active   { background: var(--ic-accent-soft); color: var(--ic-accent); font-weight: 600; }
    .ic-badge-full     { background: #f1f1ef; color: #6b6b66; font-weight: 600; }
    .ic-badge-pending  { background: #FFF8DD; color: #8A6D16; font-weight: 600; }
    .ic-badge-critical { background: #fde3e3; color: var(--ic-red); font-weight: 600; }
    .ic-badge-warn     { background: #FFF8DD; color: #8A6D16; font-weight: 600; }
    /* ── List items ── */
    .ic-list-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: .45rem 0;
        border-bottom: 1px solid var(--ic-border);
    }
    .ic-list-item:last-child { border-bottom: none; }
    .ic-list-title { font-size: .85rem; font-weight: 600; color: #1c1c1a; }
    .ic-list-sub   { font-size: .75rem; color: var(--ic-muted); }
    /* ── Table ── */
    .ic-table thead th {
        font-size: .67rem;
        text-transform: uppercase;
        color: var(--ic-muted);
        background: #fafaf8;
        border-bottom: 1px solid var(--ic-border);
        font-weight: 600;
        letter-spacing: .04em;
        white-space: nowrap;
    }
    .ic-table td {
        font-size: .82rem;
        vertical-align: middle;
        border-top: 1px solid #f1f1ef;
        white-space: nowrap;
    }
    .ic-table tbody tr { transition: background-color .15s ease; }
    .ic-table tbody tr:hover { background: #fbfdfc; }
    .ic-table .ic-client-name { color: #1c1c1a; font-weight: 600; }
    .ic-table .ic-client-id { color: var(--ic-muted); font-size: .72rem; }
    .ic-table .ic-points { color: #1c1c1a; font-weight: 600; }
    .ic-table .ic-action-cell { width: 48px; }
    .ic-action-button {
        width: 30px;
        height: 30px;
        color: var(--ic-muted);
        border: 1px solid transparent;
        border-radius: .4rem;
    }
    .ic-action-button:hover,
    .ic-action-button:focus {
        color: var(--ic-accent);
        background: var(--ic-accent-soft);
        border-color: #B8E1F4;
        box-shadow: none;
    }
    .ic-client-actions .dropdown-menu {
        min-width: 150px;
        padding: .35rem;
        border: 1px solid var(--ic-border);
        border-radius: .5rem;
        box-shadow: 0 8px 22px rgba(28, 28, 26, .10);
    }
    .ic-client-actions .dropdown-item {
        padding: .45rem .55rem;
        border-radius: .3rem;
        font-size: .78rem;
    }
    .ic-client-actions .dropdown-item i { width: 18px; color: var(--ic-muted); }
    .ic-client-actions .dropdown-item:hover { background: var(--ic-accent-soft); color: var(--ic-accent); }
    .ic-client-actions .dropdown-item.text-danger:hover { background: #fdeaea; color: var(--ic-red); }
    /* ── Avatars ── */
    .ic-avatar {
        width: 32px;
        height: 32px;
        font-size: .7rem;
        font-weight: 600;
        color: var(--ic-accent);
        background: var(--ic-accent-soft);
    }
    /* ── Search & filters ── */
    .ic-search .input-group-text {
        background: #fff;
        border-right: 0;
        border-color: var(--ic-border);
    }
    .ic-search .form-control {
        border-left: 0;
        border-color: var(--ic-border);
    }
    .ic-search .form-control:focus {
        box-shadow: none;
        border-color: var(--ic-accent);
    }
    .ic-filter {
        border-color: var(--ic-border);
        font-size: .8rem;
        color: #1c1c1a;
    }
    .ic-add-client {
        background: var(--ic-accent);
        border-color: var(--ic-accent);
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .ic-add-client:hover,
    .ic-add-client:focus {
        background: #0F2C59;
        border-color: #0F2C59;
        box-shadow: 0 3px 8px rgba(0, 130, 202, .2);
    }
    @media (max-width: 767.98px) {
        .ic-client-toolbar { align-items: stretch !important; }
        .ic-client-filters { width: 100%; flex-wrap: wrap; }
        .ic-client-filters .ic-search { width: 100% !important; margin-right: 0 !important; margin-bottom: .5rem; }
        .ic-client-filters .ic-filter { flex: 1 1 130px; width: auto !important; }
        .ic-add-client { flex: 1 1 100%; margin-top: .5rem; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- ===== ROW 1 — Fila de estadísticas ===== --}}
    <div class="row mb-3">
        @foreach ([
            ['label' => 'TOTAL DE EMPLEADOS',    'value' => number_format($totalEmpleados ?? 24),     'delta' => '+2.0%',  'up' => true],
            ['label' => 'EMPLEADOS ACTIVOS',      'value' => number_format($empleadosActivos ?? 22),   'delta' => '+4.5%',  'up' => true],
            ['label' => 'TURNOS POR CUBRIR',      'value' => number_format($turnosPorCubrir ?? 3),     'delta' => '-1.5%',  'up' => false],
            ['label' => 'NUEVOS ESTE MES',        'value' => number_format($nuevosEsteMes ?? 2),       'delta' => '+5.0%',  'up' => true],
        ] as $stat)
        <div class="col-6 col-lg-3 mb-3">
            <div class="ic-card ic-stat-card h-100">
                <span class="ic-stat-label">{{ $stat['label'] }}</span>
                <div class="ic-stat-summary">
                    <div class="ic-stat-details">
                        <span class="ic-stat-value">{{ $stat['value'] }}</span>
                        <span class="{{ $stat['up'] ? 'ic-delta-up' : 'ic-delta-down' }}">{{ $stat['delta'] }}</span>
                    </div>
                    <div class="ic-stat-chart-box">
                        <canvas class="ic-stat-sparkline" data-sparkline data-trend="{{ $stat['up'] ? 'up' : 'down' }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ===== ROW 2 — Gráfico de asistencia/horas + Empleados constantes ===== --}}
    <div class="row mb-3">
        <div class="col-lg-8 mb-3">
            <div class="ic-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="ic-card-title">HORAS TRABAJADAS (12 MESES)</span>
                    <span class="ic-stat-label"><span class="ic-badge-active px-2 py-1 rounded-pill">● Horas Promedio</span></span>
                </div>
                <canvas id="growthChart" height="90"></canvas>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="ic-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="ic-card-title">EMPLEADOS DESTACADOS</span>
                    <span class="ic-badge-active px-2 py-1 rounded-pill" style="font-size:.7rem">TOP 3</span>
                </div>
                @php
                    $listaTop = $topEmpleados ?? [
                        (object)['nombre' => 'Marcus Vance', 'dato' => '120 horas este mes', 'racha' => 'Puntualidad 100%', 'nivel' => 'active'],
                        (object)['nombre' => 'Sarah Connor', 'dato' => '115 horas este mes', 'racha' => 'Puntualidad 98%', 'nivel' => 'active'],
                        (object)['nombre' => 'John Kreese', 'dato' => '180 horas este mes', 'racha' => 'Puntualidad 95%', 'nivel' => 'warn'],
                    ];
                @endphp
                @forelse ($listaTop as $item)
                <div class="ic-list-item">
                    <div>
                        <div class="ic-list-title">{{ $item->nombre }}</div>
                        <div class="ic-list-sub">{{ $item->dato }}</div>
                    </div>
                    <div class="text-right">
                        <span class="{{ $item->nivel === 'active' ? 'ic-badge-active' : 'ic-badge-warn' }} px-2 py-1 rounded-pill" style="font-size:.65rem">
                            {{ strtoupper($item->racha) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="ic-list-sub mb-0">Sin datos registrados.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===== ROW 3 — Tabla de empleados + Turnos por cubrir ===== --}}
    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="ic-card h-100" style="padding: .85rem .95rem;">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <span class="ic-card-title">LISTADO DE EMPLEADOS</span>

                    <div class="d-flex">
                        <div class="input-group input-group-sm ic-search mr-2" style="width: 220px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search" style="font-size:.75rem;color:var(--ic-muted)"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Buscar por nombre o rol...">
                        </div>
                        <select class="form-control form-control-sm ic-filter mr-2" style="width: 120px;">
                            <option selected>Estado</option>
                            <option>Activo</option>
                            <option>Inactivo</option>
                        </select>
                        <select class="form-control form-control-sm ic-filter" style="width: 140px;">
                            <option selected>Turno</option>
                            <option>Mañana</option>
                            <option>Tarde</option>
                            <option>Noche</option>
                            <option>Completo</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table ic-table mb-0">
                        <thead>
                            <tr>
                                <th>EMPLEADO</th>
                                <th>CÉDULA</th>
                                <th>TELÉFONO</th>
                                <th>ROL</th>
                                <th>EXPERIENCIA</th>
                                <th>TURNO</th>
                                <th>ESTADO</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $listaEmpleados = $empleados ?? [
                                    (object)['nombre' => 'Marcus Vance', 'cedula' => '0801-1985-12345', 'telefono' => '(504) 9811-2233', 'rol' => 'entrenador', 'experiencia' => '5 años', 'turno' => 'Mañana', 'estado' => 'activo'],
                                    (object)['nombre' => 'Sarah Connor', 'cedula' => '0801-1990-54321', 'telefono' => '(504) 9922-3344', 'rol' => 'entrenador', 'experiencia' => '3 años', 'turno' => 'Tarde', 'estado' => 'activo'],
                                    (object)['nombre' => 'John Kreese', 'cedula' => '0801-1980-98765', 'telefono' => '(504) 9833-4455', 'rol' => 'coordinador', 'experiencia' => '10 años', 'turno' => 'Completo', 'estado' => 'activo'],
                                    (object)['nombre' => 'Amanda Waller', 'cedula' => '0801-1992-45678', 'telefono' => '(504) 9844-5566', 'rol' => 'recepcion', 'experiencia' => '1 año', 'turno' => 'Mañana', 'estado' => 'inactivo'],
                                ];
                            @endphp
                            @forelse ($listaEmpleados as $empleado)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle ic-avatar d-flex align-items-center justify-content-center mr-2">
                                            {{ collect(explode(' ', $empleado->nombre))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('') }}
                                        </div>
                                        <span class="ic-list-title">{{ $empleado->nombre }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $empleado->cedula }}</td>
                                <td class="text-muted">{{ $empleado->telefono }}</td>
                                <td>
                                    <span class="badge bg-light text-dark px-2 py-1 border" style="font-size:.7rem">{{ strtoupper($empleado->rol) }}</span>
                                </td>
                                <td>{{ $empleado->experiencia }}</td>
                                <td class="text-muted">{{ $empleado->turno }}</td>
                                <td>
                                    <span class="{{ $empleado->estado === 'activo' ? 'ic-badge-active' : 'ic-badge-full' }} px-2 py-1 rounded-pill" style="font-size:.7rem">
                                        {{ strtoupper($empleado->estado) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button class="btn btn-sm btn-light border-0" title="Ver expediente">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-muted">No hay empleados registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="ic-list-sub">Mostrando 4 de {{ $totalEmpleados ?? 24 }} empleados</span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                            <li class="page-item active"><a class="page-link" href="#" style="background:var(--ic-accent);border-color:var(--ic-accent)">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        {{-- Turnos por cubrir / proximos --}}
        <div class="col-lg-4 mb-3">
            <div class="ic-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="ic-card-title">TURNOS PRÓXIMOS</span>
                    <span class="ic-badge-critical px-2 py-1 rounded-pill" style="font-size:.7rem">3 PENDIENTES</span>
                </div>
                @php
                    $listaTurnos = $turnosProximos ?? [
                        (object)['nombre' => 'Marcus Vance', 'plan' => 'Tactical Conditioning', 'dias' => 1, 'nivel' => 'warn'],
                        (object)['nombre' => 'Sarah Connor', 'plan' => 'Iron Barbell Olympic', 'dias' => 1, 'nivel' => 'warn'],
                        (object)['nombre' => 'Amanda Waller', 'plan' => 'Recepción Principal', 'dias' => 2, 'nivel' => 'critical'],
                    ];
                @endphp
                @forelse ($listaTurnos as $item)
                <div class="ic-list-item">
                    <div>
                        <div class="ic-list-title">{{ $item->nombre }}</div>
                        <div class="ic-list-sub">{{ $item->plan }}</div>
                    </div>
                    <div class="text-right">
                        <span class="{{ $item->nivel === 'critical' ? 'ic-badge-critical' : 'ic-badge-warn' }} px-2 py-1 rounded-pill" style="font-size:.65rem">
                            EN {{ $item->dias }} DÍA(S)
                        </span>
                    </div>
                </div>
                @empty
                <p class="ic-list-sub mb-0">No hay turnos pendientes por cubrir.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Gráfico principal: crecimiento de clientes ── */
    var ctx = document.getElementById('growthChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'],
                datasets: [{
                    data: [1220,1280,1310,1350,1410,1480,1520,1560,1610,1680,1750,1842],
                    borderColor: '#0082CA',
                    backgroundColor: 'rgba(0,130,202,0.10)',
                    pointBackgroundColor: '#0082CA',
                    pointRadius: 3,
                    tension: 0.35,
                    fill: true,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { grid: { display: false }, ticks: { color: '#8a8a86', font: { size: 10 } } }
                }
            }
        });
    }

    /* ── Mini sparklines de las tarjetas ── */
    document.querySelectorAll('[data-sparkline]').forEach(function (canvas) {
        var up = canvas.dataset.trend === 'up';
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: Array(8).fill(''),
                datasets: [{
                    data: up ? [3,4,3,5,6,5,7,8] : [8,7,7,6,5,6,4,3],
                    borderColor: up ? '#16a34a' : '#dc2626',
                    borderWidth: 1.5,
                    pointRadius: 0,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { display: false } },
                elements: { line: { fill: false } }
            }
        });
    });
});
</script>
@endpush