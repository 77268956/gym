@extends('layouts.app')

@section('title', 'Pagos')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .badge-membresia-activa  { background:#D1FAE5; color:#059669; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-vencida { background:#FEE2E2; color:#EF4444; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-sin     { background:#F1F5F9; color:#475569; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    
    /* Select2 custom styling for Bootstrap 4 */
    .select2-container .select2-selection--single {
        height: calc(1.5em + .75rem + 2px);
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(1.5em + .75rem);
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + .75rem);
    }
    
    .client-result { display: flex; align-items: center; }
    .client-result img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 15px; }
    .client-result .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background: #2563EB; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; font-size: 14px; }
    .client-result .info { display: flex; flex-direction: column; }
    .client-result .name { font-weight: bold; color: #1e293b; }
    .client-result .cedula { font-size: 0.85em; color: #64748b; }
    .client-result .badges { margin-top: 4px; }
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
                                <i class="fas fa-exclamation-triangle mr-1"></i> El socio tiene una membresía vigente.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#pagosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 15
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
        $('#infoEstado').removeClass().addClass('badge badge-pill px-3 py-2 ' + (data.estado === 'activo' ? 'ic-status-active' : 'ic-status-inactive'));
        
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