
@extends('layouts.app')

@section('title', 'Gestión de Tienda')

@section('skeleton')
    <div class="skel-box" style="height: 32px; width: 220px; margin-bottom: 1.5rem;"></div>
    {{-- 3 KPI cards --}}
    <div class="skel-row">
        <div class="skel-box" style="height: 70px; flex: 1;"></div>
        <div class="skel-box" style="height: 70px; flex: 1;"></div>
        <div class="skel-box" style="height: 70px; flex: 1;"></div>
    </div>
    {{-- Table --}}
    <div class="skel-box" style="height: 42px; width: 100%; margin-bottom: 0.5rem; border-radius: 8px 8px 0 0;"></div>
    <div class="skel-box" style="flex: 1; width: 100%; border-radius: 0 0 8px 8px;"></div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    body, html { overflow: hidden; height: 100%; }
    #page-wrapper main {
        padding: 1rem 1.5rem !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 60px);
        overflow: hidden;
    }
    .main-container { flex: 1; min-height: 0; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }
    .table-panel { flex: 1; min-height: 0; overflow: hidden; }
    .dataTables_wrapper { display: flex; flex-direction: column; height: 100%; }
    .dataTables_wrapper .row { margin-left: 0; margin-right: 0; }
    .dataTables_scroll { flex-grow: 1; overflow: hidden; display: flex; flex-direction: column; min-height: 0; margin-top: .5rem; margin-bottom: .5rem; }
    .dataTables_scrollBody { flex-grow: 1; min-height: 0; overflow-y: auto !important; max-height: none !important; height: auto !important; }
    :root { --card-color: var(--sidebar-bg); }
    .ic-card { background:#fff; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,.04); padding:.85rem; display:flex; flex-direction:column; margin-bottom:0 !important; }
    .ic-card-title { font-weight:700; font-size:.8rem; color:var(--sidebar-bg); margin-bottom:.5rem; text-transform:uppercase; }
    .ic-table th { background:#eaecf4; color:var(--primary); border-bottom:2px solid var(--primary); text-transform:uppercase; font-size:.75rem; font-weight:700; padding:.75rem .5rem; letter-spacing:.5px; }
    .ic-table td { font-size:.85rem; vertical-align:middle; white-space:nowrap; border-top:1px solid #e3e6f0; padding:.6rem .5rem; color:#5a5c69; }
    .ic-status-active, .ic-status-inactive, .ic-category-badge, .stock-badge { background:var(--card-color); color:#fff; padding:.35rem .75rem; border-radius:50px; font-size:.75rem; font-weight:600; }
    .product-thumb { width:45px; height:45px; border-radius:8px; object-fit:cover; background:var(--card-color); }
    .stock-badge { font-size:.72rem; padding:.2rem .6rem; }
    .ic-card-icon { color:var(--card-color); }

    .kpi-card {
        border-radius: 10px;
        border: none;
        padding: 0.6rem 1rem;
        color: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--card-color);
        height: 100%;
    }
    .kpi-icon { font-size: 1.8rem; opacity: 0.4; }
    .kpi-value { font-size: 1.4rem; font-weight: 800; margin: 0; line-height: 1; }
    .kpi-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; opacity: 0.8; margin-top: 2px;}
</style>
@endpush

@section('content')
<div class="container-fluid main-container">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row tight flex-shrink-0 mb-3">
        <div class="col-md-4 mb-2">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $totalProductos }}</h3>
                    <div class="kpi-label">Productos activos</div>
                </div>
                <i class="fas fa-box kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $stockTotal }}</h3>
                    <div class="kpi-label">Unidades en stock</div>
                </div>
                <i class="fas fa-cubes kpi-icon"></i>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="kpi-card">
                <div>
                    <h3 class="kpi-value">{{ $totalCanjes }}</h3>
                    <div class="kpi-label">Canjes totales</div>
                </div>
                <i class="fas fa-exchange-alt kpi-icon"></i>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="ic-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0">
            <div class="d-flex align-items-center">
                <h5 class="ic-card-title mb-0 mr-3"><i class="fas fa-shopping-bag text-primary mr-2"></i>Productos de la tienda</h5>
                <div class="input-group input-group-sm" style="width:250px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="customSearch" class="form-control border-left-0" placeholder="Buscar producto..." style="background-color:#F8FAFC;">
                </div>
            </div>
            <div class="d-flex align-items-center">
                <select id="categoryFilter" class="form-control form-control-sm mr-2" style="width:140px;">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria }}">{{ $categoria }}</option>
                    @endforeach
                </select>
                <select id="statusFilter" class="form-control form-control-sm mr-2" style="width:110px;">
                    <option value="">Todos</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
                </select>
                <button class="btn btn-sm btn-primary" onclick="abrirModalCrear()" title="Nuevo producto" style="width:35px;height:35px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="table-panel">
                <table id="productosTable" class="table ic-table w-100">
                    <thead>
                        <tr>
                            <th class="pl-4">Producto</th>
                            <th>Categoría</th>
                            <th>Puntos</th>
                            <th>Stock</th>
                            <th>Canjes</th>
                            <th>Estado</th>
                            <th class="text-right pr-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr class="{{ $producto->trashed() ? 'table-secondary text-muted' : '' }}">
                            <td class="pl-4">
                                <div class="d-flex align-items-center">
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" class="product-thumb mr-3" alt="{{ $producto->nombre }}">
                                    @else
                                        <div class="product-thumb mr-3 d-flex align-items-center justify-content-center text-white"><i class="fas fa-box"></i></div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $producto->nombre }}</div>
                                        @if($producto->descripcion)
                                            <small class="text-muted">{{ Str::limit($producto->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="ic-category-badge">{{ $producto->categoria ?? '—' }}</span></td>
                            <td><strong class="text-primary">{{ number_format($producto->puntos_valor) }}</strong> pts</td>
                            <td>
                                <span class="stock-badge">
                                    {{ $producto->stock }}
                                </span>
                            </td>
                            <td>{{ $producto->canjes_count }}</td>
                            <td>
                                @if($producto->trashed())
                                    <span class="ic-status-inactive">Eliminado</span>
                                @elseif($producto->estado === 'activo')
                                    <span class="ic-status-active">Activo</span>
                                @else
                                    <span class="ic-status-inactive">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-right pr-4">
                                @if(!$producto->trashed())
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light py-0 px-2" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="dropdown-menu dropdown-menu-right" style="font-size:0.8rem;">
                                        <button type="button" class="dropdown-item py-1" onclick="abrirModalEditar({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', '{{ addslashes($producto->descripcion) }}', '{{ $producto->categoria }}', {{ $producto->puntos_valor }}, {{ $producto->stock }}, '{{ $producto->estado }}', '{{ $producto->imagen ? asset('storage/'.$producto->imagen) : '' }}')">
                                            <i class="fas fa-pen text-primary mr-2"></i> Editar
                                        </button>
                                        <form action="{{ route('admin.productos.toggleStatus', $producto) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item py-1">
                                                <i class="fas {{ $producto->estado === 'activo' ? 'fa-eye-slash text-secondary' : 'fa-eye text-secondary' }} mr-2"></i> 
                                                {{ $producto->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('admin.productos.destroy', $producto) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este producto?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-1 text-danger">
                                                <i class="fas fa-trash text-danger mr-2"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-box-open fa-2x d-block mb-2"></i>No hay productos creados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
        </div>
    </div>
</div>

{{-- MODAL CREAR/EDITAR --}}
<div class="modal fade" id="modalProducto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1E293B,#0F172A);">
                <h6 class="modal-title font-weight-bold text-white" id="modalProductoTitle"><i class="fas fa-box mr-2 text-primary"></i>Nuevo Producto</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="formProducto" action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <span id="methodField"></span>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="inpNombre" class="form-control" required maxlength="100" placeholder="Ej: Termo de acero inoxidable">
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small">Descripción</label>
                                <textarea name="descripcion" id="inpDescripcion" class="form-control" rows="2" maxlength="500" placeholder="Breve descripción del producto..."></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small">Categoría</label>
                                        <input type="text" name="categoria" id="inpCategoria" class="form-control" maxlength="50" placeholder="suplementos, toallas, termos..." list="categoriasExistentes">
                                        <datalist id="categoriasExistentes">
                                            @foreach($categorias as $cat)
                                                <option value="{{ $cat }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small">Puntos <span class="text-danger">*</span></label>
                                        <input type="number" name="puntos_valor" id="inpPuntos" class="form-control" min="1" required placeholder="50">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="font-weight-bold small">Stock <span class="text-danger">*</span></label>
                                        <input type="number" name="stock" id="inpStock" class="form-control" min="0" required placeholder="10">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold small">Estado</label>
                                <select name="estado" id="inpEstado" class="form-control">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold small">Imagen del Producto</label>
                            <div class="border rounded p-3 text-center" style="background:#F8FAFC;">
                                <img id="imagenPreview" src="#" alt="Preview" class="rounded mb-2 d-none" style="width:100%;max-height:120px;object-fit:cover;">
                                <div id="imagenPlaceholder" class="text-muted py-3">
                                    <i class="fas fa-image fa-2x mb-2 d-block"></i>
                                    <small>Sin imagen</small>
                                </div>
                            </div>
                            <div class="custom-file mt-2">
                                <input type="file" name="imagen" id="inpImagen" class="custom-file-input" accept="image/*" onchange="previewImagen(this)">
                                <label class="custom-file-label" for="inpImagen" style="font-size:.8rem;">Seleccionar imagen...</label>
                            </div>
                            <div id="eliminarImagenBox" class="custom-control custom-checkbox mt-2 d-none">
                                <input type="checkbox" class="custom-control-input" id="chkEliminarImagen" name="eliminar_imagen" value="1">
                                <label class="custom-control-label text-danger small" for="chkEliminarImagen">Eliminar imagen actual</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">
                        <i class="fas fa-save mr-1"></i><span id="btnModalLabel">Guardar Producto</span>
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
    var table = $('#productosTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        order: [],
        scrollY: '100%',
        scrollCollapse: true,
        info: true,
        dom: "<'row dataTables_scroll'<'col-sm-12'tr>>" +
             "<'row mt-2 align-items-center'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>",
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ]
    });

    $('#customSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#categoryFilter').on('change', function() {
        table.column(1).search(this.value).draw();
    });

    $('#statusFilter').on('change', function() {
        table.column(5).search(this.value).draw();
    });
});

function previewImagen(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagenPreview').src = e.target.result;
            document.getElementById('imagenPreview').classList.remove('d-none');
            document.getElementById('imagenPlaceholder').classList.add('d-none');
        };
        reader.readAsDataURL(input.files[0]);
        var lbl = input.nextElementSibling;
        if (lbl) lbl.textContent = input.files[0].name;
    }
}

