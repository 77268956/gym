@extends('layouts.app')

@section('title', 'Tipos de Membresía')

@push('styles')
<style>
    :root {
        --ic-accent: #2563EB;
        --ic-accent-soft: #EFF6FF;
        --ic-green: #10B981;
        --ic-red: #EF4444;
        --ic-border: #E2E8F0;
        --ic-muted: #64748B;
    }
    .ic-card {
        background: #fff;
        border: 1px solid var(--ic-border);
        border-radius: .75rem;
        padding: 1.25rem;
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    /* ── Stat cards ── */
    .ic-stat-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .45rem;
        min-height: 72px;
    }
    .ic-stat-label {
        font-size: .72rem;
        color: var(--ic-muted);
        letter-spacing: .03em;
        text-transform: uppercase;
        font-weight: 600;
    }
    .ic-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1c1c1a;
    }
    .ic-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: .5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .ic-icon-blue { background: var(--ic-accent-soft); color: var(--ic-accent); }
    .ic-icon-green { background: #dcfce7; color: var(--ic-green); }
    .ic-icon-gray { background: #f1f1ef; color: #6b6b66; }
    .ic-icon-yellow { background: #fef9c3; color: #8a6d16; }

    /* ── Badges ── */
    .ic-badge-active   { background: #D1FAE5; color: #059669; font-weight: 600; }
    .ic-badge-inactive { background: #F1F5F9; color: #475569; font-weight: 600; }
    .ic-badge-full     { background: #F1F5F9; color: #475569; font-weight: 600; }
    .ic-badge-pending  { background: #FEF3C7; color: #D97706; font-weight: 600; }
    .ic-badge-critical { background: #FEE2E2; color: var(--ic-red); font-weight: 600; }
    .ic-badge-warn     { background: #FEF3C7; color: #D97706; font-weight: 600; }

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
        font-size: .84rem;
        vertical-align: middle;
        border-top: 1px solid #f1f1ef;
    }
    .ic-table tbody tr { transition: background-color .15s ease; }
    .ic-table tbody tr:hover { background: #fbfdfc; }

    /* ── Action button ── */
    .ic-action-button {
        width: 32px;
        height: 32px;
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

    /* ── Plan Pricing Card ── */
    .plan-card {
        border: 1px solid var(--ic-border);
        border-radius: .75rem;
        transition: transform .2s ease, box-shadow .2s ease;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .plan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .07);
        border-color: #0082CA;
    }
    .plan-card-header {
        padding: 1.25rem 1.25rem 0.75rem;
        border-bottom: 1px solid #f1f1ef;
    }
    .plan-price {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1c1c1a;
    }
    .plan-card-body {
        padding: 1.25rem;
        flex: 1;
    }
    .plan-card-footer {
        padding: 0.85rem 1.25rem;
        background: #fafaf8;
        border-top: 1px solid #f1f1ef;
        border-radius: 0 0 .75rem .75rem;
    }
    
    .custom-tab-btn.active {
        background-color: var(--ic-accent) !important;
        color: #fff !important;
        border-color: var(--ic-accent) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Por favor verifica los campos ingresados:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ===== ROW 1 — Tarjetas de Estadísticas ===== --}}
    <div class="row mb-4">
        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="ic-card ic-stat-card">
                <div>
                    <span class="ic-stat-label">TOTAL DE PLANES</span>
                    <div class="ic-stat-value mt-1">{{ $totalPlanes }}</div>
                </div>
                <div class="ic-stat-icon ic-icon-blue">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="ic-card ic-stat-card">
                <div>
                    <span class="ic-stat-label">PLANES ACTIVOS</span>
                    <div class="ic-stat-value mt-1 text-success">{{ $planesActivos }}</div>
                </div>
                <div class="ic-stat-icon ic-icon-green">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="ic-card ic-stat-card">
                <div>
                    <span class="ic-stat-label">PLANES INACTIVOS</span>
                    <div class="ic-stat-value mt-1 text-muted">{{ $planesInactivos }}</div>
                </div>
                <div class="ic-stat-icon ic-icon-gray">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 mb-3 mb-lg-0">
            <div class="ic-card ic-stat-card">
                <div>
                    <span class="ic-stat-label">PRECIO PROMEDIO</span>
                    <div class="ic-stat-value mt-1">${{ number_format($precioPromedio, 2) }}</div>
                </div>
                <div class="ic-stat-icon ic-icon-yellow">
                    <i class="fas fa-tag"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2 — Toolbar y Filtros ===== --}}
    <div class="ic-card mb-4" style="padding: 0.9rem 1.1rem;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            
            <!-- Título y buscador -->
            <div class="d-flex align-items-center mb-3 mb-md-0 flex-wrap">
                <h5 class="mb-0 font-weight-bold text-dark mr-4">
                    <i class="fas fa-id-card text-primary mr-2"></i> Tipos de Membresías y Planes
                </h5>

                <!-- Filtros -->
                <form action="{{ route('membresias.index') }}" method="GET" class="form-inline mt-2 mt-sm-0">
                    <div class="input-group input-group-sm mr-2" style="width: 210px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="buscar" class="form-control border-left-0" placeholder="Buscar plan..." value="{{ request('buscar') }}">
                    </div>

                    <select name="estado" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>

                    @if(request('buscar') || request('estado'))
                        <a href="{{ route('membresias.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Botones de vista y agregar -->
            <div class="d-flex align-items-center">
                <div class="btn-group btn-group-sm mr-3" role="group">
                    <button type="button" class="btn btn-outline-primary custom-tab-btn active" id="btnViewTable" onclick="switchView('table')">
                        <i class="fas fa-list mr-1"></i> Tabla
                    </button>
                    <button type="button" class="btn btn-outline-primary custom-tab-btn" id="btnViewCards" onclick="switchView('cards')">
                        <i class="fas fa-th-large mr-1"></i> Tarjetas
                    </button>
                </div>

                <button type="button" class="btn btn-primary btn-sm px-3 font-weight-bold" data-toggle="modal" data-target="#modalCrearPlan">
                    <i class="fas fa-plus mr-1"></i> Nuevo Plan
                </button>
            </div>
        </div>
    </div>

    {{-- ===== VISTA 1: TABLA DE GESTIÓN ===== --}}
    <div id="viewTable" class="ic-card p-0 mb-4">
        <div class="table-responsive">
            <table class="table ic-table mb-0">
                <thead>
                    <tr>
                        <th>NOMBRE DEL PLAN</th>
                        <th>DURACIÓN</th>
                        <th>PRECIO</th>
                        <th>BENEFICIOS / SERVICIOS</th>
                        <th>ESTADO</th>
                        <th class="text-right pr-4">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($planes as $plan)
                    <tr>
                        <td class="font-weight-bold text-dark">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-2 text-primary font-weight-bold" style="width:36px; height:36px;">
                                    <i class="fas fa-dumbbell"></i>
                                </div>
                                <div>
                                    <span class="d-block text-dark font-weight-bold">{{ $plan->nombre }}</span>
                                    <small class="text-muted">ID: #{{ $plan->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light border px-2 py-1 text-dark" style="font-size: .8rem;">
                                <i class="far fa-clock mr-1 text-muted"></i> {{ $plan->duracion_dias }} {{ $plan->duracion_dias == 1 ? 'Día' : 'Días' }}
                            </span>
                        </td>
                        <td>
                            <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                ${{ number_format($plan->precio, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted d-inline-block text-truncate" style="max-width: 320px;" title="{{ $plan->descripcion ?? 'Sin descripción detallada' }}">
                                {{ $plan->descripcion ?? 'Servicios estándar de gimnasio' }}
                            </span>
                        </td>
                        <td>
                            @if($plan->estado === 'activo')
                                <span class="ic-badge-active px-2.5 py-1 rounded-pill small">
                                    <i class="fas fa-circle mr-1" style="font-size: 7px;"></i> ACTIVO
                                </span>
                            @else
                                <span class="ic-badge-inactive px-2.5 py-1 rounded-pill small">
                                    <i class="fas fa-circle mr-1" style="font-size: 7px;"></i> INACTIVO
                                </span>
                            @endif
                        </td>
                        <td class="text-right pr-4">
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm ic-action-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow-sm border">
                                    <button class="dropdown-item" 
                                            onclick="openEditModal({{ json_encode($plan) }})">
                                        <i class="fas fa-edit mr-2 text-info"></i> Editar Plan
                                    </button>
                                    <form action="{{ route('membresias.toggleStatus', $plan) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            @if($plan->estado === 'activo')
                                                <i class="fas fa-ban mr-2 text-warning"></i> Desactivar
                                            @else
                                                <i class="fas fa-check-circle mr-2 text-success"></i> Activar
                                            @endif
                                        </button>
                                    </form>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('membresias.destroy', $plan) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este plan de membresía?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash-alt mr-2"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-id-card fa-3x mb-3 d-block opacity-50"></i>
                            No se encontraron planes de membresía configurados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($planes->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $planes->links() }}
            </div>
        @endif
    </div>

    {{-- ===== VISTA 2: TARJETAS / PRICING CARDS ===== --}}
    <div id="viewCards" class="row d-none mb-4">
        @forelse($planes as $plan)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="plan-card">
                <div class="plan-card-header d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge {{ $plan->estado === 'activo' ? 'badge-primary' : 'badge-secondary' }} px-2 py-1 mb-2">
                            {{ strtoupper($plan->estado) }}
                        </span>
                        <h5 class="font-weight-bold text-dark mb-0">{{ $plan->nombre }}</h5>
                    </div>
                    <div class="text-right">
                        <span class="plan-price">${{ number_format($plan->precio, 2) }}</span>
                        <small class="d-block text-muted">/ {{ $plan->duracion_dias }} Días</small>
                    </div>
                </div>
                <div class="plan-card-body">
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle mr-1 text-primary"></i> 
                        {{ $plan->descripcion ?? 'Acceso a instalaciones, vestidores y equipamiento principal.' }}
                    </p>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Duración equivalente a {{ round($plan->duracion_dias / 30, 1) }} mes(es)</li>
                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> Acceso a rutinas de entrenamiento</li>
                        <li><i class="fas fa-check text-success mr-2"></i> Acumulación de puntos EcoGim</li>
                    </ul>
                </div>
                <div class="plan-card-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditModal({{ json_encode($plan) }})">
                        <i class="fas fa-edit mr-1"></i> Editar
                    </button>
                    <form action="{{ route('membresias.toggleStatus', $plan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $plan->estado === 'activo' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                            {{ $plan->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="ic-card text-center py-5 text-muted">
                No hay planes para mostrar en vista de tarjetas.
            </div>
        </div>
        @endforelse
    </div>

</div>

<!-- ===== MODAL: CREAR PLAN ===== -->
<div class="modal fade" id="modalCrearPlan" tabindex="-1" role="dialog" aria-labelledby="modalCrearPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalCrearPlanLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Nuevo Tipo de Membresía
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('membresias.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    
                    <div class="form-group mb-3">
                        <label for="nombre" class="font-weight-bold small text-dark">Nombre del Plan <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: 1 Mes, Trimestral VIP, Pase Anual" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="duracion_dias" class="font-weight-bold small text-dark">Duración (Días) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="duracion_dias" id="duracion_dias" class="form-control" placeholder="30" min="1" required>
                                <div class="input-group-append">
                                    <span class="input-group-text small">Días</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="precio" class="font-weight-bold small text-dark">Precio ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" name="precio" id="precio" class="form-control" placeholder="45.00" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="descripcion" class="font-weight-bold small text-dark">Servicios / Beneficios Incluidos</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control" placeholder="Describe los servicios o beneficios de esta membresía..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label for="estado" class="font-weight-bold small text-dark">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-control" required>
                            <option value="activo" selected>Activo (Disponible para venta)</option>
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
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="modalEditarPlanLabel">
                    <i class="fas fa-edit mr-2"></i> Editar Tipo de Membresía
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarPlan" method="POST">
                @csrf
                @method('PUT')
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
                                <div class="input-group-append">
                                    <span class="input-group-text small">Días</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="edit_precio" class="font-weight-bold small text-dark">Precio ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_descripcion" class="font-weight-bold small text-dark">Servicios / Beneficios Incluidos</label>
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
                        <i class="fas fa-save mr-1"></i> Actualizar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
