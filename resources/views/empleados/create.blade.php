@extends('layouts.app')

@section('title', 'Nuevo Empleado / Recepcionista')

@section('skeleton')
    <div class="skel-box" style="height: 32px; width: 300px; margin-bottom: 1.5rem;"></div>
    <div class="skel-row">
        <div class="skel-box" style="height: 45px; flex: 1;"></div>
        <div class="skel-box" style="height: 45px; flex: 1;"></div>
    </div>
    <div class="skel-row">
        <div class="skel-box" style="height: 45px; flex: 1;"></div>
        <div class="skel-box" style="height: 45px; flex: 1;"></div>
    </div>
    <div class="skel-box" style="height: 45px; width: 180px; margin-top: 1rem;"></div>
@endsection

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
    .config-section-title i { color: var(--primary); }

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
                        <i class="fas fa-user-plus text-primary mr-2"></i> Registrar Nuevo Empleado
                    </h5>
                    <a href="{{ route('empleados') }}" class="btn btn-outline-secondary btn-sm font-weight-bold">
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

                {{-- Scrollable Form --}}
                <div class="config-scroll">
                    <form action="{{ route('empleados.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="foto_base64" id="foto_base64">
                        <input type="hidden" name="descriptor_facial" id="descriptor_facial">

                        {{-- SECCIÓN 1: DATOS PERSONALES --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-id-badge mr-2"></i> 1. Información Personal y Credenciales</div>
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="config-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Amanda Waller" required>
                                    </div>
                                    @error('nombre')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="config-label">Identificación <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-id-card"></i></span></div>
                                        <input type="text" name="cedula" id="cedula" class="form-control @error('cedula') is-invalid @enderror" value="{{ old('cedula') }}" placeholder="Ej: 0801-1992-45678" required>
                                    </div>
                                    @error('cedula')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 form-group mb-2">
                                    <label class="config-label">Usuario de Acceso <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user-tag"></i></span></div>
                                        <input type="text" name="usuario" class="form-control @error('usuario') is-invalid @enderror" value="{{ old('usuario') }}" placeholder="Ej: awaller" required>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group mb-2">
                                    <label class="config-label">Contraseña Inicial <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-key"></i></span></div>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 6 caracteres" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECCIÓN 2: ROL Y TURNO --}}
                        <div class="config-section">
                            <div class="config-section-title"><i class="fas fa-clock mr-2"></i> 2. Rol y Horario de Turno</div>
                            
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="config-label">Rol en el Sistema <span class="text-danger">*</span></label>
                                    <select name="rol" class="form-control form-control-sm @error('rol') is-invalid @enderror" required>
                                        <option value="empleado" {{ old('rol') === 'empleado' ? 'selected' : '' }}>Recepcionista / Empleado</option>
                                        <option value="admin" {{ old('rol') === 'admin' ? 'selected' : '' }}>Administrador General</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label class="config-label">Estado del Empleado <span class="text-danger">*</span></label>
                                    <select name="estado" class="form-control form-control-sm @error('estado') is-invalid @enderror" required>
                                        <option value="activo" {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ old('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group mb-0">
                                    <label class="config-label">Hora de Entrada (Turno)</label>
                                    <input type="time" name="hora_entrada_turno" class="form-control form-control-sm" value="{{ old('hora_entrada_turno', '08:00') }}">
                                </div>
                                <div class="col-md-4 form-group mb-0">
                                    <label class="config-label">Hora de Salida (Turno)</label>
                                    <input type="time" name="hora_salida_turno" class="form-control form-control-sm" value="{{ old('hora_salida_turno', '16:00') }}">
                                </div>
                                <div class="col-md-4 form-group mb-0">
                                    <label class="config-label">Tolerancia (Minutos)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="tolerancia_minutos" class="form-control" value="{{ old('tolerancia_minutos', 10) }}" min="0" max="120">
                                        <div class="input-group-append"><span class="input-group-text">min</span></div>
                                    </div>
                                </div>
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
                            <a href="{{ route('empleados') }}" class="btn btn-outline-secondary btn-sm px-4 font-weight-bold mr-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Guardar Empleado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL WEBCAM --}}
<div class="modal fade" id="modalWebcam" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white">
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
<script>
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
            document.getElementById('fotoPreview').src = dataUrl;
            document.getElementById('fotoPreview').classList.remove('d-none');
            document.getElementById('fotoPlaceholder').classList.add('d-none');
            document.getElementById('webcamBadge').classList.remove('d-none');
            document.getElementById('foto').value = '';
            document.querySelector('.custom-file-label').textContent = 'Subir desde archivo...';
            closeWebcamModal();
        }
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('cedula')) {
        new Cleave('#cedula', { delimiters: ['-', '-'], blocks: [4, 4, 5], numericOnly: true });
    }
});
</script>
@endpush