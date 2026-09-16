<?php
$f = 'c:/laragon/www/GymX/resources/views/pagos/index.blade.php';
$c = file_get_contents($f);

// We need to add Select2 CSS/JS and modify the modal
$headStyles = <<<'EOD'
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
EOD;

$c = preg_replace('/@push\(\'styles\'\).*?@endpush/is', $headStyles, $c);


$modalContent = <<<'EOD'
{{-- ============================================================
     MODAL 1: SELECCIÓN DE CLIENTE (SELECT2)
============================================================ --}}
<div class="modal fade" id="modalClientes" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1E293B;">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-users mr-2 text-primary"></i> Procesar Cobro
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="form-group mb-4">
                    <label class="font-weight-bold text-dark"><i class="fas fa-search mr-1"></i> Buscar Cliente</label>
                    <select id="select2Cliente" class="form-control" style="width: 100%;"></select>
                </div>
                
                <div id="clienteSeleccionadoInfo" class="d-none">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div id="infoFoto" class="mb-3"></div>
                            <h5 id="infoNombre" class="font-weight-bold mb-1">Nombre</h5>
                            <p id="infoCedula" class="text-muted mb-2 small">Cédula</p>
                            
                            <div class="mb-3">
                                <span id="infoMembresia" class="badge">MEMBRESÍA</span>
                                <span id="infoEstado" class="badge">ESTADO</span>
                            </div>
                            
                            <div class="mt-4">
                                <a href="#" id="btnCobrar" class="btn btn-primary font-weight-bold btn-block">
                                    <i class="fas fa-cash-register mr-1"></i> Continuar con el Cobro
                                </a>
                                <div id="alertaYaActiva" class="alert alert-info d-none mt-3 small text-left">
                                    <i class="fas fa-info-circle mr-1"></i> Este cliente ya tiene una membresía activa. Aún puedes procesar otro tipo de pago o forzar renovación.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
EOD;

$c = preg_replace('/\{\{-- ============================================================.*?MODAL 1: SELECCIÓN DE CLIENTE.*?\<\/div\>\n\s*\<\/div\>\n\s*\<\/div\>/is', $modalContent, $c);


$scriptsContent = <<<'EOD'
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
        $('#infoEstado').className = 'badge ' + (data.estado === 'activo' ? 'ic-status-active' : 'ic-status-inactive') + ' ml-2';
        
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
EOD;

$c = preg_replace('/@push\(\'scripts\'\).*?@endpush/is', $scriptsContent, $c);

file_put_contents($f, $c);
echo "View updated with Select2.\n";