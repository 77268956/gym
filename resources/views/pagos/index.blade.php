@extends('layouts.app')

@section('title', 'Pagos')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .badge-membresia-activa  { background:#D1FAE5; color:#059669; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-vencida { background:#FEE2E2; color:#EF4444; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-sin     { background:#F1F5F9; color:#475569; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    #modalClientesTable_wrapper .dataTables_filter { display:none; }
    #modalClientesTable_wrapper .dataTables_info,
    #modalClientesTable_wrapper .dataTables_paginate { font-size:.8rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- KPI Cards --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="ic-card ic-stat-card h-100" style="border-left:4px solid #2563EB">
                <span class="ic-stat-label">INGRESOS DE HOY</span>
                <div class="ic-stat-summary">
                    <div class="ic-stat-details">
                        <span class="ic-stat-value">{{ $gymConfig->simbolo_moneda }} {{ number_format($pagosHoy, 2) }}</span>
                    </div>
                    <div class="ic-stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ic-card ic-stat-card h-100" style="border-left:4px solid #10B981">
                <span class="ic-stat-label">INGRESOS TOTALES</span>
                <div class="ic-stat-summary">
                    <div class="ic-stat-details">
                        <span class="ic-stat-value text-success">{{ $gymConfig->simbolo_moneda }} {{ number_format($totalIngresos, 2) }}</span>
                    </div>
                    <div class="ic-stat-icon text-success"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Tabla de Movimientos --}}
    <div class="ic-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <span class="ic-card-title">HISTORIAL Y AUDITORÍA DE COBROS</span>
            <button type="button" class="btn btn-primary btn-sm font-weight-bold" data-toggle="modal" data-target="#modalClientes">
                <i class="fas fa-cash-register mr-1"></i> Procesar Nuevo Cobro
            </button>
        </div>

        <div class="table-responsive mt-2">
            <table id="pagosTable" class="table ic-table mb-0 w-100">
                <thead>
                    <tr>
                        <th>FECHA / HORA</th>
                        <th>CLIENTE</th>
                        <th>TIPO DE COBRO</th>
                        <th>MÉTODO</th>
                        <th>MONTO</th>
                        <th>CAJERO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagos as $pago)
                    <tr>
                        <td>
                            <span class="d-block font-weight-bold text-dark">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</span>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('h:i A') }}</small>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $pago->cliente->nombre ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $pago->cliente->cedula ?? '' }}</small>
                        </td>
                        <td>
                            @if($pago->tipo_pago === 'membresia')
                                <span class="badge badge-info px-2 py-1"><i class="fas fa-id-card mr-1"></i> Membresía</span>
                                @if($pago->membresia?->tipoMembresia)
                                    <small class="d-block mt-1 text-muted">{{ $pago->membresia->tipoMembresia->nombre }}</small>
                                @endif
                            @else
                                <span class="badge badge-warning px-2 py-1 text-dark"><i class="fas fa-ticket-alt mr-1"></i> Pase Diario</span>
                            @endif
                        </td>
                        <td>
                            @if($pago->metodo_pago === 'efectivo')
                                <span class="text-success"><i class="fas fa-money-bill-wave mr-1"></i> Efectivo</span>
                            @elseif($pago->metodo_pago === 'tarjeta')
                                <span class="text-primary"><i class="fas fa-credit-card mr-1"></i> Tarjeta</span>
                            @else
                                <span class="text-info"><i class="fas fa-exchange-alt mr-1"></i> Transferencia</span>
                            @endif
                        </td>
                        <td class="font-weight-bold text-success">{{ $gymConfig->simbolo_moneda }} {{ number_format($pago->monto, 2) }}</td>
                        <td class="text-muted"><i class="fas fa-user-tie mr-1"></i> {{ $pago->empleado->nombre ?? 'Sistema' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL 1: SELECCIÓN DE CLIENTE
============================================================ --}}
<div class="modal fade" id="modalClientes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1E293B;">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-users mr-2 text-primary"></i> Seleccionar Cliente para Cobro
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            {{-- Filtros --}}
            <div class="modal-body border-bottom py-3" style="background:#F8FAFC;">
                <div class="row align-items-center">
                    <div class="col-md-5 mb-2 mb-md-0">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                            <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar por nombre o cédula...">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <select id="filtroMembresia" class="form-control form-control-sm">
                            <option value="">Todos — Membresía</option>
                            <option value="ACTIVA">Membresía Activa</option>
                            <option value="VENCIDA">Membresía Vencida</option>
                            <option value="SIN MEMBRESÍA">Sin Membresía</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroEstadoCliente" class="form-control form-control-sm">
                            <option value="">Todos — Estado</option>
                            <option value="ACTIVO">Activos</option>
                            <option value="INACTIVO">Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabla de clientes --}}
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table id="modalClientesTable" class="table ic-table mb-0 w-100">
                        <thead>
                            <tr>
                                <th>CLIENTE</th>
                                <th>CÉDULA</th>
                                <th>TELÉFONO</th>
                                <th>MEMBRESÍA</th>
                                <th>ESTADO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientes as $c)
                            @php
                                $membActiva = $c->membresias->first();
                                if ($membActiva) {
                                    $mLabel = 'ACTIVA'; $mClass = 'badge-membresia-activa';
                                } else {
                                    $tieneVencida = $c->membresias()->where('estado', 'vencida')->exists();
                                    $mLabel = $tieneVencida ? 'VENCIDA' : 'SIN MEMBRESÍA';
                                    $mClass = $tieneVencida ? 'badge-membresia-vencida' : 'badge-membresia-sin';
                                }
                                $puedeActivar = $mLabel !== 'ACTIVA';
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($c->foto_referencia)
                                            <img src="{{ asset('storage/' . $c->foto_referencia) }}" class="rounded-circle mr-2" style="width:36px;height:36px;object-fit:cover;">
                                        @else
                                            <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center bg-primary text-white" style="width:36px;height:36px;font-size:.8rem;font-weight:700;">
                                                {{ strtoupper(substr($c->nombre, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold" style="font-size:.88rem;">{{ $c->nombre }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size:.85rem;">{{ $c->cedula }}</td>
                                <td class="text-muted" style="font-size:.85rem;">{{ $c->telefono ?? '—' }}</td>
                                <td><span class="{{ $mClass }}">{{ $mLabel }}</span></td>
                                <td>
                                    <span class="{{ $c->estado === 'activo' ? 'ic-status-active' : 'ic-status-inactive' }}">
                                        {{ strtoupper($c->estado) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('clientes.show', $c) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-info px-2 py-1 mr-1"
                                       title="Ver expediente">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pagos.create') }}?cliente_id={{ $c->id }}"
                                        class="btn btn-sm px-2 py-1 {{ $puedeActivar ? 'btn-primary' : 'btn-secondary' }}"
                                        title="{{ $puedeActivar ? 'Procesar cobro' : 'Ya tiene membresía activa' }}"
                                        {!! $puedeActivar ? '' : 'style="pointer-events: none;"' !!}>
                                        <i class="fas fa-cash-register mr-1"></i>
                                        {{ $puedeActivar ? 'Cobrar' : 'Activa' }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#pagosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 15
    });

    var tClientes = $('#modalClientesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        dom: 'rt<"d-flex justify-content-between align-items-center px-3 py-2"ip>',
        pageLength: 8,
        columnDefs: [{ orderable: false, targets: 5 }]
    });

    $('#buscarCliente').on('keyup', function() { tClientes.search(this.value).draw(); });
    $('#filtroMembresia').on('change', function() {
        tClientes.column(3).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
    });
    $('#filtroEstadoCliente').on('change', function() {
        tClientes.column(4).search(this.value ? '^' + this.value + '$' : '', true, false).draw();
    });
});
</script>
@endpush