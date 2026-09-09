@extends('layouts.app')

@section('title', 'Editar Cliente - ' . $cliente->nombre)

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('user') }}"><i class="fas fa-users mr-1"></i> Socios / Clientes</a></li>
                    <li class="breadcrumb-item active">Editar: {{ $cliente->nombre }}</li>
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

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit mr-2"></i> Editar: {{ $cliente->nombre }}
                    </h5>
                    <a href="{{ route('user') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('clientes.update', $cliente) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Hidden webcam fields --}}
                        <input type="hidden" name="foto_base64" id="foto_base64">
                        <input type="hidden" name="descriptor_facial" id="descriptor_facial">

                        {{-- SECCIÓN 1: DATOS PERSONALES --}}
                        <h6 class="text-uppercase text-muted font-weight-bold mb-3 border-bottom pb-2" style="letter-spacing:.05em;font-size:.75rem;">
                            1. Información Personal
                        </h6>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Nombre Completo <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $cliente->nombre) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Cédula / Identificación <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-id-card"></i></span></div>
                                    <input type="text" name="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula', $cliente->cedula) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Teléfono</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}" placeholder="+504 9999-9999">
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold small text-dark">Historial Médico / Notas</label>
                                <textarea name="historial_medico" class="form-control" rows="2">{{ old('historial_medico', $cliente->historial_medico) }}</textarea>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: MEMBRESÍA ACTIVA (info, no editable aquí) --}}
                        @if($membresiaActiva)
                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="letter-spacing:.05em;font-size:.75rem;">
                            2. Membresía Activa
                        </h6>
                        <div class="p-3 border rounded mb-4" style="background:#F0FDF4;border-color:#10B981 !important;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="font-weight-bold" style="color:#065F46;">{{ $membresiaActiva->tipoMembresia->nombre ?? 'Sin tipo' }}</span>
                                    <span class="badge badge-success ml-2">Activa</span>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted d-block">Inicio: {{ \Carbon\Carbon::parse($membresiaActiva->fecha_inicio)->format('d/m/Y') }}</small>
                                    <small class="text-muted d-block">Vence: {{ \Carbon\Carbon::parse($membresiaActiva->fecha_vencimiento)->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- SECCIÓN 3: FOTO DE REFERENCIA --}}
                        <h6 class="text-uppercase text-muted font-weight-bold mt-4 mb-3 border-bottom pb-2" style="letter-spacing:.05em;font-size:.75rem;">
                            3. Fotografía de Referencia (Reconocimiento Facial)
                        </h6>

                        <div class="form-group mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <div class="p-3 border rounded bg-light d-flex flex-column align-items-center justify-content-center" style="min-height:140px;">
                                        @if($cliente->foto_referencia)
                                            <img id="fotoPreview" src="{{ asset('storage/' . $cliente->foto_referencia) }}" alt="{{ $cliente->nombre }}" class="rounded-circle mb-2" style="width:80px;height:80px;object-fit:cover;">
                                        @else
                                            <img id="fotoPreview" src="#" alt="Preview" class="rounded-circle d-none mb-2" style="width:80px;height:80px;object-fit:cover;">
                                            <div id="fotoPlaceholder" class="text-muted text-center">
                                                <i class="fas fa-user-circle fa-3x mb-1"></i>
                                                <small class="d-block">Sin foto</small>
                                            </div>
                                        @endif
                                        <span id="webcamBadge" class="badge badge-success d-none mt-1"><i class="fas fa-camera mr-1"></i> Webcam</span>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-2">
                                        <div class="custom-file flex-grow-1 mb-2 mb-sm-0 mr-sm-2">
                                            <input type="file" name="foto" id="foto" class="custom-file-input" accept="image/*" onchange="previewFoto(this)">
                                            <label class="custom-file-label" for="foto" data-browse="Buscar">Cambiar fotografía...</label>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary font-weight-bold text-nowrap" onclick="openWebcamModal()">
                                            <i class="fas fa-camera mr-1"></i> Usar Cámara
                                        </button>
                                    </div>
                                    <small class="form-text text-muted mb-2">JPG/PNG, máx 2MB. Para reconocimiento facial en recepción.</small>

                                    @if($cliente->descriptor_facial)
                                        <div class="mt-2">
                                            <span class="badge badge-info"><i class="fas fa-check-circle mr-1"></i> Descriptor Facial guardado</span>
                                        </div>
                                    @endif

                                    @if($cliente->foto_referencia)
                                        <div class="custom-control custom-checkbox mt-2">
                                            <input type="checkbox" class="custom-control-input" id="eliminar_foto" name="eliminar_foto" value="1">
                                            <label class="custom-control-label text-danger" for="eliminar_foto">
                                                <i class="fas fa-trash-alt mr-1"></i> Eliminar foto actual
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('user') }}" class="btn btn-outline-secondary px-4 py-2 font-weight-bold mr-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Actualizar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL WEBCAM --}}
<div class="modal fade" id="modalWebcam" tabindex="-1" role="dialog" aria-labelledby="modalWebcamLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-camera mr-2 text-info"></i> Capturar Foto con Cámara</h5>
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
                .then(function(stream) { webcamStream = stream; document.getElementById('webcamVideo').srcObject = stream; })
                .catch(function() { alert('No se pudo acceder a la cámara.'); closeWebcamModal(); });
        } else { alert('Tu navegador no soporta la cámara.'); closeWebcamModal(); }
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
            if (fileLabel) fileLabel.textContent = 'Cambiar fotografía...';
            closeWebcamModal();
        }
    }
</script>
@endpush
