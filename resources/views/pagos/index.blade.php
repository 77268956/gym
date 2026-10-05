@extends('layouts.app')

@section('title', 'Pagos y Cobros')

@section('skeleton')
    <div class="skel-box" style="height: 32px; width: 220px; margin-bottom: 1.5rem;"></div>
    <div class="skel-row">
        <div class="skel-box" style="height: 90px; flex: 1;"></div>
        <div class="skel-box" style="height: 90px; flex: 1;"></div>
        <div class="skel-box" style="height: 90px; flex: 1;"></div>
    </div>
    <div class="skel-box" style="height: 42px; width: 100%; margin-bottom: 0.5rem; border-radius: 8px 8px 0 0;"></div>
    <div class="skel-box" style="flex: 1; width: 100%; border-radius: 0 0 8px 8px;"></div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Diseño sin scroll en escritorio */
    body, html { overflow: hidden; height: 100%; }
    
    #page-wrapper main { 
        padding: 1rem 1.5rem !important; 
        display: flex; 
        flex-direction: column; 
        height: calc(100vh - 60px);
        overflow: hidden;
    }
    
    .page-header { flex-shrink: 0; margin-bottom: 0.75rem !important; }
    .main-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }

    :root {
        --ic-accent: var(--primary);
        --card-color: var(--sidebar-bg);
        --ic-green: #10B981;
        --ic-red: #EF4444;
        --ic-muted: #64748B;
    }
    
    /* KPI Cards */
    .kpi-card {
        border-radius: 10px;
        border: none;
        padding: 0.6rem 1rem;
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--sidebar-bg);
        height: 100%;
    }
    .kpi-icon { font-size: 1.8rem; opacity: 0.4; }
    .kpi-value { font-size: 1.4rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px;}
    .kpi-card .kpi-value,
    .kpi-card .kpi-label { color: #fff !important; }

    .ic-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.04);
        padding: 0.85rem;
        display: flex;
        flex-direction: column;
        margin-bottom: 0 !important;
    }
    .ic-card-title { font-weight: 700; font-size: 0.8rem; color: var(--sidebar-bg); margin-bottom: 0.5rem; text-transform: uppercase; }
    
    /* Panel scrollable */
    .table-panel { flex: 1 1 0; min-height: 0; min-width: 0; overflow: hidden; padding-right: 5px; display: flex; flex-direction: column; }
    
    /* Fix datatables height */
    .dataTables_wrapper { display: flex; flex: 1 1 0; min-height: 0; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dataTables_scroll { flex: 1 1 0; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scrollBody { flex: 1 1 auto; min-height: 180px; height: calc(100vh - 350px) !important; max-height: calc(100vh - 350px) !important; overflow-y: scroll !important; padding-bottom: 1rem; box-sizing: border-box; }
    #pagosTable_wrapper { flex: 1 1 auto; min-height: 0; }
    #pagosTable_wrapper .dataTables_scroll { min-height: 0; }
    #pagosTable_wrapper .dataTables_scrollHead table,
    #pagosTable_wrapper .dataTables_scrollBody table { width: 100% !important; }

    .ic-table thead th { font-size: 0.75rem; font-weight: 700; color: var(--primary); background: #eaecf4; border-bottom: 2px solid var(--primary); padding: 0.75rem 0.5rem; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; }
    #pagosTable_wrapper .pagination .page-item.active .page-link { background-color: var(--primary); border-color: var(--primary); color: #fff; }
    #pagosTable_wrapper .pagination .page-link { color: var(--primary); }
    #pagosTable_wrapper .pagination .page-item:not(.disabled):not(.active) .page-link:hover { background-color: var(--primary); border-color: var(--primary); color: #fff; }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #e3e6f0; padding: 0.6rem 0.5rem; color: #5a5c69; }
    
    .ic-avatar { width: 30px; height: 30px; background: var(--card-color); color: white; font-weight: bold; font-size:0.7rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .ic-card-icon { color: var(--card-color); }
    .ic-badge-active, .ic-badge-inactive, .ic-badge-warn, .ic-badge-critical { background: var(--card-color); color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    
    /* Badges para el estado de membresía en Select2 */
    .badge-membresia-activa { background-color: #10B981; color: #fff; }
    .badge-membresia-vencida { background-color: #EF4444; color: #fff; }
    .badge-membresia-sin { background-color: #64748B; color: #fff; }

    .client-result { display: flex; align-items: center; }
    .client-result img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 15px; }
    .client-result .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; font-size: 14px; }
    .client-result .info { display: flex; flex-direction: column; }
    .client-result .name { font-weight: bold; color: #1e293b; }
    .client-result .cedula { font-size: 0.85em; color: #64748b; }
    .client-result .badges { margin-top: 4px; }

    .row.tight { margin-bottom: 0.75rem; }
    .payments-toolbar { gap: .75rem; }
    .payments-toolbar-main,
    .payments-toolbar-actions { min-width: 0; }
    .payments-toolbar-main { flex: 1 1 420px; }
    .payments-toolbar-main .ic-card-title { flex: 0 1 auto; }
    .payments-toolbar-main .input-group { flex: 1 1 250px; min-width: 180px; }
    .payments-toolbar-actions { flex: 0 1 auto; }

    /* Modales y Ajustes Select2 para Modal */
    .payment-client-search-modal .modal-dialog { max-width: 560px; }
    .payment-client-search-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: visible;
        background: #f8fafc;
        box-shadow: 0 20px 55px rgba(15, 23, 42, .28);
    }
    .payment-modal-header {
        position: relative;
        padding: 1.6rem 1.75rem 1.45rem;
        background: linear-gradient(135deg, var(--sidebar-bg), #0f172a);
        color: #fff;
    }
    .payment-modal-header::after {
        content: "";
        position: absolute;
        width: 150px;
        height: 150px;
        right: -55px;
        top: -75px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
    }
    .payment-modal-kicker {
        margin-bottom: .35rem;
        color: var(--primary);
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }
    .payment-modal-title { margin: 0; font-size: 1.5rem; font-weight: 800; }
    .payment-modal-subtitle { margin: .35rem 0 0; color: #cbd5e1; font-size: .82rem; }
    .payment-modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 2;
        width: 32px;
        height: 32px;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 50%;
        background: rgba(0,0,0,.18);
        color: #fff;
        line-height: 1;
    }
    .payment-client-search-modal .modal-body {
        padding: 1.35rem;
        overflow: visible;
    }
    .payment-search-box {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(15,23,42,.05);
    }
    .payment-search-label {
        display: block;
        margin-bottom: .5rem;
        color: #475569;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    /* Correcciones críticas de estructura Select2 */
    .payment-client-search-modal .select2-container {
        width: 100% !important;
        z-index: 1060 !important;
    }
    .payment-client-search-modal .select2-container--default .select2-selection--single {
        height: 46px !important;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
    }
    .payment-client-search-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155;
        line-height: normal !important;
        padding-left: .85rem;
        padding-right: 2rem;
        width: 100%;
    }
    .payment-client-search-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
        right: 10px;
    }
    .payment-client-search-modal .select2-dropdown {
        z-index: 1060 !important;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 10px 25px rgba(15,23,42,.14);
    }
    .payment-client-search-modal .select2-container--open .select2-dropdown {
        top: 100% !important;
        bottom: auto !important;
    }
    .payment-client-search-modal .modal-body > .select2-container--open {
        top: 88px !important;
        left: 16px !important;
    }
    .payment-client-search-modal .modal-body > .select2-container--open .select2-dropdown {
        top: 0 !important;
        bottom: auto !important;
    }
    .payment-client-search-modal .select2-search--dropdown { padding: .5rem; background: #fff; }
    .payment-client-search-modal .select2-search--dropdown .select2-search__field {
        width: 100%;
        height: 38px;
        display: block !important;
        padding: .375rem .7rem;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        color: #334155;
    }
    .payment-client-result {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .45rem .6rem;
    }
    .payment-client-result .client-result { width: 100%; }
    #clienteSeleccionadoInfo { margin-top: 1rem !important; }
    .payment-selected-card {
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(15,23,42,.05);
    }
    .payment-selected-card .card-body { padding: 0 !important; }
    .payment-client-search-modal .modal-footer {
        padding: 1rem 1.35rem;
        border-top: 1px solid #e2e8f0;
        background: #fff;
    }

    @media (max-width: 991.98px) {
        body, html { overflow: auto; height: auto; }
        #page-wrapper main {
            height: auto;
            min-height: calc(100vh - 60px);
            overflow: visible;
            padding: .75rem !important;
        }
        .main-container { overflow: visible; min-height: auto; }
        .main-container > .row.flex-grow-1 { height: auto; flex: none !important; }
        .main-container > .row.flex-grow-1 > .col-12 { height: auto !important; min-height: 0; padding-bottom: .75rem !important; }
        .ic-card.h-100 { height: auto !important; min-height: 0; }
        .payments-toolbar {
            gap: .75rem;
            flex-wrap: wrap;
        }
        .payments-toolbar-main,
        .payments-toolbar-actions {
            width: 100%;
        }
        .payments-toolbar-main {
            flex-wrap: wrap;
        }
        .payments-toolbar-main .input-group {
            width: 100% !important;
            margin-top: .5rem;
        }
        .payments-toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-top: 0 !important;
        }
        #filterTipoCobro,
        #filterMetodoPago {
            flex: 1 1 140px;
            width: auto !important;
            margin-right: 0 !important;
        }
        .table-panel {
            flex: 0 0 470px;
            height: 470px;
            min-height: 470px;
            overflow-x: auto;
            overflow-y: hidden;
        }
        #pagosTable_wrapper {
            width: 900px;
            min-width: 900px;
        }
        #pagosTable_wrapper .dataTables_scrollBody {
            height: 360px !important;
            max-height: 360px !important;
        }
        #pagosTable_wrapper > .row:last-child {
            min-width: 0;
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }
        #pagosTable_wrapper .dataTables_length,
        #pagosTable_wrapper .dataTables_info,
        #pagosTable_wrapper .dataTables_paginate {
            width: 100%;
            text-align: center;
        }
        #pagosTable_wrapper .pagination {
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 0;
        }
    }

    @media (max-width: 575.98px) {
        .main-container > .row.tight > [class*="col-"] { margin-bottom: .75rem; }
        .main-container > .row.tight > [class*="col-"]:last-child { margin-bottom: 0; }
        .kpi-card { min-height: 64px; }
        .kpi-value { font-size: 1.2rem; }
        .kpi-label { font-size: .62rem; }
        .ic-card { padding: .65rem; }
        .ic-card-title { font-size: .72rem; }
        #pagosTable_wrapper { width: 900px; min-width: 900px; }
        .payments-toolbar-main .ic-card-title {
            width: 100%;
            margin-right: 0 !important;
            margin-bottom: .25rem !important;
            white-space: normal;
        }
        .payments-toolbar-actions {
            align-items: stretch !important;
        }
        #filterTipoCobro,
        #filterMetodoPago {
            flex: 1 1 100%;
            width: 100% !important;
            margin-right: 0 !important;
        }
        .payments-toolbar-actions > .btn {
            width: 100%;
            min-height: 38px;
        }
        .payment-client-search-modal .modal-dialog {
            width: auto;
            max-width: calc(100% - 1rem);
            margin: .5rem;
        }
        .payment-client-search-modal .modal-content {
            width: 100%;
            border-radius: 14px;
        }
        .payment-modal-header {
            padding: 1.25rem 1rem 1.1rem;
        }
        .payment-modal-title {
            padding-right: 2rem;
            font-size: 1.25rem;
        }
        .payment-modal-subtitle {
            max-width: calc(100% - 1rem);
            font-size: .78rem;
        }
        .payment-client-search-modal .modal-body { padding: 1rem; }
        .payment-client-search-modal .modal-body > .select2-container--open {
            top: 84px !important;
            left: 16px !important;
        }
        .payment-selected-card {
            padding: 1rem .75rem;
        }
        .payment-selected-card .d-flex {
            flex-wrap: wrap;
            gap: .35rem;
        }
        .payment-selected-card .d-flex .mr-2,
        .payment-selected-card .d-flex .ml-2 {
            margin-left: 0 !important;
            margin-right: .25rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid main-container">

    {{-- KPI Cards --}}
    <div class="row tight flex-shrink-0">
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value text-success">{{ $gymConfig->simbolo_moneda }} {{ number_format($pagosHoy, 2) }}</h3>
                    <div class="kpi-label">Ingresos de Hoy</div>
                </div>
                <i class="fas fa-hand-holding-usd kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value text-info">{{ $gymConfig->simbolo_moneda }} {{ number_format($pagosMes ?? 0, 2) }}</h3>
                    <div class="kpi-label">Ingresos del Mes</div>
                </div>
                <i class="fas fa-calendar-check kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $gymConfig->simbolo_moneda }} {{ number_format($totalIngresos, 2) }}</h3>
                    <div class="kpi-label">Ingresos Históricos</div>
                </div>
                <i class="fas fa-wallet kpi-icon"></i>
            </div>
        </div>
    </div>

    {{-- Tabla de Movimientos --}}
    <div class="row flex-grow-1" style="min-height: 0;">
        <div class="col-12 h-100 pb-1">
            <div class="ic-card h-100 d-flex flex-column">
                <div class="payments-toolbar d-flex justify-content-between align-items-center mb-2 flex-shrink-0 flex-wrap">
                    <div class="payments-toolbar-main d-flex align-items-center flex-wrap">
                        <h5 class="ic-card-title mb-0 mr-3"><i class="fas fa-receipt text-primary mr-2"></i> Historial y Auditoría de Cobros</h5>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" id="customSearch" class="form-control border-left-0" placeholder="Buscar cobro..." style="background-color: #F8FAFC;">
                        </div>
                    </div>
                    <div class="payments-toolbar-actions d-flex align-items-center">
                        <select id="filterTipoCobro" class="form-control form-control-sm mr-2" style="width: 130px;">
                            <option value="">Todos los cobros</option>
                            <option value="Membresía">Membresías</option>
                            <option value="Pase Diario">Pases diarios</option>
                            <option value="OTRO">Otros</option>
                        </select>
                        <select id="filterMetodoPago" class="form-control form-control-sm mr-2" style="width: 125px;">
                            <option value="">Todos los métodos</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3" data-toggle="modal" data-target="#modalClientes" title="Procesar nuevo cobro">
                            <i class="fas fa-cash-register mr-1"></i> Procesar Cobro
                        </button>
                    </div>
                </div>
        
                <div class="table-panel mt-2">
                    <table id="pagosTable" class="table ic-table w-100">
                        <thead>
                            <tr>
                                <th class="text-uppercase"><i class="fas fa-calendar-alt mr-1 text-primary"></i> Fecha / Hora</th>
                                <th class="text-uppercase"><i class="fas fa-user mr-1 text-primary"></i> Cliente</th>
                                <th class="text-uppercase"><i class="fas fa-receipt mr-1 text-primary"></i> Tipo de cobro</th>
                                <th class="text-uppercase"><i class="fas fa-credit-card mr-1 text-primary"></i> Método</th>
                                <th class="text-uppercase"><i class="fas fa-dollar-sign mr-1 text-primary"></i> Monto</th>
                                <th class="text-uppercase"><i class="fas fa-user-tie mr-1 text-primary"></i> Cajero</th>
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
                                    @if($pago->cliente)
                                        <div class="d-flex align-items-center">
                                            <div class="ic-avatar mr-2">
                                                {{ strtoupper(substr($pago->cliente->nombre, 0, 2)) }}
                                            </div>
                                            <div class="font-weight-bold text-dark">
                                                <a href="{{ route('clientes.show', $pago->cliente) }}" class="text-dark">
                                                {{ $pago->cliente->nombre }}
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted font-weight-bold">Cliente no disponible</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pago->tipo_pago === 'membresia')
                                        <span class="ic-badge-active"><i class="fas fa-id-card mr-1"></i> Membresía</span><br>
                                        <small class="text-muted">{{ $pago->membresia->tipoMembresia->nombre ?? 'N/A' }}</small>
                                    @elseif($pago->tipo_pago === 'pase_diario')
                                        <span class="ic-badge-warn"><i class="fas fa-ticket-alt mr-1"></i> Pase Diario</span>
                                    @else
                                        <span class="ic-badge-inactive">{{ strtoupper($pago->tipo_pago) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $icon = 'fas fa-money-bill';
                                        if($pago->metodo_pago == 'tarjeta') $icon = 'fas fa-credit-card';
                                        if($pago->metodo_pago == 'transferencia') $icon = 'fas fa-exchange-alt';
                                    @endphp
                                    <i class="{{ $icon }} text-muted mr-1"></i> {{ ucfirst($pago->metodo_pago) }}
                                </td>
                                <td class="font-weight-bold text-success">
                                    {{ $gymConfig->simbolo_moneda }} {{ number_format($pago->monto, 2) }}
                                </td>
                                <td class="text-muted" style="font-size: 0.8rem;">
                                    {{ $pago->empleado->nombre ?? 'Sistema' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL SELECCIÓN DE CLIENTE (BÚSQUEDA) --}}
<div class="modal fade payment-client-search-modal" id="modalClientes" tabindex="-1" role="dialog" aria-labelledby="modalClientesTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="payment-modal-header">
                <button type="button" class="payment-modal-close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
                <div class="payment-modal-kicker"><i class="fas fa-cash-register mr-1"></i> Gestión de cobros</div>
                <h5 class="payment-modal-title" id="modalClientesTitle">Nuevo Cobro</h5>
                <p class="payment-modal-subtitle">Busca un cliente para consultar su estado y generar el cobro.</p>
            </div>

            <div class="modal-body">
                <div class="payment-search-box">
                    <label for="select2Cliente" class="payment-search-label">
                        <i class="fas fa-search mr-1"></i> Buscar cliente
                    </label>
                    <select id="select2Cliente" style="width: 100%;">
                        <option></option>
                    </select>
                </div>
                
                <div id="clienteSeleccionadoInfo" class="d-none mt-4 animate__animated animate__fadeIn">
                    <div class="payment-selected-card">
                        <div class="card-body text-center">
                            
                            <div class="position-relative d-inline-block mb-3">
                                <div id="infoFoto"></div>
                            </div>

                            <div class="small text-uppercase text-muted font-weight-bold mb-1">Cliente seleccionado</div>
                            <h5 id="infoNombre" class="font-weight-bold mb-0 text-dark" style="font-size: 1.25rem;">Nombre</h5>
                            <p id="infoCedula" class="text-muted mb-3 small">Cédula</p>

                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <span class="text-muted small mr-2">Membresía:</span>
                                <span id="infoMembresia" class="badge badge-pill px-3 py-2 mr-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">MEMB</span>
                                <span class="text-muted small mr-2 ml-2">Cliente:</span>
                                <span id="infoEstado" class="badge badge-pill px-3 py-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">ESTADO</span>
                            </div>

                            <a href="#" id="btnCobrar" class="btn btn-primary font-weight-bold btn-block py-3" style="border-radius: 10px; font-size: 1.05rem;">
                                <i class="fas fa-arrow-right mr-2"></i> Procesar Cobro
                            </a>

                            <div id="alertaYaActiva" class="alert alert-warning d-none mt-3 mb-0 small text-left" style="border-radius: 8px;">
                                <i class="fas fa-exclamation-triangle mr-1"></i> El socio ya tiene una membresía vigente.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#pagosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        scrollY: window.innerWidth <= 991
            ? '360px'
            : Math.max(240, window.innerHeight - 350) + 'px',
        scrollCollapse: true,
        info: true,
        dom: "<'row table-data-row'<'col-sm-12'tr>>" +
             "<'row mt-2 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>"
    });

    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#filterTipoCobro').on('change', function() {
        table.column(2).search(this.value).draw();
    });

    $('#filterMetodoPago').on('change', function() {
        table.column(3).search(this.value).draw();
    });

    $(window).on('resize', function() {
        table.columns.adjust();
    });

    // Inicialización correcta de Select2 vinculada al Modal
    $('#select2Cliente').select2({
        dropdownParent: $('#modalClientes .modal-body'),
        placeholder: 'Escribe nombre o cédula...',
        allowClear: true,
        minimumInputLength: 1,
        width: '100%',
        ajax: {
            url: '{{ route("pagos.buscarClientes") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return { results: data.results };
            },
            cache: true
        },
        templateResult: formatClient,
        templateSelection: formatClientSelection
    });

    $('#select2Cliente').on('select2:open', function () {
        window.setTimeout(function () {
            $('.payment-client-search-modal .select2-search__field').trigger('focus');

            window.setTimeout(function () {
                $('.payment-client-search-modal .select2-search__field').trigger('focus');
            }, 250);
        }, 50);
    });
    
    function formatClient(client) {
        if (client.loading) return client.text;
        
        var imgHtml = '';
        if (client.foto) {
            imgHtml = '<img src="' + client.foto + '" />';
        } else {
            var initials = client.text ? client.text.substring(0,2).toUpperCase() : 'CL';
            imgHtml = '<div class="avatar-placeholder">' + initials + '</div>';
        }
        
        var mClass = 'badge-secondary';
        if (client.membresia_status === 'ACTIVA') mClass = 'badge-membresia-activa';
        else if (client.membresia_status === 'VENCIDA') mClass = 'badge-membresia-vencida';
        else mClass = 'badge-membresia-sin';
        
        return $(
            '<div class="client-result">' +
                imgHtml +
                '<div class="info">' +
                    '<span class="name">' + client.text + '</span>' +
                    '<span class="cedula">' + (client.cedula ? client.cedula : '') + '</span>' +
                    '<div class="badges">' +
                        '<span class="badge ' + mClass + ' mr-1">' + (client.membresia_status || 'SIN MEMBRESÍA') + '</span>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }
    
    function formatClientSelection(client) {
        return client.text || client.id;
    }
    
    $('#select2Cliente').on('select2:select', function (e) {
        var data = e.params.data;
        $('#clienteSeleccionadoInfo').removeClass('d-none');
        
        $('#infoNombre').text(data.text);
        $('#infoCedula').text(data.cedula || 'Sin Cédula');
        
        // Foto
        if (data.foto) {
            $('#infoFoto').html('<img src="' + data.foto + '" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">');
        } else {
            $('#infoFoto').html('<div class="rounded-circle mx-auto d-flex align-items-center justify-content-center bg-primary text-white font-weight-bold" style="width:80px;height:80px;font-size:1.5rem;">' + data.text.substring(0,2).toUpperCase() + '</div>');
        }
        
        // Membresia badge
        var mBadge = $('#infoMembresia');
        var status = data.membresia_status || 'SIN MEMBRESÍA';
        mBadge.text(status);
        mBadge.removeClass('badge-membresia-activa badge-membresia-vencida badge-membresia-sin');

        if (status === 'ACTIVA') {
            mBadge.addClass('badge-membresia-activa');
            $('#alertaYaActiva').removeClass('d-none');
            $('#btnCobrar').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            if (status === 'VENCIDA') mBadge.addClass('badge-membresia-vencida');
            else mBadge.addClass('badge-membresia-sin');
            $('#alertaYaActiva').addClass('d-none');
            $('#btnCobrar').removeClass('btn-secondary').addClass('btn-primary');
        }
        
        // Estado
        var estadoText = data.estado ? data.estado.toUpperCase() : 'INACTIVO';
        $('#infoEstado').text(estadoText);
        $('#infoEstado').removeClass().addClass('badge badge-pill px-3 py-2 ' + (data.estado === 'activo' ? 'ic-badge-active' : 'ic-badge-inactive text-white bg-secondary'));
        
        // Btn Link
        $('#btnCobrar').attr('href', '{{ route("pagos.create") }}?cliente_id=' + data.id);
    });
    
    // Clear on open
    $('#modalClientes').on('show.bs.modal', function () {
        $('#select2Cliente').val(null).trigger('change');
        $('#clienteSeleccionadoInfo').addClass('d-none');
    });
});
</script>
@endpush