function resetModal() {
    document.getElementById('formProducto').reset();
    document.getElementById('imagenPreview').classList.add('d-none');
    document.getElementById('imagenPlaceholder').classList.remove('d-none');
    document.getElementById('eliminarImagenBox').classList.add('d-none');
    document.querySelector('.custom-file-label').textContent = 'Seleccionar imagen...';
    document.getElementById('methodField').innerHTML = '';
}

function abrirModalCrear() {
    resetModal();
    document.getElementById('formProducto').action = '{{ route("admin.productos.store") }}';
    document.getElementById('modalProductoTitle').innerHTML = '<i class="fas fa-plus mr-2 text-primary"></i>Nuevo Producto';
    document.getElementById('btnModalLabel').textContent = 'Guardar Producto';
    $('#modalProducto').modal('show');
}

function abrirModalEditar(id, nombre, descripcion, categoria, puntos, stock, estado, imagenUrl) {
    resetModal();
    document.getElementById('formProducto').action = '/admin/productos/' + id;
    document.getElementById('methodField').innerHTML = '@csrf<input type="hidden" name="_method" value="PUT">';
    document.getElementById('modalProductoTitle').innerHTML = '<i class="fas fa-edit mr-2 text-primary"></i>Editar Producto';
    document.getElementById('btnModalLabel').textContent = 'Actualizar Producto';

    document.getElementById('inpNombre').value = nombre;
    document.getElementById('inpDescripcion').value = descripcion;
    document.getElementById('inpCategoria').value = categoria;
    document.getElementById('inpPuntos').value = puntos;
    document.getElementById('inpStock').value = stock;
    document.getElementById('inpEstado').value = estado;

    if (imagenUrl) {
        document.getElementById('imagenPreview').src = imagenUrl;
        document.getElementById('imagenPreview').classList.remove('d-none');
        document.getElementById('imagenPlaceholder').classList.add('d-none');
        document.getElementById('eliminarImagenBox').classList.remove('d-none');
    }

    $('#modalProducto').modal('show');
}
</script>
@endpush
