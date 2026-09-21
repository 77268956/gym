
@extends('layouts.app')

@section('title', 'Gestión de Tienda')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .ic-card { border:none; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,.04); }
    .ic-table th { background:#f8f9fc; color:#4e73df; text-transform:uppercase; font-size:.75rem; }
    .ic-status-active { background:#D1FAE5; color:#059669; padding:.35rem .75rem; border-radius:50px; font-size:.75rem; font-weight:600; }
    .ic-status-inactive { background:#F1F5F9; color:#475569; padding:.35rem .75rem; border-radius:50px; font-size:.75rem; font-weight:600; }
    .product-thumb { width:45px; height:45px; border-radius:8px; object-fit:cover; background:#F1F5F9; }
    .stock-badge { font-size:.72rem; font-weight:600; padding:.2rem .6rem; border-radius:50px; }

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
</style>
@endpush

@section('content')
<div class="container-fluid py-2">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- KPIs --}}
    <div class="row mb-3">
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
    <div class="ic-card card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h6 class="font-weight-bold mb-0"><i class="fas fa-shopping-bag text-primary mr-2"></i>Productos de la Tienda</h6>
            <button class="btn btn-primary btn-sm font-weight-bold px-3" onclick="abrirModalCrear()">
                <i class="fas fa-plus mr-1"></i>Nuevo Producto
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table ic-table mb-0">
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
                                        <div class="product-thumb mr-3 d-flex align-items-center justify-content-center text-muted"><i class="fas fa-box"></i></div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $producto->nombre }}</div>
                                        @if($producto->descripcion)
                                            <small class="text-muted">{{ Str::limit($producto->descripcion, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge badge-pill badge-light px-3">{{ $producto->categoria ?? '—' }}</span></td>
                            <td><strong class="text-primary">{{ number_format($producto->puntos_valor) }}</strong> pts</td>
                            <td>
                                <span class="stock-badge {{ $producto->stock == 0 ? 'bg-danger text-white' : ($producto->stock <= 5 ? 'bg-warning text-dark' : 'bg-light text-dark') }}">
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
    $('.ic-table').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        pageLength: 25,
        order: [],
        columnDefs: [
            { orderable: false, targets: [0, 6] }
        ]
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

