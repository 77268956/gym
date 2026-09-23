@extends('layouts.app')

@section('title', 'Tipos de Membresía')

@section('skeleton')
    <div class="skel-box" style="height: 32px; width: 250px; margin-bottom: 1.5rem;"></div>
    <div class="skel-row">
        <div class="skel-box" style="height: 200px; flex: 1;"></div>
        <div class="skel-box" style="height: 200px; flex: 1;"></div>
        <div class="skel-box" style="height: 200px; flex: 1;"></div>
    </div>
    <div class="skel-row">
        <div class="skel-box" style="height: 200px; flex: 1;"></div>
        <div class="skel-box" style="height: 200px; flex: 1;"></div>
        <div class="skel-box" style="height: 200px; flex: 1;"></div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    body, html { overflow: hidden; height: 100%; }
    #page-wrapper main { 
        padding: 1rem 1.5rem !important; 
        display: flex; flex-direction: column; 
        height: calc(100vh - 60px); overflow: hidden;
    }
    .page-header { flex-shrink: 0; margin-bottom: 0.75rem !important; }
    .main-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }

    :root { --card-color: var(--sidebar-bg); }

    .kpi-card {
        border-radius: 8px; border: none; padding: 0.5rem 1rem;
        color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex; justify-content: space-between; align-items: center;
        background: var(--card-color); height: 100%;
    }
    .kpi-icon { font-size: 1.6rem; opacity: 0.4; }
    .kpi-value { font-size: 1.3rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px;}

    .ic-card {
        background: #fff; border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04); padding: 0.85rem;
        display: flex; flex-direction: column; margin-bottom: 0 !important;
    }
    .ic-card-title { font-weight: 700; font-size: 0.8rem; color: var(--sidebar-bg); text-transform: uppercase; }

    .table-panel { flex: 1; min-height: 0; overflow-y: auto; padding-right: 5px; }
    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dataTables_scroll { flex-grow: 1; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .dataTables_scrollBody { flex-grow: 1; min-height: 0; overflow-y: auto !important; max-height: none !important; height: auto !important; }

    .ic-table thead th { font-size: 0.7rem; color: #64748B; background: #F8FAFC; border-bottom: 2px solid #E2E8F0; padding: 0.5rem; white-space: nowrap; }
    .ic-table td { font-size: 0.85rem; vertical-align: middle; white-space: nowrap; border-top: 1px solid #F1F5F9; padding: 0.5rem; }

    .ic-badge-active, .ic-badge-inactive, .ic-status-badge { background: var(--card-color); color: white; padding: 3px 8px; border-radius: 50px; font-weight: 600; font-size: 0.7rem; }
    .ic-card-icon { color: var(--card-color); }
    .ic-status-badge .ic-card-icon { color: white; }

    .ic-list-item { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9; }
    .ic-list-item:last-child { border-bottom: none; }
    .ic-list-title { font-size: 0.8rem; font-weight: 600; color: var(--sidebar-bg); }
    .ic-list-sub { font-size: 0.7rem; color: #64748B; }

    .row.tight { margin-bottom: 0.75rem; }

    .ic-color-btn { color: var(--card-color) !important; border-color: var(--card-color) !important; }
    .ic-color-btn:hover, .ic-color-btn.active { background-color: var(--card-color) !important; color: #fff !important; border-color: var(--card-color) !important; }
    
    /* Plan Pricing Card */
    .plan-card { border: 1px solid #E2E8F0; border-radius: .75rem; transition: transform .2s; background: #fff; height: 100%; display: flex; flex-direction: column; }
    .plan-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.06); border-color: var(--primary); }
    .plan-card-header { padding: 1rem; border-bottom: 1px solid #f1f1ef; }
    .plan-price { font-size: 1.6rem; font-weight: 800; color: var(--card-color); }
    .plan-card-body { padding: 1rem; flex: 1; }
    .plan-card-footer { padding: 0.75rem 1rem; background: #fafaf8; border-top: 1px solid #f1f1ef; border-radius: 0 0 .75rem .75rem; }
    .plan-card h6, .plan-card .plan-card-body, .plan-card .plan-card-body p, .plan-card .plan-card-body li, .plan-card .plan-card-body small { color: var(--card-color) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid main-container">

    {{-- KPI Cards --}}
    <div class="row tight flex-shrink-0">
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $totalPlanes }}</h3>
                    <div class="kpi-label">Total Planes</div>
                </div>
                <i class="fas fa-layer-group kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value text-success">{{ $planesActivos }}</h3>
                    <div class="kpi-label">Planes Activos</div>
                </div>
                <i class="fas fa-check-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $planesInactivos }}</h3>
                    <div class="kpi-label">Inactivos</div>
                </div>
                <i class="fas fa-ban kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">${{ number_format($precioPromedio, 2) }}</h3>
                    <div class="kpi-label">Precio Promedio</div>
                </div>
                <i class="fas fa-tag kpi-icon"></i>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row flex-grow-1" style="min-height: 0;">
        <div class="col-12 h-100 pb-1">
            <div class="ic-card h-100 d-flex flex-column">
                
                {{-- Toolbar --}}
                <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0 flex-wrap">
                    <h5 class="ic-card-title mb-0">
                        <i class="fas fa-id-card text-primary mr-2"></i> Tipos de Membresías y Planes
                    </h5>
                    <div class="d-flex align-items-center mt-2 mt-md-0">
                        <div class="btn-group btn-group-sm mr-2" role="group">
                            <button type="button" class="btn btn-outline-primary ic-color-btn custom-tab-btn active" id="btnViewTable" onclick="switchView('table')">
                                <i class="fas fa-list mr-1"></i> Tabla
                            </button>
                            <button type="button" class="btn btn-outline-primary ic-color-btn custom-tab-btn" id="btnViewCards" onclick="switchView('cards')">
                                <i class="fas fa-th-large mr-1"></i> Tarjetas
                            </button>
                        </div>
                        @if(Auth::user() && Auth::user()->rol === 'admin')
                            <button type="button" class="btn btn-primary btn-sm py-1 px-2 font-weight-bold" data-toggle="modal" data-target="#modalCrearPlan" style="font-size:0.75rem;">
                                <i class="fas fa-plus mr-1"></i> Nuevo Plan
                            </button>
                        @endif
                    </div>
                </div>

                {{-- VISTA TABLA --}}
                <div id="viewTable" class="table-panel">
                    <table id="mainPlanesTable" class="table ic-table w-100">
                        <thead>
                            <tr>
                                <th>NOMBRE DEL PLAN</th>
                                <th>DURACIÓN</th>
                                <th>PRECIO</th>
                                <th>BENEFICIOS</th>
                                <th>ESTADO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($planes as $plan)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-2 text-white font-weight-bold" style="width:30px;height:30px;font-size:0.7rem;background:var(--card-color);">
                                            <i class="fas fa-dumbbell"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark">{{ $plan->nombre }}</span>
                                            <small class="d-block text-muted">ID: #{{ $plan->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="ic-status-badge px-2 py-1" style="font-size:.78rem;">
                                        <i class="far fa-clock ic-card-icon mr-1"></i> {{ $plan->duracion_dias }} {{ $plan->duracion_dias == 1 ? 'Día' : 'Días' }}
                                    </span>
                                </td>
                                <td class="font-weight-bold text-success">${{ number_format($plan->precio, 2) }}</td>
                                <td>
                                    <span class="text-muted d-inline-block text-truncate" style="max-width: 250px;" title="{{ $plan->descripcion ?? 'Sin descripción' }}">
                                        {{ $plan->descripcion ?? 'Servicios estándar' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="{{ $plan->estado === 'activo' ? 'ic-badge-active' : 'ic-badge-inactive' }}">
                                        {{ strtoupper($plan->estado) }}
                                    </span>
                                </td>
                                <td class="text-center py-1">
                                    @if(Auth::user() && Auth::user()->rol === 'admin')
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light py-0 px-2" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                            <div class="dropdown-menu dropdown-menu-right" style="font-size:0.8rem;">
                                                <button class="dropdown-item py-1" onclick="openEditModal({{ json_encode($plan) }})">
                                                    <i class="fas fa-pen text-primary mr-2"></i> Editar
                                                </button>
                                                <form action="{{ route('membresias.toggleStatus', $plan) }}" method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="dropdown-item py-1 text-warning">
                                                        <i class="fas fa-exchange-alt text-warning mr-2"></i> Estado
                                                    </button>
                                                </form>
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('membresias.destroy', $plan) }}" method="POST" class="d-inline form-delete">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-1 text-danger">
                                                        <i class="fas fa-trash text-danger mr-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No hay planes de membresía configurados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VISTA TARJETAS --}}
                <div id="viewCards" class="d-none" style="overflow-y:auto; flex:1; min-height:0;">
                    <div class="row">
                        @forelse($planes as $plan)
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="plan-card">
                                <div class="plan-card-header d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="ic-status-badge px-2 py-1 mb-2">
                                            {{ strtoupper($plan->estado) }}
                                        </span>
                                        <h6 class="font-weight-bold text-dark mb-0">{{ $plan->nombre }}</h6>
                                    </div>
                                    <div class="text-right">
                                        <span class="plan-price">${{ number_format($plan->precio, 2) }}</span>
                                        <small class="d-block text-muted">/ {{ $plan->duracion_dias }} Días</small>
                                    </div>
                                </div>
                                <div class="plan-card-body">
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-info-circle mr-1 text-primary"></i> 
                                        {{ $plan->descripcion ?? 'Acceso a instalaciones y equipamiento.' }}
                                    </p>
                                    <ul class="list-unstyled small text-muted mb-0">
                                        <li class="mb-1"><i class="fas fa-check text-success mr-1"></i> {{ round($plan->duracion_dias / 30, 1) }} mes(es)</li>
                                        <li><i class="fas fa-check text-success mr-1"></i> Puntos EcoGim</li>
                                    </ul>
                                </div>
                                <div class="plan-card-footer d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary ic-color-btn" onclick="openEditModal({{ json_encode($plan) }})">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </button>
                                    <form action="{{ route('membresias.toggleStatus', $plan) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-primary ic-color-btn">
                                            {{ $plan->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-center text-muted py-4">No hay planes para mostrar.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL: CREAR PLAN ===== -->
<div class="modal fade" id="modalCrearPlan" tabindex="-1" role="dialog" aria-labelledby="modalCrearPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1E293B, #0F172A);">
                <h5 class="modal-title font-weight-bold" id="modalCrearPlanLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Nuevo Tipo de Membresía
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('membresias.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="nombre" class="font-weight-bold small text-dark">Nombre del Plan <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: 1 Mes, Trimestral VIP" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="duracion_dias" class="font-weight-bold small text-dark">Duración (Días) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="duracion_dias" id="duracion_dias" class="form-control" placeholder="30" min="1" required>
                                <div class="input-group-append"><span class="input-group-text small">Días</span></div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="precio" class="font-weight-bold small text-dark">Precio ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" step="0.01" name="precio" id="precio" class="form-control" placeholder="45.00" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="descripcion" class="font-weight-bold small text-dark">Servicios / Beneficios</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control" placeholder="Describe los beneficios..."></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="estado" class="font-weight-bold small text-dark">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="activo" selected>Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Guardar Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== MODAL: EDITAR PLAN ===== -->
<div class="modal fade" id="modalEditarPlan" tabindex="-1" role="dialog" aria-labelledby="modalEditarPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1E293B, #0F172A);">
                <h5 class="modal-title font-weight-bold" id="modalEditarPlanLabel">
                    <i class="fas fa-edit mr-2"></i> Editar Tipo de Membresía
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formEditarPlan" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label for="edit_nombre" class="font-weight-bold small text-dark">Nombre del Plan <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="edit_duracion_dias" class="font-weight-bold small text-dark">Duración (Días) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="duracion_dias" id="edit_duracion_dias" class="form-control" min="1" required>
                                <div class="input-group-append"><span class="input-group-text small">Días</span></div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="edit_precio" class="font-weight-bold small text-dark">Precio ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_descripcion" class="font-weight-bold small text-dark">Servicios / Beneficios</label>
                        <textarea name="descripcion" id="edit_descripcion" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="edit_estado" class="font-weight-bold small text-dark">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="edit_estado" class="form-control" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark btn-sm px-4 font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#mainPlanesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        scrollY: '100%',
        scrollCollapse: true,
        info: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row dataTables_scroll'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });
});

function switchView(view) {
    var tableView = document.getElementById('viewTable');
    var cardsView = document.getElementById('viewCards');
    var btnTable = document.getElementById('btnViewTable');
    var btnCards = document.getElementById('btnViewCards');

    if (view === 'cards') {
        tableView.classList.add('d-none');
        cardsView.classList.remove('d-none');
        btnCards.classList.add('active');
        btnTable.classList.remove('active');
    } else {
        cardsView.classList.add('d-none');
        tableView.classList.remove('d-none');
        btnTable.classList.add('active');
        btnCards.classList.remove('active');
    }
}

function openEditModal(plan) {
    var form = document.getElementById('formEditarPlan');
    form.action = '/membresias/' + plan.id;
    document.getElementById('edit_nombre').value = plan.nombre;
    document.getElementById('edit_duracion_dias').value = plan.duracion_dias;
    document.getElementById('edit_precio').value = plan.precio;
    document.getElementById('edit_descripcion').value = plan.descripcion || '';
    document.getElementById('edit_estado').value = plan.estado;
    $('#modalEditarPlan').modal('show');
}
</script>
@endpush