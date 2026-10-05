
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
    html, body { height: 100%; overflow: hidden; }
    #page-wrapper main {
        height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .store-page {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .store-toolbar { flex-shrink: 0; }
    .products-panel {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: .25rem .35rem .75rem .1rem;
    }
    .store-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 250px), 1fr)); justify-items:center; gap:1.5rem; }
    .product-card { position:relative; overflow:hidden; width:100%; max-width:310px; aspect-ratio:1 / 1; background:transparent; border:1px solid rgba(255,255,255,.16); border-radius:6px; box-shadow:0 8px 24px rgba(0,0,0,.1); transition:transform .2s ease,box-shadow .2s ease; }
    .product-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(0,0,0,.18); }
    .product-img { position:absolute; z-index:0; inset:0; display:flex; align-items:center; justify-content:center; overflow:hidden; background:transparent; color:#e5e5e5; font-size:2.5rem; }
    .product-img::after { content:""; position:absolute; inset:0; z-index:1; background:linear-gradient(180deg,rgba(15,23,42,.08) 20%,rgba(15,23,42,.9) 100%); pointer-events:none; }
    .product-img img { width:100%; height:100%; object-fit:cover; filter:none; }
    .product-img img.product-click-image { cursor:zoom-in; }
    .product-overlay { display:none; }
    .product-image-info { display:none; }
    .product-body { position:absolute; z-index:2; inset:0; display:flex; flex-direction:column; justify-content:flex-end; padding:1rem; background:transparent; color:#fff; pointer-events:none; }
    .product-name { overflow:hidden; margin:0 0 .35rem; color:#fff; font-size:1.08rem; font-weight:800; line-height:1.2; text-overflow:ellipsis; text-shadow:0 1px 3px #000,0 0 8px rgba(0,0,0,.95); white-space:nowrap; }
    .product-desc { display:-webkit-box; overflow:hidden; margin:0 0 .65rem; color:#fff; font-size:.76rem; line-height:1.35; text-shadow:0 1px 3px #000,0 0 8px rgba(0,0,0,.95); -webkit-box-orient:vertical; -webkit-line-clamp:2; }
    .product-meta { display:flex; flex-wrap:wrap; gap:.35rem; margin-bottom:.55rem; }
    .product-tag { display:inline-flex; align-items:center; gap:.35rem; max-width:100%; padding:.25rem .5rem; border:1px solid rgba(255,255,255,.38); border-radius:50px; background:transparent; color:#fff; font-size:.66rem; font-weight:700; text-shadow:0 1px 3px #000,0 0 6px rgba(0,0,0,.95); }
    .canje-product-summary { display:flex; align-items:center; gap:.85rem; padding:.75rem; background:#F8FAFC; border-radius:12px; margin-bottom:1rem; }
    .canje-product-summary img { width:76px; height:76px; border-radius:10px; object-fit:cover; flex-shrink:0; }
    .canje-product-summary .placeholder { width:76px; height:76px; border-radius:10px; background:#E2E8F0; color:#94A3B8; display:flex; align-items:center; justify-content:center; font-size:1.8rem; flex-shrink:0; }
    .canje-product-summary-name { font-weight:800; color:#1E293B; font-size:1rem; }
    .canje-product-summary-category { color:#64748B; font-size:.7rem; font-weight:700; text-transform:uppercase; }
    .canje-product-summary-description { color:#64748B; font-size:.78rem; line-height:1.3; margin-top:.25rem; }
    .canje-product-summary-points { color:var(--primary); font-size:.8rem; font-weight:800; margin-top:.35rem; }
    .product-footer { margin-top:auto; display:flex; justify-content:flex-end; }
    .product-footer .btn { display:inline-flex; align-items:center; align-self:flex-end; gap:.4rem; min-height:30px; margin-top:auto; padding:.3rem .55rem; border:1px solid rgba(255,255,255,.72); border-radius:7px; background:transparent; color:#fff; font-size:.72rem; font-weight:800; pointer-events:auto; text-shadow:0 1px 3px #000,0 0 6px rgba(0,0,0,.95); transition:border-color .2s ease,opacity .2s ease; }
    .product-footer .btn:hover { border-color:#fff; color:#fff; opacity:.82; }
    .canje-modal { background:#191b1e; color:#fff; }
    .canje-modal .modal-header { position:absolute; top:0; right:0; z-index:5; min-height:0; padding:0 !important; background:transparent; }
    .canje-modal .close { top:12px !important; right:14px !important; width:32px; height:32px; border:1px solid rgba(255,255,255,.45); border-radius:50%; background:rgba(0,0,0,.35); font-size:1.35rem; line-height:1; }
    .canje-modal .modal-body { display:grid; grid-template-columns:minmax(0,42%) minmax(0,58%); padding:0; background:#191b1e; }
    .canje-modal-dialog { max-width:780px; }
    .canje-modal-image { position:relative; display:flex; align-items:center; justify-content:center; min-height:390px; background:#090a0c; overflow:hidden; }
    .canje-modal-image::after { content:""; position:absolute; inset:0; background:linear-gradient(180deg,rgba(9,10,12,.02) 25%,rgba(9,10,12,.55) 100%); pointer-events:none; }
    .canje-modal-image img { position:relative; z-index:0; display:block; width:100%; height:390px; min-height:390px; object-fit:cover; }
    .canje-modal-image #canjeProductoImagenContenedor { width:100%; height:100%; }
    .canje-modal-image #canjeProductoImagenContenedor img { width:100%; height:100%; object-fit:cover; }
    .canje-modal-image .placeholder { color:#64748B; font-size:3rem; }
    .canje-modal-content { display:flex; flex-direction:column; justify-content:center; padding:2rem 1.5rem 1.5rem; overflow-y:auto; max-height:510px; }
    .canje-modal-content .canje-product-summary { display:block; padding:0; margin-bottom:1.1rem; background:transparent; }
    .canje-modal-content .canje-product-summary-name { color:#fff; font-size:1.7rem; font-weight:800; line-height:1.1; text-shadow:0 1px 3px #000,0 0 8px rgba(0,0,0,.95); }
    .canje-modal-content .canje-product-summary-category { color:var(--primary); margin-top:.45rem; letter-spacing:.08em; }
    .canje-modal-content .canje-product-summary-description { color:#c4c6c8; font-size:.88rem; line-height:1.45; margin-top:.55rem; }
    .canje-modal-content .canje-product-summary-points { display:inline-flex; align-items:center; gap:.35rem; padding:.4rem .7rem; border:1px solid rgba(255,255,255,.2); border-radius:50px; color:#fff; font-size:.78rem; }
    .canje-modal-content label { color:#c4c6c8 !important; letter-spacing:.06em; }
    .canje-modal-content .select2-container .select2-selection--single { height:42px; border:1px solid rgba(255,255,255,.22); border-radius:7px; background:#24272b; }
    .canje-modal-content .select2-container--default .select2-selection--single .select2-selection__rendered { color:#fff; line-height:40px; padding-left:13px; }
    .canje-modal-content .select2-container--default .select2-selection--single .select2-selection__arrow { height:40px; }
    .canje-modal-content .card { background:#24272b !important; color:#fff; border:1px solid rgba(255,255,255,.12) !important; box-shadow:0 8px 22px rgba(0,0,0,.16); }
    .canje-modal-content .alert { border:1px solid rgba(255,255,255,.12); border-radius:7px; }
    .canje-modal-content .text-muted { color:#aeb2b6 !important; }
    .canje-modal .modal-footer { background:#191b1e !important; border-top:1px solid rgba(255,255,255,.12); padding:1rem 1.5rem !important; }
    .canje-modal .modal-footer .btn { min-width:125px; border-radius:5px; font-weight:700; }
    .canje-modal .modal-footer .btn-outline-secondary { border-color:rgba(255,255,255,.35); color:#fff; }
    @media (max-width: 575.98px) {
        .canje-modal .modal-body { display:block; }
        .canje-modal-image, .canje-modal-image img { min-height:220px; height:220px; }
        .canje-modal-content { max-height:none; }
    }
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
<div class="container-fluid py-2 store-page">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 store-toolbar" style="gap:.75rem;">
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
        <div class="products-panel">
            <div class="store-grid" id="productoGrid">
                @foreach($productos as $producto)
                <div class="product-card" data-cat="{{ $producto->categoria }}" data-points="{{ $producto->puntos_valor }}" data-search="{{ $producto->nombre }} {{ $producto->descripcion }} {{ $producto->categoria }}">
                    <div class="product-img">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="product-click-image" data-image="{{ asset('storage/' . $producto->imagen) }}" data-description="{{ $producto->descripcion }}">
                        @else
                            <i class="fas fa-box"></i>
                        @endif
                    </div>
                    <div class="product-body">
                        <h3 class="product-name">{{ $producto->nombre }}</h3>
                        <p class="product-desc">{{ $producto->descripcion ?: 'Canjea este producto con tus puntos EcoGim.' }}</p>
                        <div class="product-meta">
                            <span class="product-tag"><i class="fas fa-star"></i>{{ number_format($producto->puntos_valor) }} pts</span>
                            @if($producto->categoria)
                                <span class="product-tag">{{ ucfirst($producto->categoria) }}</span>
                            @endif
                        </div>
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
    <div class="modal-dialog modal-dialog-centered canje-modal-dialog" role="document">
        <div class="modal-content canje-modal border-0 shadow-lg" style="border-radius:6px;overflow:hidden;">
            <div class="modal-header border-0 text-center">
                <button type="button" class="close text-white position-absolute" style="top:15px;right:20px;opacity:.8;" data-dismiss="modal"><span>&times;</span></button>
                <h5 class="sr-only modal-title" id="modalCanjeTitle">Canjear Producto</h5>
                <span id="modalCanjePts" class="sr-only"></span>
            </div>
            <div class="modal-body">
                <div class="canje-modal-image">
                    <div id="canjeProductoImagenContenedor"></div>
                </div>
                <div class="canje-modal-content">
                    <div class="canje-product-summary">
                        <div class="min-width-0">
                            <div id="canjeProductoNombre" class="canje-product-summary-name"></div>
                            <div id="canjeProductoCategoria" class="canje-product-summary-category"></div>
                            <div id="canjeProductoDescripcion" class="canje-product-summary-description"></div>
                            <div id="canjeProductoPuntos" class="canje-product-summary-points"><i class="fas fa-star"></i></div>
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
            </div>
            <div class="modal-footer">
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
