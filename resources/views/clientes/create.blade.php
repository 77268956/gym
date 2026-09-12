@extends('layouts.app')

@section('title', 'Nuevo Socio / Cliente')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('user') }}"><i class="fas fa-users mr-1"></i> Socios / Clientes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nuevo Socio</li>
                </ol>
            </nav>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Por favor verifica los datos:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            {{-- Success --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold text-primary">
                        <i class="fas fa-user-plus mr-2"></i> Registrar Nuevo Socio / Cliente
                    </h5>
                    <a href="{{ route('user') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Hidden webcam fields --}}
                        <input type="hidden" name="foto_base64" id="foto_base64">
                        <input type="hidden" name="descriptor_facial" id="descriptor_facial">
                        {{-- Hidden membresia selection --}}
                        <input type="hidden" name="tipo_membresia_id" id="tipo_membresia_id" value="{{ old('tipo_membresia_id') }}">

                        {{-- SECCIÓN 1: DATOS PERSONALES --}}
                        <h6 class="text-uppercase text-muted font-weight-bold mb-3 border-bottom pb-2" style="letter-spacing:.05em;font-size:.75rem;">
                            1. Información Personal
                        </h6>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Carlos Martínez" required>
                                </div>
                                @error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Cédula / Identificación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-id-card"></i></span></div>
                                    <input type="text" name="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}" placeholder="Ej: 0801-1990-12345" required>
                                </div>
                                @error('cedula')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Teléfono</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" placeholder="Ej: +504 9999-9999">
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Historial Médico / Notas</label>
                                <textarea name="historial_medico" class="form-control @error('historial_medico') is-invalid @enderror" rows="2" placeholder="Alergias, condiciones, etc.">{{ old('historial_medico') }}</textarea>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: MEMBRESÍA --}}
                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="letter-spacing:.05em;font-size:.75rem;">
                            2. Membresía <span class="text-danger">*</span>
                        </h6>

                        {{-- Membresía seleccionada (display) --}}
                        <div id="membresiaSeleccionadaBox" class="d-none mb-3 p-3 border rounded" style="background:#EFF6FF;border-color:#2563EB !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="font-weight-bold text-primary" id="membresiaSelNombre">—</span>
                                    <small class="text-muted ml-2" id="membresiaSelDuracion"></small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="h5 mb-0 font-weight-bold text-primary mr-3" id="membresiaSelPrecio"></span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirModalMembresias()">
                                        <i class="fas fa-exchange-alt mr-1"></i> Cambiar
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="membresiaVaciaBox" class="{{ old('tipo_membresia_id') ? 'd-none' : '' }}">
                            <button type="button" class="btn btn-outline-primary btn-block py-3" onclick="abrirModalMembresias()">
                                <i class="fas fa-id-card mr-2"></i> Seleccionar Plan de Membresía
                            </button>
                            @error('tipo_membresia_id')
                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SECCIÓN 3: FOTO --}}
                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="letter-spacing:.05em;font-size:.75rem;">
                            3. Fotografía de Referencia (Reconocimiento Facial)
                        </h6>

                        <div class="form-group mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <div class="p-3 border rounded bg-light d-flex flex-column align-items-center justify-content-center" style="min-height:140px;">
                                        <img id="fotoPreview" src="#" alt="Preview" class="rounded-circle d-none mb-2" style="width:90px;height:90px;object-fit:cover;">
                                        <div id="fotoPlaceholder" class="text-muted text-center">
                                            <i class="fas fa-user-circle fa-3x mb-1 text-primary"></i>
                                            <small class="d-block">Sin foto</small>
                                        </div>
                                        <span id="webcamBadge" class="badge badge-success d-none mt-1"><i class="fas fa-camera mr-1"></i> Webcam</span>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-2">
                                        <div class="custom-file flex-grow-1 mb-2 mb-sm-0 mr-sm-2">
                                            <input type="file" name="foto" id="foto" class="custom-file-input @error('foto') is-invalid @enderror" accept="image/*" onchange="previewFoto(this)">
                                            <label class="custom-file-label" for="foto" data-browse="Buscar">Subir desde archivo...</label>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary font-weight-bold text-nowrap" onclick="openWebcamModal()">
                                            <i class="fas fa-camera mr-1"></i> Usar Cámara
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">JPG/PNG, máx 2MB. Se usará para reconocimiento facial en recepción.</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('user') }}" class="btn btn-outline-secondary px-4 py-2 font-weight-bold mr-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL MEMBRESÍAS --}}
<div class="modal fade" id="modalMembresias" tabindex="-1" role="dialog" aria-labelledby="modalMembresiasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background:#1E293B;">
                <h5 class="modal-title font-weight-bold text-white" id="modalMembresiasLabel">
                    <i class="fas fa-id-card mr-2 text-primary"></i> Seleccionar Plan de Membresía
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4" style="background:#F1F5F9;">
                <p class="text-muted small mb-4">Selecciona el plan que el cliente desea contratar. El inicio de la membresía será hoy.</p>
                <div class="row">
                    @foreach($tiposMembresia as $tipo)
                    <div class="col-md-6 mb-4">
                        <div class="membresia-card h-100" data-id="{{ $tipo->id }}" data-nombre="{{ $tipo->nombre }}" data-precio="{{ $gymConfig->simbolo_moneda }} {{ number_format($tipo->precio, 2) }}" data-duracion="{{ $tipo->duracion_dias }} días" onclick="seleccionarMembresia(this)" style="cursor:pointer;background:#fff;border-radius:12px;padding:1.5rem;border:2px solid #E2E8F0;transition:all .2s;">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="font-weight-bold mb-1" style="color:#1E293B;">{{ $tipo->nombre }}</h5>
                                    <span class="badge badge-pill" style="background:#EFF6FF;color:#2563EB;font-size:.75rem;">{{ $tipo->duracion_dias }} días</span>
                                </div>
                                <div class="text-right">
                                    <div class="h4 font-weight-bold mb-0" style="color:#2563EB;">{{ $gymConfig->simbolo_moneda }} {{ number_format($tipo->precio, 2) }}</div>
                                    <small class="text-muted">/ plan</small>
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
                <button type="button" class="btn btn-primary font-weight-bold px-4" id="btnConfirmarMembresia" disabled onclick="confirmarMembresia()">
                    <i class="fas fa-check mr-1"></i> Confirmar Selección
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL WEBCAM --}}
<div class="modal fade" id="modalWebcam" tabindex="-1" role="dialog" aria-labelledby="modalWebcamLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="modalWebcamLabel">
                    <i class="fas fa-camera mr-2 text-info"></i> Capturar Foto con Cámara
                </h5>
                <button type="button" class="close text-white" onclick="closeWebcamModal()"><span>&times;</span></button>
            </div>
            <div class="modal-body p-3 text-center bg-black">
                <video id="webcamVideo" autoplay playsinline style="width:100%;max-height:320px;border-radius:8px;background:#1a1a1a;"></video>
                <canvas id="webcamCanvas" class="d-none"></canvas>
            </div>
            <div class="modal-footer bg-light justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeWebcamModal()">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm px-4 font-weight-bold" onclick="takeSnapshot()">
                    <i class="fas fa-camera mr-1"></i> Capturar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Membresía selection ──
    var selectedMembresiaId = null;
    var selectedMembresiaCard = null;

    function abrirModalMembresias() {
        $('#modalMembresias').modal('show');
    }

    function seleccionarMembresia(el) {
        // Quitar selección previa
        document.querySelectorAll('.membresia-card').forEach(function(c) {
            c.style.borderColor = '#E2E8F0';
            c.style.background = '#fff';
            c.querySelector('.check-icon').classList.add('d-none');
        });
        // Marcar nuevo
        el.style.borderColor = '#2563EB';
        el.style.background = '#EFF6FF';
        el.querySelector('.check-icon').classList.remove('d-none');
        selectedMembresiaId = el.dataset.id;
        selectedMembresiaCard = el;
        document.getElementById('btnConfirmarMembresia').disabled = false;
    }

    function confirmarMembresia() {
        if (!selectedMembresiaId) return;
        var el = selectedMembresiaCard;
        document.getElementById('tipo_membresia_id').value = selectedMembresiaId;
        document.getElementById('membresiaSelNombre').textContent = el.dataset.nombre;
        document.getElementById('membresiaSelPrecio').textContent = el.dataset.precio;
        document.getElementById('membresiaSelDuracion').textContent = el.dataset.duracion;
        document.getElementById('membresiaSeleccionadaBox').classList.remove('d-none');
        document.getElementById('membresiaVaciaBox').classList.add('d-none');
        $('#modalMembresias').modal('hide');
    }

    // ── Webcam ──
    var webcamStream = null;

    function previewFoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('fotoPreview');
                var placeholder = document.getElementById('fotoPlaceholder');
                var badge = document.getElementById('webcamBadge');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
                if (badge) badge.classList.add('d-none');
                document.getElementById('foto_base64').value = '';
            };
            reader.readAsDataURL(input.files[0]);
            var label = input.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) label.textContent = input.files[0].name;
        }
    }

    function openWebcamModal() {
        $('#modalWebcam').modal('show');
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { width: 1280, height: 720, facingMode: 'user' } })
                .then(function(stream) {
                    webcamStream = stream;
                    document.getElementById('webcamVideo').srcObject = stream;
                })
                .catch(function() {
                    alert('No se pudo acceder a la cámara.');
                    closeWebcamModal();
                });
        } else {
            alert('Tu navegador no soporta la cámara.');
            closeWebcamModal();
        }
    }

    function closeWebcamModal() {
        if (webcamStream) { webcamStream.getTracks().forEach(function(t) { t.stop(); }); webcamStream = null; }
        $('#modalWebcam').modal('hide');
    }

    function takeSnapshot() {
        var video = document.getElementById('webcamVideo');
        var canvas = document.getElementById('webcamCanvas');
        if (video && video.videoWidth > 0) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            var dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('foto_base64').value = dataUrl;
            var preview = document.getElementById('fotoPreview');
            var placeholder = document.getElementById('fotoPlaceholder');
            var badge = document.getElementById('webcamBadge');
            preview.src = dataUrl;
            preview.classList.remove('d-none');
            if (placeholder) placeholder.classList.add('d-none');
            if (badge) badge.classList.remove('d-none');
            document.getElementById('foto').value = '';
            var fileLabel = document.querySelector('.custom-file-label');
            if (fileLabel) fileLabel.textContent = 'Subir desde archivo...';
            closeWebcamModal();
        }
    }

    // Restaurar membresía si viene de old() tras error de validación
    @if(old('tipo_membresia_id'))
        document.getElementById('tipo_membresia_id').value = '{{ old("tipo_membresia_id") }}';
        @foreach($tiposMembresia as $tipo)
            @if(old('tipo_membresia_id') == $tipo->id)
                document.getElementById('membresiaSelNombre').textContent = '{{ $tipo->nombre }}';
                document.getElementById('membresiaSelPrecio').textContent = '{{ $gymConfig->simbolo_moneda }} {{ number_format($tipo->precio, 2) }}';
                document.getElementById('membresiaSelDuracion').textContent = '{{ $tipo->duracion_dias }} días';
                document.getElementById('membresiaSeleccionadaBox').classList.remove('d-none');
                document.getElementById('membresiaVaciaBox').classList.add('d-none');
            @endif
        @endforeach
    @endif
</script>
@endpush
