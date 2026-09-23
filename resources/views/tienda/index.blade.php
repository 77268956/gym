
@extends('layouts.app')

@section('title', 'Tienda EcoGim')

@section('skeleton')
    {{-- Search bar + filters --}}
    <div class="skel-row" style="align-items: center;">
        <div class="skel-box" style="height: 40px; flex: 2;"></div>
        <div class="skel-box" style="height: 40px; flex: 1;"></div>
        <div class="skel-box" style="height: 32px; width: 60px;"></div>
        <div class="skel-box" style="height: 32px; width: 60px;"></div>
        <div class="skel-box" style="height: 32px; width: 60px;"></div>
    </div>
    {{-- Product grid 4 columns --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem;">
        <div class="skel-box" style="height: 260px;"></div>
        <div class="skel-box" style="height: 260px;"></div>
        <div class="skel-box" style="height: 260px;"></div>
        <div class="skel-box" style="height: 260px;"></div>
        <div class="skel-box" style="height: 260px;"></div>
        <div class="skel-box" style="height: 260px;"></div>
        <div class="skel-box" style="height: 260px;"></div>
        <div class="skel-box" style="height: 260px;"></div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .store-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 1.25rem; }
    .product-card { background:#fff; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden; transition:transform .2s,box-shadow .2s; display:flex; flex-direction:column; }
    .product-card:hover { transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,.1); }
    .product-img { position:relative; width:100%; height:160px; background:#F1F5F9; display:flex; align-items:center; justify-content:center; color:#CBD5E1; font-size:3rem; overflow:hidden; }
    .product-img::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg,rgba(15,23,42,.08) 20%,rgba(15,23,42,.88) 100%); pointer-events:none; }
    .product-img img { width:100%; height:100%; object-fit:cover; filter:saturate(.78) brightness(.9); }
    .product-img img.product-click-image { cursor:zoom-in; }
    .product-overlay { position:absolute; inset:0; padding:.45rem; display:flex; align-items:flex-start; justify-content:flex-start; pointer-events:none; z-index:2; }
    .product-category { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--primary); margin-bottom:.3rem; }
    .product-category-badge { background:rgba(255,255,255,.92); border-radius:50px; padding:.3rem .65rem; box-shadow:0 2px 8px rgba(15,23,42,.12); }
    .product-points-badge { background:linear-gradient(135deg,var(--primary),#1E293B); color:#fff; border-radius:50px; padding:.35rem .7rem; font-size:.78rem; font-weight:800; box-shadow:0 2px 8px rgba(15,23,42,.28); }
    .product-image-info { position:absolute; left:.75rem; right:.75rem; bottom:.7rem; z-index:2; color:#fff; }
    .product-image-name { display:block; font-size:1.1rem; line-height:1.15; font-weight:800; color:#fff; text-shadow:0 1px 4px rgba(15,23,42,.85); }
    .product-body { padding:1rem; flex:1; display:flex; flex-direction:column; }
    .product-name { font-weight:800; font-size:1.12rem; line-height:1.15; color:#1E293B; margin:0; }
    .product-desc { font-size:.78rem; color:#64748B; flex:1; margin-bottom:.75rem; }
    .canje-product-summary { display:flex; align-items:center; gap:.85rem; padding:.75rem; background:#F8FAFC; border-radius:12px; margin-bottom:1rem; }
    .canje-product-summary img { width:76px; height:76px; border-radius:10px; object-fit:cover; flex-shrink:0; }
    .canje-product-summary .placeholder { width:76px; height:76px; border-radius:10px; background:#E2E8F0; color:#94A3B8; display:flex; align-items:center; justify-content:center; font-size:1.8rem; flex-shrink:0; }
    .canje-product-summary-name { font-weight:800; color:#1E293B; font-size:1rem; }
    .canje-product-summary-category { color:#64748B; font-size:.7rem; font-weight:700; text-transform:uppercase; }
    .canje-product-summary-description { color:#64748B; font-size:.78rem; line-height:1.3; margin-top:.25rem; }
    .canje-product-summary-points { color:var(--primary); font-size:.8rem; font-weight:800; margin-top:.35rem; }
    .product-footer { margin-top:auto; }
    .product-footer .btn { display:block; width:100%; }
    .store-search { position:relative; flex:1 1 420px; max-width:560px; }
    .store-search input { width:100%; border:1px solid #E2E8F0; border-radius:50px; padding:.55rem 1rem .55rem 2.35rem; font-size:.82rem; color:#1E293B; outline:none; }
    .store-search input:focus { border-color:var(--primary); box-shadow:0 0 0 .15rem rgba(78,115,223,.12); }
    .store-search i { position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:#94A3B8; }
    .points-filter { flex:0 1 190px; }
    .points-filter select { width:100%; border:1px solid #E2E8F0; border-radius:50px; padding:.55rem 2rem .55rem 1rem; font-size:.82rem; color:#475569; background:#fff; outline:none; cursor:pointer; }
    .points-filter select:focus { border-color:var(--primary); box-shadow:0 0 0 .15rem rgba(78,115,223,.12); }
    .filter-btn { border-radius:50px; font-size:.8rem; padding:.3rem .9rem; font-weight:600; border:1px solid #E2E8F0; background:#fff; color:#475569; cursor:pointer; transition:all .15s; }
    .filter-btn.active { background:var(--primary); color:#fff; border-color:var(--primary); }
    .pts-badge { background:linear-gradient(135deg,var(--primary),var(--primary-hover)); color:#fff; border-radius:50px; padding:.2rem .65rem; font-size:.75rem; font-weight:700; }
    /* Select2 custom styling for Bootstrap 4 */
    .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); border: 1px solid #ced4da; border-radius: .25rem; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: calc(1.5em + .75rem); color: #495057; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: calc(1.5em + .75rem); }
    
    .client-result { display: flex; align-items: center; }
    .client-result img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 15px; }
    .client-result .avatar-placeholder { width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; font-size: 14px; }
    .client-result .info { display: flex; flex-direction: column; }
    .client-result .name { font-weight: bold; color: #1e293b; }
    .client-result .cedula { font-size: 0.85em; color: #64748b; }
    .client-result .badges { margin-top: 4px; }
    .badge-membresia-activa { background: #D1FAE5; color: #059669; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-vencida { background: #FEE2E2; color: #DC2626; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
    .badge-membresia-sin { background: #F1F5F9; color: #475569; padding:.3rem .6rem; border-radius:50px; font-size:.72rem; font-weight:600; }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3" style="gap:.75rem;">
        <div class="store-search">
            <i class="fas fa-search"></i>
            <input type="search" id="buscadorProductos" placeholder="Buscar productos..." aria-label="Buscar productos">
        </div>
        <div class="points-filter">
            <select id="filtroPuntos" aria-label="Filtrar por puntos">
                <option value="todos">Todos los puntos</option>
                @foreach($productos->pluck('puntos_valor')->unique()->sort() as $puntos)
                    <option value="{{ $puntos }}">{{ number_format($puntos) }} puntos</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex flex-wrap" style="gap:.5rem;" id="filtros">
            <button class="filter-btn active" data-cat="todos">Todos</button>
            @foreach($categorias as $cat)
                <button class="filter-btn" data-cat="{{ $cat }}">{{ ucfirst($cat) }}</button>
            @endforeach
        </div>
    </div>

    @if($productos->isEmpty())
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-box-open fa-3x mb-3 d-block text-muted"></i>
            <strong>No hay productos disponibles.</strong><br>
            <small>El administrador puede agregar productos desde <strong>Administración → Gestión de Tienda</strong>.</small>
        </div>
    @else
        <div class="store-grid" id="productoGrid">
            @foreach($productos as $producto)
            <div class="product-card" data-cat="{{ $producto->categoria }}" data-points="{{ $producto->puntos_valor }}" data-search="{{ $producto->nombre }} {{ $producto->descripcion }} {{ $producto->categoria }}">
                <div class="product-img">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="product-click-image" data-image="{{ asset('storage/' . $producto->imagen) }}" data-description="{{ $producto->descripcion }}">
                    @else
                        <i class="fas fa-box"></i>
                    @endif
                    <div class="product-overlay">
                        <span class="product-points-badge"><i class="fas fa-star mr-1"></i>{{ number_format($producto->puntos_valor) }} pts</span>
                    </div>
                    <div class="product-image-info">
                        <div class="product-image-name">{{ $producto->nombre }}</div>
                    </div>
                </div>
                <div class="product-body">
                    <div class="product-footer pt-2">
                        <button class="btn btn-primary btn-sm font-weight-bold px-3"
                            data-product-name="{{ $producto->nombre }}"
                            data-product-image="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : '' }}"
                            data-product-description="{{ $producto->descripcion }}"
                            data-product-category="{{ $producto->categoria }}"
                            onclick="abrirModalCanje(this, {{ $producto->id }}, {{ $producto->puntos_valor }}, {{ $producto->stock }})">
                            <i class="fas fa-exchange-alt mr-1"></i>Canjear
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- MODAL IMAGEN DEL PRODUCTO --}}
<div class="modal fade" id="modalImagenProducto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 bg-dark py-2">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0 bg-dark text-center">
                <img id="imagenProductoModal" src="" alt="" style="width:100%;max-height:65vh;object-fit:contain;">
                <div id="descripcionProductoModal" class="text-white text-left px-4 py-3"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CANJE --}}
<div class="modal fade" id="modalCanje" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 pb-0 text-center" style="background:linear-gradient(135deg,#1E293B,#0F172A);padding:1.5rem 1.5rem 1rem;display:block;">
                <button type="button" class="close text-white position-absolute" style="top:15px;right:20px;opacity:.8;" data-dismiss="modal"><span>&times;</span></button>
                <i class="fas fa-exchange-alt fa-2x text-primary mb-2 d-block"></i>
                <h5 class="modal-title font-weight-bold text-white mb-1" id="modalCanjeTitle">Canjear Producto</h5>
                <small class="text-muted" id="modalCanjePts"></small>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="canje-product-summary">
                    <div id="canjeProductoImagenContenedor"></div>
                    <div class="min-width-0">
                        <div id="canjeProductoNombre" class="canje-product-summary-name"></div>
                        <div id="canjeProductoCategoria" class="canje-product-summary-category"></div>
                        <div id="canjeProductoDescripcion" class="canje-product-summary-description"></div>
                        <div id="canjeProductoPuntos" class="canje-product-summary-points"></div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="font-weight-bold small text-uppercase text-muted mb-1">Buscar Cliente</label>
                    <select id="selectCliente" style="width:100%;"></select>
                </div>
                <div id="clienteInfoBox" class="d-none">
                    <div class="card border-0 bg-light" style="border-radius:12px;">
                        <div class="card-body p-3 text-center">
                            <div id="clienteFoto" class="mb-2"></div>
                            <h6 id="clienteNombre" class="font-weight-bold mb-0"></h6>
                            <small id="clienteCedula" class="text-muted d-block mb-2"></small>
                            <div class="d-flex justify-content-center flex-wrap" style="gap:.5rem;">
                                <span class="pts-badge"><i class="fas fa-star mr-1"></i><span id="clientePuntos"></span> pts</span>
                                <span id="membresiaTag" class="badge badge-pill px-3 py-1" style="font-size:.75rem;"></span>
                            </div>
                        </div>
                    </div>
                    <div id="alertaYaCanjeo" class="alert alert-warning mt-3 mb-0 small d-none py-2">
                        <i class="fas fa-calendar-times mr-1"></i> Este cliente ya realizó un canje este mes.
                    </div>
                    <div id="alertaSinMembresia" class="alert alert-danger mt-3 mb-0 small d-none py-2">
                        <i class="fas fa-times-circle mr-1"></i> El cliente no tiene membresía activa y vigente.
                    </div>
                    <div id="alertaSinPuntos" class="alert alert-warning mt-3 mb-0 small d-none py-2">
                        <i class="fas fa-coins mr-1"></i> Puntos insuficientes para este producto.
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white py-3">
                <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnConfirmarCanje" class="btn btn-primary font-weight-bold px-4" disabled onclick="confirmarCanje()">
                    <i class="fas fa-check mr-1"></i>Confirmar Canje
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
var canjeProductoId = null, canjeProductoPuntos = 0, canjeClienteId = null;

document.querySelectorAll('.product-click-image').forEach(function(image) {
    image.addEventListener('click', function() {
        document.getElementById('imagenProductoModal').src = this.dataset.image;
        document.getElementById('imagenProductoModal').alt = this.alt;
        $('#modalImagenProducto').modal('show');
    });
});

function filtrarProductos() {
    var categoriaActiva = document.querySelector('.filter-btn.active').dataset.cat;
    var busqueda = document.getElementById('buscadorProductos').value.trim().toLowerCase();
    var puntosSeleccionados = document.getElementById('filtroPuntos').value;

    document.querySelectorAll('.product-card').forEach(function(card) {
        var coincideCategoria = categoriaActiva === 'todos' || card.dataset.cat === categoriaActiva;
        var coincideBusqueda = !busqueda || card.dataset.search.toLowerCase().includes(busqueda);
        var coincidePuntos = puntosSeleccionados === 'todos' || card.dataset.points === puntosSeleccionados;
        card.style.display = coincideCategoria && coincideBusqueda && coincidePuntos ? '' : 'none';
    });
}

document.getElementById('buscadorProductos').addEventListener('input', filtrarProductos);
document.getElementById('filtroPuntos').addEventListener('change', filtrarProductos);

document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        filtrarProductos();
    });
});

function abrirModalCanje(button, productoId, puntos, stock) {
    var nombre = button.dataset.productName;
    var imagen = button.dataset.productImage;
    var descripcion = button.dataset.productDescription;
    var categoria = button.dataset.productCategory;
    canjeProductoId = productoId; canjeProductoPuntos = puntos; canjeClienteId = null;
    document.getElementById('modalCanjeTitle').textContent = nombre;
    document.getElementById('modalCanjePts').textContent = puntos + ' puntos requeridos · ' + stock + ' en stock';
    document.getElementById('canjeProductoNombre').textContent = nombre;
    document.getElementById('canjeProductoCategoria').textContent = categoria || 'Sin categoría';
    document.getElementById('canjeProductoDescripcion').textContent = descripcion || 'Sin descripción disponible.';
    document.getElementById('canjeProductoPuntos').textContent = puntos + ' puntos';
    var imagenContenedor = document.getElementById('canjeProductoImagenContenedor');
    imagenContenedor.replaceChildren();
    if (imagen) {
        var imagenProducto = document.createElement('img');
        imagenProducto.src = imagen;
        imagenProducto.alt = nombre;
        imagenContenedor.appendChild(imagenProducto);
    } else {
        var imagenPlaceholder = document.createElement('div');
        imagenPlaceholder.className = 'placeholder';
        imagenPlaceholder.innerHTML = '<i class="fas fa-box"></i>';
        imagenContenedor.appendChild(imagenPlaceholder);
    }
    document.getElementById('clienteInfoBox').classList.add('d-none');
    document.getElementById('btnConfirmarCanje').disabled = true;
    $('#selectCliente').val(null).trigger('change');
    $('#modalCanje').modal('show');
}

$(document).ready(function() {
    $('#selectCliente').select2({
        dropdownParent: $('#modalCanje'),
        placeholder: 'Escribe nombre o cédula...',
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("tienda.buscarClientes") }}',
            dataType: 'json', delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
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
            var initials = client.text ? client.text.substring(0,2).toUpperCase() : '??';
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
                        '<span class="badge badge-info text-white" style="background:var(--primary);"><i class="fas fa-star mr-1"></i>' + (client.puntos || 0) + ' pts</span>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }
    
    function formatClientSelection(client) {
        if (!client.id) return client.text;
        return client.text + (client.cedula ? ' — ' + client.cedula : '');
    }

    $('#selectCliente').on('select2:select', function(e) {
        canjeClienteId = e.params.data.id;
        cargarInfoCliente(canjeClienteId);
    });

    $('#selectCliente').on('select2:clear', function() {
        canjeClienteId = null;
        document.getElementById('clienteInfoBox').classList.add('d-none');
        document.getElementById('btnConfirmarCanje').disabled = true;
    });

    $('#modalCanje').on('hidden.bs.modal', function() {
        $('#selectCliente').val(null).trigger('change');
        canjeClienteId = null; canjeProductoId = null;
    });
});

function cargarInfoCliente(clienteId) {
    $.getJSON('{{ url("tienda/cliente") }}/' + clienteId + '/info', function(data) {
        document.getElementById('clienteInfoBox').classList.remove('d-none');
        var fotoHtml = data.foto
            ? '<img src="' + data.foto + '" class="rounded-circle mb-1" style="width:60px;height:60px;object-fit:cover;">'
            : '<div class="rounded-circle mx-auto d-flex align-items-center justify-content-center bg-primary text-white font-weight-bold mb-1" style="width:60px;height:60px;font-size:1.3rem;">' + data.nombre.substring(0,2).toUpperCase() + '</div>';
        document.getElementById('clienteFoto').innerHTML = fotoHtml;
        document.getElementById('clienteNombre').textContent = data.nombre;
        document.getElementById('clienteCedula').textContent = data.cedula;
        document.getElementById('clientePuntos').textContent = data.puntos;

        var mTag = document.getElementById('membresiaTag');
        if (data.tiene_membresia_activa) {
            mTag.textContent = 'Membresía activa · vence ' + data.membresia_vence;
            mTag.className = 'badge badge-pill px-3 py-1 badge-success';
        } else {
            mTag.textContent = 'Sin membresía activa';
            mTag.className = 'badge badge-pill px-3 py-1 badge-danger';
        }

        document.getElementById('alertaYaCanjeo').classList.toggle('d-none', !data.ya_canje_este_mes);
        document.getElementById('alertaSinMembresia').classList.toggle('d-none', data.tiene_membresia_activa);
        document.getElementById('alertaSinPuntos').classList.toggle('d-none', data.puntos >= canjeProductoPuntos);

        var ok = data.tiene_membresia_activa && !data.ya_canje_este_mes && data.puntos >= canjeProductoPuntos;
        document.getElementById('btnConfirmarCanje').disabled = !ok;
    });
}

function confirmarCanje() {
    if (!canjeProductoId || !canjeClienteId) return;
    var btn = document.getElementById('btnConfirmarCanje');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Procesando...';

    fetch('{{ route("tienda.canjear") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ cliente_id: canjeClienteId, producto_id: canjeProductoId })
    })
    .then(r => r.json())
    .then(data => {
        $('#modalCanje').modal('hide');
        Swal.fire({ icon: data.ok ? 'success' : 'error', title: data.ok ? '¡Éxito!' : 'No se pudo canjear', text: data.mensaje, confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() });
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i>Confirmar Canje';
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error inesperado.' });
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i>Confirmar Canje';
    });
}
</script>
@endpush

