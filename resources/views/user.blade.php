@extends('layouts.app')

@section('title', 'Socios / Clientes')

@push('styles')
<style>
    :root {
        --ic-accent: #2f7d72;
        --ic-accent-soft: #e6f2f0;
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
    .ic-badge-pending  { background: #fff4e0; color: #b7791f; font-weight: 600; }
    .ic-badge-critical { background: #fde3e3; color: var(--ic-red); font-weight: 600; }
    .ic-badge-warn     { background: #fff4e0; color: #b7791f; font-weight: 600; }
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
        border-color: #cfe6e1;
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
        background: #25665d;
        border-color: #25665d;
        box-shadow: 0 3px 8px rgba(47, 125, 114, .2);
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
            ['label' => 'TOTAL DE CLIENTES',     'value' => number_format($totalClientes ?? 1842),       'delta' => '+4.2%',  'up' => true],
            ['label' => 'CLIENTES ACTIVOS',       'value' => number_format($clientesActivos ?? 1605),     'delta' => '+2.6%',  'up' => true],
            ['label' => 'MEMBRESÍAS POR VENCER',  'value' => number_format($membresiasPorVencer ?? 37),   'delta' => '-12.0%', 'up' => false],
            ['label' => 'NUEVOS ESTE MES',        'value' => number_format($nuevosClientes ?? 158),       'delta' => '+9.4%',  'up' => true],
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

    {{-- ===== ROW 2 — Gráfico de crecimiento + Clientes constantes ===== --}}
    <div class="row mb-3">
        <div class="col-lg-8 mb-3">
            <div class="ic-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="ic-card-title">CRECIMIENTO DE CLIENTES (12 MESES)</span>
                    <span class="ic-stat-label"><span class="ic-badge-active px-2 py-1 rounded-pill">● Clientes Activos</span></span>
                </div>
                <canvas id="growthChart" height="90"></canvas>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="ic-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="ic-card-title">CLIENTES MÁS CONSTANTES</span>
                    <span class="ic-badge-active px-2 py-1 rounded-pill" style="font-size:.7rem">TOP 3</span>
                </div>
                @php
                    $listaTop = $topClientes ?? [
                        (object)['nombre' => 'Marcus Vance', 'dato' => '28 asistencias este mes', 'racha' => 'Racha de 14 días', 'nivel' => 'active'],
                        (object)['nombre' => 'Sarah Connor', 'dato' => '24 asistencias este mes', 'racha' => 'Racha de 9 días', 'nivel' => 'active'],
                        (object)['nombre' => 'John Kreese', 'dato' => '21 asistencias este mes', 'racha' => 'Racha de 6 días', 'nivel' => 'warn'],
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
                <p class="ic-list-sub mb-0">Sin datos de asistencia todavía.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===== ROW 3 — Tabla de clientes + Membresías por vencer ===== --}}
    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="ic-card h-100" style="padding: .85rem .95rem;">
                <div class="ic-client-toolbar d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <span class="ic-card-title">LISTADO DE CLIENTES</span>

                    <div class="ic-client-filters d-flex align-items-center mt-2 mt-md-0">
                        <div class="input-group input-group-sm ic-search mr-2" style="width: 220px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search" style="font-size:.75rem;color:var(--ic-muted)"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Buscar por nombre o cédula...">
                        </div>
                        <select class="form-control form-control-sm ic-filter mr-2" style="width: 120px;">
                            <option selected>Estado</option>
                            <option>Activo</option>
                            <option>Inactivo</option>
                        </select>
                        <select class="form-control form-control-sm ic-filter" style="width: 140px;">
                            <option selected>Membresía</option>
                            <option>Activa</option>
                            <option>Vencida</option>
                            <option>Sin membresía</option>
                        </select>
                        <button type="button" class="ic-add-client btn btn-sm text-white ml-2">
                            <i class="fas fa-plus mr-1"></i> Añadir cliente
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table ic-table mb-0">
                        <thead>
                            <tr>
                                <th>CLIENTE</th>
                                <th>CÉDULA</th>
                                <th>TELÉFONO</th>
                                <th>MEMBRESÍA</th>
                                <th>PUNTOS</th>
                                <th>ÚLTIMA ACTIVIDAD</th>
                                <th>ESTADO</th>
                                <th class="ic-action-cell"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $listaClientes = $clientes ?? [
                                    (object)['nombre' => 'Logan Cole',    'cedula' => '0801-1990-04521', 'telefono' => '(504) 9812-3344', 'membresia' => 'activa',        'puntos' => 240, 'ultima_actividad' => 'Hoy, 09:42 AM',  'estado' => 'activo'],
                                    (object)['nombre' => 'Diana Prince',  'cedula' => '0801-1988-11023', 'telefono' => '(504) 9945-2210', 'membresia' => 'vencida',       'puntos' => 80,  'ultima_actividad' => 'Hace 6 días',    'estado' => 'activo'],
                                    (object)['nombre' => 'Roy Harper',    'cedula' => '0801-1995-33871', 'telefono' => '(504) 9877-6612', 'membresia' => 'activa',        'puntos' => 130, 'ultima_actividad' => 'Ayer, 08:50 AM', 'estado' => 'activo'],
                                    (object)['nombre' => 'Selina Kyle',   'cedula' => '0801-1992-00456', 'telefono' => '(504) 9822-9034', 'membresia' => 'sin_membresia', 'puntos' => 0,   'ultima_actividad' => 'Hace 63 días',   'estado' => 'inactivo'],
                                ];
                            @endphp
                            @forelse ($listaClientes as $cliente)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle ic-avatar d-flex align-items-center justify-content-center mr-2">
                                            {{ collect(explode(' ', $cliente->nombre))->map(fn($p) => strtoupper($p[0] ?? ''))->take(2)->implode('') }}
                                        </div>
                                        <div>
                                            <div class="ic-client-name">{{ $cliente->nombre }}</div>
                                            <div class="ic-client-id">Cliente registrado</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $cliente->cedula }}</td>
                                <td class="text-muted">{{ $cliente->telefono }}</td>
                                <td>
                                    @php
                                        $mBadge = match($cliente->membresia) {
                                            'activa' => 'ic-badge-active',
                                            'vencida' => 'ic-badge-critical',
                                            default  => 'ic-badge-full',
                                        };
                                        $mLabel = match($cliente->membresia) {
                                            'activa' => 'ACTIVA',
                                            'vencida' => 'VENCIDA',
                                            default  => 'SIN MEMBRESÍA',
                                        };
                                    @endphp
                                    <span class="{{ $mBadge }} px-2 py-1 rounded-pill" style="font-size:.7rem">{{ $mLabel }}</span>
                                </td>
                                <td class="ic-points">{{ $cliente->puntos }} pts</td>
                                <td class="text-muted">{{ $cliente->ultima_actividad }}</td>
                                <td>
                                    <span class="{{ $cliente->estado === 'activo' ? 'ic-badge-active' : 'ic-badge-full' }} px-2 py-1 rounded-pill" style="font-size:.7rem">
                                        {{ strtoupper($cliente->estado) }}
                                    </span>
                                </td>
                                <td class="text-right ic-action-cell">
                                    <div class="dropdown ic-client-actions">
                                        <button type="button" class="btn btn-sm ic-action-button d-inline-flex align-items-center justify-content-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Acciones del cliente">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"><i class="fas fa-eye"></i> Ver expediente</a>
                                            <a class="dropdown-item" href="#"><i class="fas fa-pen"></i> Editar cliente</a>
                                            <a class="dropdown-item" href="#"><i class="fas fa-id-card"></i> Ver membresía</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt"></i> Eliminar</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-muted">No hay clientes registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="ic-list-sub">Mostrando 4 de {{ $totalClientes ?? 1842 }} clientes</span>
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

        {{-- Membresías por vencer --}}
        <div class="col-lg-4 mb-3">
            <div class="ic-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="ic-card-title">MEMBRESÍAS POR VENCER</span>
                    <span class="ic-badge-critical px-2 py-1 rounded-pill" style="font-size:.7rem">4 PRÓXIMAS</span>
                </div>
                @php
                    $listaVencer = $porVencer ?? [
                        (object)['nombre' => 'Logan Cole',   'plan' => 'Black Pass Anual',    'dias' => 2, 'nivel' => 'critical'],
                        (object)['nombre' => 'Diana Prince', 'plan' => 'Standard Mensual',    'dias' => 3, 'nivel' => 'critical'],
                        (object)['nombre' => 'Roy Harper',   'plan' => 'Performance Tier',    'dias' => 5, 'nivel' => 'warn'],
                        (object)['nombre' => 'Selina Kyle',  'plan' => 'Black Pass Mensual',  'dias' => 5, 'nivel' => 'warn'],
                    ];
                @endphp
                @forelse ($listaVencer as $item)
                <div class="ic-list-item">
                    <div>
                        <div class="ic-list-title">{{ $item->nombre }}</div>
                        <div class="ic-list-sub">{{ $item->plan }}</div>
                    </div>
                    <div class="text-right">
                        <span class="{{ $item->nivel === 'critical' ? 'ic-badge-critical' : 'ic-badge-warn' }} px-2 py-1 rounded-pill" style="font-size:.65rem">
                            VENCE EN {{ $item->dias }}D
                        </span>
                    </div>
                </div>
                @empty
                <p class="ic-list-sub mb-0">No hay membresías por vencer.</p>
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
                    borderColor: '#2f7d72',
                    backgroundColor: 'rgba(47,125,114,0.10)',
                    pointBackgroundColor: '#2f7d72',
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