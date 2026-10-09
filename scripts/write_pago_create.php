<?php
$content = <<<'EOD'
@extends('layouts.app')

@section('title', 'Procesar Cobro')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single { height: 38px; border: 1px solid #ced4da; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; color: #495057; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    .metodo-card { border: 2px solid #e2e8f0; border-radius: 8px; padding: 1rem; text-align: center; cursor: pointer; transition: all 0.2s; }
    .metodo-card:hover { border-color: #cbd5e1; background: #f8fafc; }
    .metodo-card.selected { border-color: #2563EB; background: #EFF6FF; }
    .metodo-card.selected i { color: #2563EB; }
    .metodo-card i { font-size: 2rem; color: #94a3b8; margin-bottom: 0.5rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('pagos.index') }}"><i class="fas fa-wallet mr-1"></i> Pagos</a></li>
                    <li class="breadcrumb-item active">Nuevo Cobro</li>
                </ol>
            </nav>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Por favor verifica los datos:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 font-weight-bold text-primary">
                        <i class="fas fa-cash-register mr-2"></i> Procesar Nuevo Cobro
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('pagos.store') }}" method="POST" id="pagoForm">
                        @csrf
                        
                        <input type="hidden" name="metodo_pago" id="metodo_pago" value="{{ old('metodo_pago', 'efectivo') }}">
                        
                        <h6 class="text-uppercase text-muted font-weight-bold mb-3 border-bottom pb-2" style="font-size:.75rem;">1. Seleccionar Cliente</h6>
                        <div class="form-group mb-4">
                            <select name="cliente_id" id="cliente_id" class="form-control select2" required>
                                <option value="">Buscar cliente por nombre o cédula...</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}" {{ (old('cliente_id') ?? $clienteSeleccionado) == $c->id ? 'selected' : '' }}>
                                        {{ $c->nombre }} - {{ $c->cedula }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="clienteInfoPanel" class="mt-3 d-none">
                                <div id="clienteInfoContenido"></div>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="font-size:.75rem;">2. Concepto de Pago</h6>
                        <div class="form-row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="custom-control custom-radio custom-control-inline w-100 p-3 border rounded h-100 text-center" style="cursor:pointer;" onclick="document.getElementById('tipo_membresia').click()">
                                    <input type="radio" id="tipo_membresia" name="tipo_pago" value="membresia" class="custom-control-input" {{ old('tipo_pago', 'membresia') == 'membresia' ? 'checked' : '' }} onchange="toggleTiposPago()">
                                    <label class="custom-control-label font-weight-bold w-100" for="tipo_membresia">Renovación / Nueva Membresía</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-control custom-radio custom-control-inline w-100 p-3 border rounded h-100 text-center" style="cursor:pointer;" onclick="document.getElementById('tipo_pase').click()">
                                    <input type="radio" id="tipo_pase" name="tipo_pago" value="pase_diario" class="custom-control-input" {{ old('tipo_pago') == 'pase_diario' ? 'checked' : '' }} onchange="toggleTiposPago()">
                                    <label class="custom-control-label font-weight-bold w-100" for="tipo_pase">Pase Diario</label>
                                </div>
                            </div>
                        </div>

                        <div id="selector_membresia" class="form-group mb-4 p-3 bg-light rounded" style="display: {{ old('tipo_pago', 'membresia') == 'membresia' ? 'block' : 'none' }};">
                            <label class="font-weight-bold small text-dark mb-2">Plan de Membresía</label>
                            <input type="hidden" name="tipo_membresia_id" id="tipo_membresia_id" value="{{ old('tipo_membresia_id') }}">
                            
                            <div id="pago_plan_seleccionado_box" class="{{ old('tipo_membresia_id') ? '' : 'd-none' }} mb-3 p-3 border rounded" style="background:#fff;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="font-weight-bold text-primary" id="pago_plan_nombre">—</span>
                                        <small class="text-muted ml-2" id="pago_plan_duracion"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="$('#modalPlanesPago').modal('show')">
                                        <i class="fas fa-exchange-alt mr-1"></i> Cambiar
                                    </button>
                                </div>
                            </div>
                            
                            <button type="button" id="btn_abrir_planes" class="btn btn-outline-primary btn-block py-2 {{ old('tipo_membresia_id') ? 'd-none' : '' }}" onclick="$('#modalPlanesPago').modal('show')">
                                <i class="fas fa-list mr-1"></i> Seleccionar Plan de Membresía
                            </button>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold small text-dark">Monto a Cobrar</label>
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend"><span class="input-group-text font-weight-bold text-success">{{ $gymConfig->simbolo_moneda }}</span></div>
                                <input type="number" step="0.01" min="1" name="monto" id="monto" class="form-control font-weight-bold" style="font-size: 1.5rem;" value="{{ old('monto', '') }}" required>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="font-size:.75rem;">3. Método de Pago</h6>
                        <div class="row mb-4">
                            <div class="col-4">
                                <div class="metodo-card {{ old('metodo_pago', 'efectivo') == 'efectivo' ? 'selected' : '' }}" data-method="efectivo" onclick="selectMetodo(this)">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <div class="font-weight-bold small">Efectivo</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="metodo-card {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}" data-method="tarjeta" onclick="selectMetodo(this)">
                                    <i class="fas fa-credit-card"></i>
                                    <div class="font-weight-bold small">Tarjeta</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="metodo-card {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}" data-method="transferencia" onclick="selectMetodo(this)">
                                    <i class="fas fa-exchange-alt"></i>
                                    <div class="font-weight-bold small">Transferencia</div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('pagos.index') }}" class="btn btn-outline-secondary px-4 py-2 mr-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4 py-2" id="btnProcesar">
                                <i class="fas fa-check-circle mr-1"></i> Procesar y Guardar Cobro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPlanesPago" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1E293B;">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-id-card mr-2 text-primary"></i> Seleccionar Plan de Membresía
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4" style="background:#F1F5F9;">
                <div class="row">
                    @foreach($tiposMembresia as $tipo)
                    <div class="col-md-6 mb-4">
                        <div class="membresia-card h-100" data-id="{{ $tipo->id }}" data-nombre="{{ $tipo->nombre }}" data-precio="{{ $tipo->precio }}" data-duracion="{{ $tipo->duracion_dias }} días" onclick="pagoSeleccionarMembresia(this)" style="cursor:pointer;background:#fff;border-radius:12px;padding:1.5rem;border:2px solid #E2E8F0;transition:all .2s;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="font-weight-bold mb-1" style="color:#1E293B;">{{ $tipo->nombre }}</h5>
                                    <span class="badge badge-pill" style="background:#EFF6FF;color:#2563EB;font-size:.75rem;">{{ $tipo->duracion_dias }} días</span>
                                </div>
                                <div class="text-right">
                                    <div class="h4 font-weight-bold mb-0" style="color:#2563EB;">{{ $gymConfig->simbolo_moneda }} {{ number_format($tipo->precio, 2) }}</div>
                                </div>
                            </div>
                            @if($tipo->descripcion)
                                <p class="text-muted small mb-0">{{ $tipo->descripcion }}</p>
                            @endif
                            <div class="mt-3 text-right check-icon d-none">
                                <span class="badge badge-success px-3 py-2"><i class="fas fa-check mr-1"></i> Seleccionado</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary font-weight-bold px-4" id="btnConfirmarPlanPago" disabled onclick="pagoConfirmarMembresia()">
                    <i class="fas fa-check mr-1"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Buscar cliente...",
            width: '100%'
        });

        if ($('#cliente_id').val()) {
            cargarInfoCliente($('#cliente_id').val());
        }

        $('#cliente_id').on('change', function() {
            cargarInfoCliente($(this).val());
        });

        @if(old('tipo_membresia_id'))
            @foreach($tiposMembresia as $tipo)
                @if(old('tipo_membresia_id') == $tipo->id)
                    document.getElementById('pago_plan_nombre').textContent = '{{ $tipo->nombre }}';
                    document.getElementById('pago_plan_duracion').textContent = '{{ $tipo->duracion_dias }} días';
                @endif
            @endforeach
        @endif
    });

    function cargarInfoCliente(clienteId) {
        if (!clienteId) {
            $('#clienteInfoPanel').addClass('d-none');
            return;
        }

        $.getJSON('/pagos/cliente/' + clienteId + '/info', function(data) {
            var html = '';
            if (!data.membresia) {
                html = '<div class="alert alert-warning py-2 mb-0"><i class="fas fa-exclamation-circle mr-2"></i><strong>Sin membresía registrada.</strong> Este cliente no tiene historial de membresías.</div>';
            } else if (data.membresia.vencida) {
                html = '<div class="alert alert-danger py-2 mb-0"><i class="fas fa-times-circle mr-2"></i>' +
                    '<strong>Membresía VENCIDA</strong> — Plan: ' + data.membresia.plan +
                    ' · Venció el: ' + data.membresia.fecha_vencimiento +
                    '. <span class="font-weight-bold">Renovar para reactivar al cliente.</span></div>';
            } else {
                var color = data.membresia.dias_restantes <= 5 ? 'warning' : 'success';
                html = '<div class="alert alert-' + color + ' py-2 mb-0"><i class="fas fa-check-circle mr-2"></i>' +
                    '<strong>Membresía ACTIVA</strong> — Plan: ' + data.membresia.plan +
                    ' · Vence: ' + data.membresia.fecha_vencimiento +
                    ' (<strong>' + data.membresia.dias_restantes + ' días restantes</strong>)</div>';
            }
            $('#clienteInfoContenido').html(html);
            $('#clienteInfoPanel').removeClass('d-none');
        });
    }

    function toggleTiposPago() {
        var esMembresia = document.getElementById('tipo_membresia').checked;
        if(esMembresia) {
            document.getElementById('selector_membresia').style.display = 'block';
            document.getElementById('tipo_membresia_id').setAttribute('required', 'required');
            if(!document.getElementById('tipo_membresia_id').value) {
                document.getElementById('monto').value = '';
            }
        } else {
            document.getElementById('selector_membresia').style.display = 'none';
            document.getElementById('tipo_membresia_id').removeAttribute('required');
            document.getElementById('monto').value = '100.00'; 
        }
    }

    var pagoSelectedPlanId = null;
    var pagoSelectedPlanCard = null;

    function pagoSeleccionarMembresia(el) {
        document.querySelectorAll('#modalPlanesPago .membresia-card').forEach(function(c) {
            c.style.borderColor = '#E2E8F0';
            c.style.background = '#fff';
            c.querySelector('.check-icon').classList.add('d-none');
        });
        
        el.style.borderColor = '#2563EB';
        el.style.background = '#EFF6FF';
        el.querySelector('.check-icon').classList.remove('d-none');
        
        pagoSelectedPlanId = el.dataset.id;
        pagoSelectedPlanCard = el;
        document.getElementById('btnConfirmarPlanPago').disabled = false;
    }

    function pagoConfirmarMembresia() {
        if (!pagoSelectedPlanId) return;
        
        var el = pagoSelectedPlanCard;
        document.getElementById('tipo_membresia_id').value = pagoSelectedPlanId;
        document.getElementById('pago_plan_nombre').textContent = el.dataset.nombre;
        document.getElementById('pago_plan_duracion').textContent = el.dataset.duracion;
        document.getElementById('pago_plan_seleccionado_box').classList.remove('d-none');
        document.getElementById('btn_abrir_planes').classList.add('d-none');
        
        document.getElementById('monto').value = parseFloat(el.dataset.precio).toFixed(2);
        
        $('#modalPlanesPago').modal('hide');
    }

    function selectMetodo(element) {
        document.querySelectorAll('.metodo-card').forEach(function(el) {
            el.classList.remove('selected');
        });
        element.classList.add('selected');
        var method = element.getAttribute('data-method');
        document.getElementById('metodo_pago').value = method;
    }

    $('#pagoForm').on('submit', function() {
        $('#btnProcesar').html('<i class="fas fa-spinner fa-spin mr-1"></i> Procesando...').attr('disabled', true);
    });
</script>
@endpush
EOD;
file_put_contents('c:/laragon/www/GymX/resources/views/pagos/create.blade.php', $content);
echo "pagos/create done.\n";