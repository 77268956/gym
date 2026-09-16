@extends('layouts.app')

@section('title', 'Nuevo Socio / Cliente')

@push('styles')
<style>
    body, html { overflow: hidden; height: 100%; }
    #page-wrapper main { 
        padding: 1rem 1.5rem !important; 
        display: flex; flex-direction: column; 
        height: calc(100vh - 60px); overflow: hidden;
    }
    .page-header { flex-shrink: 0; margin-bottom: 0.75rem !important; }
    .main-container { flex: 1; overflow: hidden; display: flex; flex-direction: column; padding: 0 !important; }

    .config-card {
        background: #fff; border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04); padding: 1.25rem;
        display: flex; flex-direction: column; height: 100%;
    }
    .config-scroll { flex: 1; min-height: 0; overflow-y: auto; padding-right: 8px; }

    .config-section {
        background: #F8FAFC; border: 1px solid #E2E8F0;
        border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem;
    }
    .config-section-title {
        font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
        color: #1E293B; margin-bottom: 1rem; letter-spacing: 0.05em;
    }
    .config-section-title i { color: #2563EB; }

    .config-label { font-weight: 600; font-size: 0.85rem; color: #334155; margin-bottom: 0.3rem; }
</style>
@endpush

@section('content')
<div class="container-fluid main-container">
    <div class="row h-100">
        <div class="col-lg-10 offset-lg-1 h-100 pb-1">
            <div class="config-card">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
                    <h5 class="mb-0 font-weight-bold text-dark" style="font-size:0.95rem;">
                        <i class="fas fa-user-plus text-primary mr-2"></i> Registrar Nuevo Socio
                    </h5>
                    <a href="{{ route('user') }}" class="btn btn-outline-secondary btn-sm font-weight-bold">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                </div>

                {{-- Errors/Success --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show p-2 mb-3" style="font-size:0.85rem;">
                        <strong>Error:</strong> Revisa los campos resaltados.
                        <button type="button" class="close p-2" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show p-2 mb-3" style="font-size:0.85rem;">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        <button type="button" class="close p-2" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                {{-- Scrollable Form --}}
                <div class="config-scroll">
                    <form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="foto_base64" id="foto_base64">
                        <input type="hidden" name="descriptor_facial" id="descriptor_facial">
                        <input type="hidden" name="tipo_membresia_id" id="tipo_membresia_id" value="{{ old('tipo_membresia_id') }}">

                        {{-- SECCIÓN 1: DATOS PERSONALES --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-address-card mr-2"></i> 1. Información Personal</div>
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="config-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Carlos Martínez" required>
                                    </div>
                                    @error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="config-label">Cédula / Identificación <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-id-card"></i></span></div>
                                        <input type="text" name="cedula" id="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}" placeholder="Ej: 0801-1990-12345" required>
                                    </div>
                                    @error('cedula')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 form-group mb-2">
                                    <label class="config-label">Teléfono</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-phone"></i></span></div>
                                        <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" placeholder="Ej: 9999-9999">
                                    </div>
                                </div>
                                <div class="col-md-6 form-group mb-2">
                                    <label class="config-label">Historial Médico / Notas</label>
                                    <textarea name="historial_medico" class="form-control form-control-sm @error('historial_medico') is-invalid @enderror" rows="1" placeholder="Alergias, lesiones, etc.">{{ old('historial_medico') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: MEMBRESÍA --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-dumbbell mr-2"></i> 2. Membresía <span class="text-danger">*</span></div>
                            
                            <div id="membresiaSeleccionadaBox" class="d-none p-3 border rounded mb-2" style="background:#EFF6FF;border-color:#2563EB !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="font-weight-bold text-primary" id="membresiaSelNombre">—</span>
                                        <small class="text-muted ml-2" id="membresiaSelDuracion"></small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="h6 mb-0 font-weight-bold text-primary mr-3" id="membresiaSelPrecio"></span>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="abrirModalMembresias()">
                                            <i class="fas fa-exchange-alt mr-1"></i> Cambiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="membresiaVaciaBox" class="{{ old('tipo_membresia_id') ? 'd-none' : '' }}">
                                <button type="button" class="btn btn-outline-primary btn-sm px-4 py-2" onclick="abrirModalMembresias()">
                                    <i class="fas fa-id-card mr-2"></i> Seleccionar Plan de Membresía
                                </button>
                                @error('tipo_membresia_id')
                                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SECCIÓN 3: FOTO --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-camera mr-2"></i> 3. Fotografía de Referencia</div>
                            
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-2 mb-md-0">
                                    <div class="p-3 border rounded bg-white d-flex flex-column align-items-center justify-content-center" style="min-height:120px;">
                                        <img id="fotoPreview" src="#" alt="Preview" class="rounded-circle d-none mb-2" style="width:80px;height:80px;object-fit:cover;">
                                        <div id="fotoPlaceholder" class="text-muted text-center">
                                            <i class="fas fa-user-circle fa-2x mb-1 text-primary"></i>
                                            <small class="d-block" style="font-size:0.7rem;">Sin foto</small>
                                        </div>
                                        <span id="webcamBadge" class="badge badge-success d-none mt-1" style="font-size:0.6rem;"><i class="fas fa-camera mr-1"></i> Webcam</span>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-1">
                                        <div class="custom-file custom-file-sm flex-grow-1 mb-2 mb-sm-0 mr-sm-2">
                                            <input type="file" name="foto" id="foto" class="custom-file-input @error('foto') is-invalid @enderror" accept="image/*" onchange="previewFoto(this)">
                                            <label class="custom-file-label" for="foto" style="font-size:0.85rem; height: calc(1.5em + 0.5rem + 2px); padding: 0.25rem 0.5rem;">Subir desde archivo...</label>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm font-weight-bold text-nowrap" onclick="openWebcamModal()">
                                            <i class="fas fa-camera mr-1"></i> Usar Cámara
                                        </button>
                                    </div>
                                    <small class="form-text text-muted" style="font-size:0.7rem;">JPG/PNG, máx 2MB. Usado para reconocimiento facial.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="d-flex justify-content-end mt-3 mb-2">
                            <a href="{{ route('user') }}" class="btn btn-outline-secondary btn-sm px-4 font-weight-bold mr-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold">
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
<div class="modal fade" id="modalMembresias" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1E293B, #0F172A);">
                <h6 class="modal-title font-weight-bold text-white"><i class="fas fa-id-card mr-2 text-primary"></i> Seleccionar Plan</h6>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-4" style="background:#F8FAFC;">
                <div class="row">
                    @foreach($tiposMembresia as $tipo)
                    <div class="col-md-6 mb-3">
                        <div class="membresia-card h-100" data-id="{{ $tipo->id }}" data-nombre="{{ $tipo->nombre }}" data-precio="{{ $gymConfig->simbolo_moneda }} {{ number_format($tipo->precio, 2) }}" data-duracion="{{ $tipo->duracion_dias }} días" onclick="seleccionarMembresia(this)" style="cursor:pointer;background:#fff;border-radius:8px;padding:1rem;border:2px solid #E2E8F0;transition:all .2s;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="font-weight-bold mb-1" style="color:#1E293B;">{{ $tipo->nombre }}</h6>
                                    <span class="badge badge-pill" style="background:#EFF6FF;color:#2563EB;font-size:.7rem;">{{ $tipo->duracion_dias }} días</span>
                                </div>
                                <div class="text-right">
                                    <div class="h5 font-weight-bold mb-0" style="color:#2563EB;">{{ $gymConfig->simbolo_moneda }} {{ number_format($tipo->precio, 2) }}</div>
                                </div>
                            </div>
                            <div class="mt-2 text-right check-icon d-none">
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Seleccionado</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-white py-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm font-weight-bold px-4" id="btnConfirmarMembresia" disabled onclick="confirmarMembresia()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL WEBCAM --}}
<div class="modal fade" id="modalWebcam" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1E293B, #0F172A);">
                <h6 class="modal-title font-weight-bold"><i class="fas fa-camera mr-2 text-info"></i> Capturar Foto</h6>
                <button type="button" class="close text-white" onclick="closeWebcamModal()"><span>&times;</span></button>
            </div>
            <div class="modal-body p-3 text-center bg-black">
                <video id="webcamVideo" autoplay playsinline style="width:100%;max-height:320px;border-radius:8px;background:#1a1a1a;"></video>
                <canvas id="webcamCanvas" class="d-none"></canvas>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm px-3" onclick="closeWebcamModal()">Cancelar</button>
                <button type="button" class="btn btn-success btn-sm px-4 font-weight-bold" onclick="takeSnapshot()"><i class="fas fa-camera mr-1"></i> Capturar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    var selectedMembresiaId = null;
    var selectedMembresiaCard = null;
    var faceApiModelsReady = Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri('{{ asset('models') }}'),
        faceapi.nets.faceLandmark68Net.loadFromUri('{{ asset('models') }}'),
        faceapi.nets.faceRecognitionNet.loadFromUri('{{ asset('models') }}')
    ]);

    async function setFaceDescriptor(dataUrl) {
        try {
            await faceApiModelsReady;
            var image = await faceapi.fetchImage(dataUrl);
            var detection = await faceapi.detectSingleFace(image).withFaceLandmarks().withFaceDescriptor();
            document.getElementById('descriptor_facial').value = detection ? JSON.stringify(Array.from(detection.descriptor)) : '';
        } catch (error) { document.getElementById('descriptor_facial').value = ''; }
    }

    function abrirModalMembresias() { $('#modalMembresias').modal('show'); }
    function seleccionarMembresia(el) {
        document.querySelectorAll('.membresia-card').forEach(function(c) {
            c.style.borderColor = '#E2E8F0'; c.style.background = '#fff';
            c.querySelector('.check-icon').classList.add('d-none');
        });
        el.style.borderColor = '#2563EB'; el.style.background = '#EFF6FF';
        el.querySelector('.check-icon').classList.remove('d-none');
        selectedMembresiaId = el.dataset.id; selectedMembresiaCard = el;
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

    var webcamStream = null;
    function previewFoto(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('fotoPreview').src = e.target.result;
                document.getElementById('fotoPreview').classList.remove('d-none');
                document.getElementById('fotoPlaceholder').classList.add('d-none');
                document.getElementById('webcamBadge').classList.add('d-none');
                document.getElementById('foto_base64').value = '';
                setFaceDescriptor(e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
            var lbl = input.nextElementSibling; if(lbl) lbl.textContent = input.files[0].name;
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
        var video = document.getElementById('webcamVideo'); var canvas = document.getElementById('webcamCanvas');
        if (video && video.videoWidth > 0) {
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            var dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('foto_base64').value = dataUrl;
            setFaceDescriptor(dataUrl);
            document.getElementById('fotoPreview').src = dataUrl;
            document.getElementById('fotoPreview').classList.remove('d-none');
            document.getElementById('fotoPlaceholder').classList.add('d-none');
            document.getElementById('webcamBadge').classList.remove('d-none');
            document.getElementById('foto').value = '';
            document.querySelector('.custom-file-label').textContent = 'Subir desde archivo...';
            closeWebcamModal();
        }
    }

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
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('telefono')) new Cleave('#telefono', { delimiters: ['-'], blocks: [4, 4], numericOnly: true });
    if (document.getElementById('cedula')) new Cleave('#cedula', { delimiters: ['-', '-'], blocks: [4, 4, 5], numericOnly: true });
});
</script>
@endpush