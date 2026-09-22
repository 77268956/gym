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
    
    /* KPI Cards */
    .kpi-card {
        border-radius: 8px;
        border: none;
        padding: 0.5rem 1rem; /* Más delgadas */
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--sidebar-bg);
        height: 100%;
    }
    .kpi-icon { font-size: 1.6rem; opacity: 0.4; } /* Icono más pequeño */
    .kpi-value { font-size: 1.3rem; font-weight: 800; margin: 0; line-height: 1; } /* Texto más pequeño */
    .kpi-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px;}

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
    
    /* Panel scrollable para la tabla */
    .table-panel { flex: 1; min-height: 0; overflow: hidden; padding-right: 5px; }
    
    /* Fix datatables height */
    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dataTables_scroll { flex: 1 1 auto; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scroll > .col-sm-12 { flex: 1 1 auto; min-height: 0; display: flex; flex-direction: column; }
    .dataTables_scrollBody { flex: 1 1 auto; min-height: 0; height: auto !important; max-height: none !important; overflow-y: auto !important; }
    
    .ic-table thead th { font-size: 0.7rem; color: #64748B; background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 0.5rem; white-space: nowrap; }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #F1F5F9; padding: 0.5rem; }
    
    .badge-membresia-activa  { background:#D1FAE5; color:#059669; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-vencida { background:#FEE2E2; color:#EF4444; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-sin     { background:#F1F5F9; color:#475569; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    
    /* Select2 custom styling for Bootstrap 4 */
    .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); border: 1px solid #ced4da; border-radius: .25rem; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + .75rem); }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: calc(1.5em + .75rem); }
    
    .client-result { display: flex; align-items: center; }
    .client-result img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 15px; }
    .client-result .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; font-size: 14px; }
    .client-result .info { display: flex; flex-direction: column; }
    .client-result .name { font-weight: bold; color: #1e293b; }
    .client-result .cedula { font-size: 0.85em; color: #64748b; }
    .client-result .badges { margin-top: 4px; }

    .badge-cobro { padding: .3rem .6rem; border-radius: 50px; font-size: .72rem; font-weight: 600; color: #fff; }
    .badge-cobro-membresia { background: var(--primary); }
    .badge-cobro-pase { background: var(--primary-hover); }
    .badge-cobro-otro { background: var(--sidebar-hover); }

    .row.tight { margin-bottom: 0.75rem; }
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
                <div class="d-flex justify-content-between align-items-center mb-1 flex-shrink-0">
                    <h5 class="ic-card-title mb-0"><i class="fas fa-receipt text-primary mr-2"></i> Historial y Auditoría de Cobros</h5>
                    <button type="button" class="btn btn-sm btn-primary py-1 px-2 font-weight-bold" data-toggle="modal" data-target="#modalClientes" style="font-size:0.75rem;">
                        <i class="fas fa-cash-register mr-1"></i> Procesar Nuevo Cobro
                    </button>
                </div>
        
                <div class="table-panel mt-2">
                    <table id="pagosTable" class="table ic-table w-100">
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
                                    @if($pago->cliente)
                                        <a href="{{ route('clientes.show', $pago->cliente) }}" class="font-weight-bold text-dark">
                                            {{ $pago->cliente->nombre }}
                                        </a>
                                    @else
                                        <span class="text-muted font-weight-bold">Cliente no disponible</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pago->tipo_pago === 'membresia')
                                        <span class="badge-cobro badge-cobro-membresia"><i class="fas fa-id-card mr-1"></i> Membresía</span><br>
                                        <small class="text-muted">{{ $pago->membresia->tipoMembresia->nombre ?? 'N/A' }}</small>
                                    @elseif($pago->tipo_pago === 'pase_diario')
                                        <span class="badge-cobro badge-cobro-pase"><i class="fas fa-ticket-alt mr-1"></i> Pase Diario</span>
                                    @else
                                        <span class="badge-cobro badge-cobro-otro">{{ strtoupper($pago->tipo_pago) }}</span>
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

{{-- ============================================================
     MODAL SELECCIÓN DE CLIENTE (BÚSQUEDA)
============================================================ --}}
<div class="modal fade" id="modalClientes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1E293B, #0F172A); padding: 1.5rem 1.5rem 1rem;">
                <h5 class="modal-title font-weight-bold text-white w-100 text-center">
                    <i class="fas fa-search-dollar mb-2 d-block text-primary" style="font-size: 2rem;"></i>
                    Nuevo Cobro
                </h5>
                <button type="button" class="close text-white position-absolute" style="top: 15px; right: 20px; opacity: 0.8;" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <div class="form-group mb-0">
                    <label class="font-weight-bold text-muted small text-uppercase mb-2">Selecciona un socio</label>
                    <select id="select2Cliente" class="form-control form-control-lg" style="width: 100%;"></select>
                </div>
                
                <div id="clienteSeleccionadoInfo" class="d-none mt-4 animate__animated animate__fadeIn">
                    <div class="card bg-light border-0" style="border-radius: 12px;">
                        <div class="card-body p-4 text-center">
                            
                            <div class="position-relative d-inline-block mb-3">
                                <div id="infoFoto"></div>
                            </div>

                            <h5 id="infoNombre" class="font-weight-bold mb-0 text-dark" style="font-size: 1.25rem;">Nombre</h5>
                            <p id="infoCedula" class="text-muted mb-3 small">Cédula</p>

                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <span class="text-muted small mr-2">Membresía:</span>
                                <span id="infoMembresia" class="badge badge-pill px-3 py-2 mr-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">MEMB</span>
                                <span class="text-muted small mr-2 ml-2">Cliente:</span>
                                <span id="infoEstado" class="badge badge-pill px-3 py-2" style="font-size: 0.8rem; letter-spacing: 0.5px;">ESTADO</span>
                            </div>

                            <a href="#" id="btnCobrar" class="btn btn-primary font-weight-bold btn-block py-3" style="border-radius: 10px; font-size: 1.05rem; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);">
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
    $('#pagosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        scrollY: '100%',
        scrollCollapse: true,
        info: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row dataTables_scroll'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });

    $('#select2Cliente').select2({
        dropdownParent: $('#modalClientes'),
        placeholder: 'Escribe nombre o cédula...',
        allowClear: true,
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
    
    function formatClient(client) {
        if (client.loading) return client.text;
        
        var imgHtml = '';
        if (client.foto) {
            imgHtml = '<img src="' + client.foto + '" />';
        } else {
            var initials = client.text.substring(0,2).toUpperCase();
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
                        '<span class="badge ' + mClass + ' mr-1">' + client.membresia_status + '</span>' +
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
        mBadge.text(data.membresia_status);
        mBadge.removeClass('badge-membresia-activa badge-membresia-vencida badge-membresia-sin');
        if (data.membresia_status === 'ACTIVA') {
            mBadge.addClass('badge-membresia-activa');
            $('#alertaYaActiva').removeClass('d-none');
            $('#btnCobrar').removeClass('btn-primary').addClass('btn-secondary');
        } else {
            if (data.membresia_status === 'VENCIDA') mBadge.addClass('badge-membresia-vencida');
            else mBadge.addClass('badge-membresia-sin');
            $('#alertaYaActiva').addClass('d-none');
            $('#btnCobrar').removeClass('btn-secondary').addClass('btn-primary');
        }
        
        // Estado
        $('#infoEstado').text(data.estado.toUpperCase());
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